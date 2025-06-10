<?php
// editar_cliente.php
require_once '../conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: /LicitHub_PHP/login/login.php");
    exit();
}

// Conexão com o banco de dados
$pdo = getConexao();

// Buscar dados do cliente a ser editado
$cliente_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$cliente = $pdo->query("
    SELECT u.*, s.id as subscription_id, s.plan_id, s.status 
    FROM users u
    LEFT JOIN subscriptions s ON u.id = s.user_id
    WHERE u.id = $cliente_id
")->fetch();

// Se não encontrou o cliente, redireciona
if (!$cliente) {
    $_SESSION['erro'] = "Cliente não encontrado!";
    header("Location: clientes.php");
    exit();
}

// Buscar planos disponíveis
$planos = $pdo->query("SELECT id, name FROM plans")->fetchAll();

// Processar formulário de edição
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_cliente'])) {
    $nome = trim($_POST['nome']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = trim($_POST['telefone']);
    $plano_id = intval($_POST['plano_id']);
    $status = $_POST['status'];
    $user_type = $_POST['user_type']; // Novo campo para tipo de usuário
    
    try {
        $pdo->beginTransaction();
        
        // Atualizar dados do usuário
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, phone = ?, user_type = ?, updated_at = NOW() WHERE id = ?");
        $stmt->execute([$nome, $email, $telefone, $user_type, $cliente_id]);
        
        // Atualizar assinatura (apenas para clientes)
        if ($user_type == 'customer') {
            if ($cliente['subscription_id']) {
                $stmt = $pdo->prepare("UPDATE subscriptions SET plan_id = ?, status = ? WHERE id = ?");
                $stmt->execute([$plano_id, $status, $cliente['subscription_id']]);
            } else {
                $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, starts_at, ends_at) 
                                      VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))");
                $stmt->execute([$cliente_id, $plano_id, $status]);
            }
        }
        
        $pdo->commit();
        $_SESSION['mensagem'] = "Usuário atualizado com sucesso!";
        header("Location: clientes.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao atualizar usuário: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Editar Usuário - LicitHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }
    .form-label {
      font-weight: 500;
    }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="card">
          <div class="card-header bg-primary text-white">
            <div class="d-flex justify-content-between align-items-center">
              <h4 class="mb-0">Editar Usuário</h4>
              <a href="clientes.php" class="btn btn-light btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
              </a>
            </div>
          </div>
          
          <div class="card-body">
            <?php if (isset($erro)): ?>
              <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            <?php endif; ?>
            
            <form method="POST">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="nome" class="form-label">Nome Completo</label>
                  <input type="text" class="form-control" id="nome" name="nome" 
                         value="<?php echo htmlspecialchars($cliente['name']); ?>" required>
                </div>
                <div class="col-md-6">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email" 
                         value="<?php echo htmlspecialchars($cliente['email']); ?>" required>
                </div>
              </div>
              
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="telefone" class="form-label">Telefone</label>
                  <input type="text" class="form-control" id="telefone" name="telefone" 
                         value="<?php echo htmlspecialchars($cliente['phone'] ?? ''); ?>">
                </div>
                <div class="col-md-6">
                  <label for="user_type" class="form-label">Tipo de Usuário</label>
                  <select class="form-select" id="user_type" name="user_type" required>
                    <option value="customer" <?php echo ($cliente['user_type'] == 'customer') ? 'selected' : ''; ?>>Cliente</option>
                    <option value="admin" <?php echo ($cliente['user_type'] == 'admin') ? 'selected' : ''; ?>>Administrador</option>
                  </select>
                </div>
              </div>
              
              <div id="plano_fields" style="<?php echo ($cliente['user_type'] == 'admin') ? 'display: none;' : ''; ?>">
                <div class="row mb-3">
                  <div class="col-md-6">
                    <label for="plano_id" class="form-label">Plano</label>
                    <select class="form-select" id="plano_id" name="plano_id">
                      <option value="">Selecione um plano</option>
                      <?php foreach ($planos as $plano): ?>
                      <option value="<?php echo $plano['id']; ?>" 
                          <?php echo ($plano['id'] == ($cliente['plan_id'] ?? 0)) ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($plano['name']); ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                      <option value="ativo" <?php echo (($cliente['status'] ?? '') == 'ativo' ? 'selected' : ''); ?>>Ativo</option>
                      <option value="pendente" <?php echo (($cliente['status'] ?? '') == 'pendente' ? 'selected' : ''); ?>>Pendente</option>
                      <option value="inativo" <?php echo (($cliente['status'] ?? '') == 'inativo' ? 'selected' : ''); ?>>Inativo</option>
                    </select>
                  </div>
                </div>
              </div>
              
              <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="clientes.php" class="btn btn-secondary me-md-2">
                  <i class="bi bi-x-circle"></i> Cancelar
                </a>
                <button type="submit" name="editar_cliente" class="btn btn-primary">
                  <i class="bi bi-check-circle"></i> Salvar Alterações
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Máscara para telefone
    document.getElementById('telefone').addEventListener('input', function(e) {
      var x = e.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
      e.target.value = !x[2] ? x[1] : '(' + x[1] + ') ' + x[2] + (x[3] ? '-' + x[3] : '');
    });
    
    // Mostrar/ocultar campos de plano conforme tipo de usuário
    document.getElementById('user_type').addEventListener('change', function() {
      const planoFields = document.getElementById('plano_fields');
      if (this.value === 'customer') {
        planoFields.style.display = 'block';
        document.getElementById('plano_id').setAttribute('required', '');
        document.getElementById('status').setAttribute('required', '');
      } else {
        planoFields.style.display = 'none';
        document.getElementById('plano_id').removeAttribute('required');
        document.getElementById('status').removeAttribute('required');
      }
    });
  </script>
</body>
</html>