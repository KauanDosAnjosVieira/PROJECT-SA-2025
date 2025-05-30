<?php
// clientes.php
require_once '../conexao.php';
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['user_id'])) {
    header("Location: /LicitHub_PHP/login/login.php");
    exit();
}

// Conexão com o banco de dados
$pdo = getConexao();

// Processar formulário de adição de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_cliente'])) {
    $nome = trim($_POST['nome']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = trim($_POST['telefone']);
    $plano_id = intval($_POST['plano_id']);
    $status = $_POST['status'];
    
    try {
        // Inserir novo cliente
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, user_type, created_at, updated_at) 
                              VALUES (?, ?, ?, 'customer', NOW(), NOW())");
        $stmt->execute([$nome, $email, $telefone]);
        $user_id = $pdo->lastInsertId();
        
        // Criar assinatura
        $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, starts_at, ends_at) 
                              VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))");
        $stmt->execute([$user_id, $plano_id, $status]);
        
        $_SESSION['mensagem'] = "Cliente adicionado com sucesso!";
        header("Location: clientes.php");
        exit();
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar cliente: " . $e->getMessage();
    }
}

// Processar exclusão de cliente
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    
    try {
        $pdo->beginTransaction();
        
        // Remover assinaturas primeiro
        $pdo->exec("DELETE FROM subscriptions WHERE user_id = $id");
        
        // Depois remover o usuário
        $pdo->exec("DELETE FROM users WHERE id = $id");
        
        $pdo->commit();
        $_SESSION['mensagem'] = "Cliente excluído com sucesso!";
        header("Location: clientes.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao excluir cliente: " . $e->getMessage();
    }
}

// Buscar clientes e planos
$clientes = $pdo->query("
    SELECT u.id, u.name, u.email, u.phone, p.name as plano, s.status 
    FROM users u
    LEFT JOIN subscriptions s ON u.id = s.user_id
    LEFT JOIN plans p ON s.plan_id = p.id
    WHERE u.user_type = 'customer'
    ORDER BY u.name
")->fetchAll();

$planos = $pdo->query("SELECT id, name FROM plans")->fetchAll();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Clientes - LicitHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .sidebar {
      background-color: #343a40;
      color: white;
      min-height: 100vh;
      padding: 20px 0;
    }
    .sidebar a {
      color: white;
      display: block;
      padding: 10px 15px;
      text-decoration: none;
      transition: all 0.3s;
    }
    .sidebar a:hover {
      background-color: #495057;
    }
    .sidebar a.active {
      background-color: #007bff;
    }
    .status-badge {
      padding: 5px 10px;
      border-radius: 20px;
      font-size: 0.8rem;
    }
    .content {
      padding: 20px;
      background-color: #f8f9fa;
    }
    .badge-ativo { background-color: #28a745; }
    .badge-pendente { background-color: #ffc107; color: #212529; }
    .badge-inativo { background-color: #dc3545; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar">
        <h4 class="px-3 mb-4">LicitHub</h4>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Painel</a>
        <a href="clientes.php" class="active"><i class="bi bi-people"></i> Clientes</a>
        <a href="planos.php"><i class="bi bi-card-list"></i> Planos</a>
        <a href="contatos.php"><i class="bi bi-envelope"></i> Contatos</a>
        <a href="config.php"><i class="bi bi-gear"></i> Configurações</a>
        <a href="../inicial/inicial.php" class="mt-4"><i class="bi bi-box-arrow-left"></i> Sair</a>
      </div>
      
      <div class="col-md-10 content">
        <?php if (isset($_SESSION['mensagem'])): ?>
          <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['mensagem']; unset($_SESSION['mensagem']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        
        <?php if (isset($erro)): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $erro; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        <?php endif; ?>
        
        <div class="row mb-4">
          <div class="col-md-6">
            <h2>Clientes</h2>
          </div>
          <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarCliente">
              <i class="bi bi-plus-circle"></i> Novo Cliente
            </button>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Plano</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($clientes as $cliente): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($cliente['name']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['email']); ?></td>
                    <td><?php echo htmlspecialchars($cliente['phone'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($cliente['plano'] ?? 'Nenhum'); ?></td>
                    <td>
                      <?php 
                        $statusClass = strtolower($cliente['status']) === 'ativo' ? 'badge-ativo' : 
                                      (strtolower($cliente['status']) === 'pendente' ? 'badge-pendente' : 'badge-inativo');
                      ?>
                      <span class="badge <?php echo $statusClass; ?>">
                        <?php echo htmlspecialchars($cliente['status'] ?? 'N/A'); ?>
                      </span>
                    </td>
                    <td>
                      <a href="editar_cliente.php?id=<?php echo $cliente['id']; ?>" class="btn btn-sm btn-warning">
                        <i class="bi bi-pencil"></i> Editar
                      </a>
                      <a href="clientes.php?excluir=<?php echo $cliente['id']; ?>" 
                         class="btn btn-sm btn-danger" 
                         onclick="return confirm('Tem certeza que deseja excluir este cliente?')">
                        <i class="bi bi-trash"></i> Excluir
                      </a>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Modal Adicionar Cliente -->
        <div class="modal fade" id="modalAdicionarCliente" tabindex="-1" aria-labelledby="modalAdicionarClienteLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalAdicionarClienteLabel">Novo Cliente</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <form method="POST">
                <div class="modal-body">
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="nome" class="form-label">Nome Completo</label>
                      <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="telefone" class="form-label">Telefone</label>
                      <input type="text" class="form-control" id="telefone" name="telefone">
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="plano_id" class="form-label">Plano</label>
                      <select class="form-select" id="plano_id" name="plano_id" required>
                        <option value="">Selecione um plano</option>
                        <?php foreach ($planos as $plano): ?>
                        <option value="<?php echo $plano['id']; ?>"><?php echo htmlspecialchars($plano['name']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" id="status" name="status" required>
                        <option value="ativo">Ativo</option>
                        <option value="pendente">Pendente</option>
                        <option value="inativo">Inativo</option>
                      </select>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" name="adicionar_cliente" class="btn btn-primary">Salvar</button>
                </div>
              </form>
            </div>
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
  </script>
</body>
</html>