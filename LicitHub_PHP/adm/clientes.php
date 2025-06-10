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

// Filtros
$search = $_GET['search'] ?? '';
$user_type = $_GET['user_type'] ?? 'customer';

// Processar formulário de adição de cliente
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_cliente'])) {
    $nome = trim($_POST['nome']);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $telefone = trim($_POST['telefone']);
    $plano_id = intval($_POST['plano_id']);
    $status = $_POST['status'];
    $tipo_usuario = $_POST['tipo_usuario'];
    
    try {
        // Inserir novo usuário
        $stmt = $pdo->prepare("INSERT INTO users (name, email, phone, user_type, created_at, updated_at) 
                              VALUES (?, ?, ?, ?, NOW(), NOW())");
        $stmt->execute([$nome, $email, $telefone, $tipo_usuario]);
        $user_id = $pdo->lastInsertId();
        
        // Se for customer, criar assinatura
        if ($tipo_usuario === 'customer') {
            $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_id, status, starts_at, ends_at) 
                                  VALUES (?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH))");
            $stmt->execute([$user_id, $plano_id, $status]);
        }
        
        $_SESSION['mensagem'] = "Usuário adicionado com sucesso!";
        header("Location: clientes.php");
        exit();
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar usuário: " . $e->getMessage();
    }
}

// Processar exclusão de cliente
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    
    try {
        $pdo->beginTransaction();
        
        // Remover assinaturas primeiro (se existirem)
        $pdo->exec("DELETE FROM subscriptions WHERE user_id = $id");
        
        // Depois remover o usuário
        $pdo->exec("DELETE FROM users WHERE id = $id");
        
        $pdo->commit();
        $_SESSION['mensagem'] = "Usuário excluído com sucesso!";
        header("Location: clientes.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao excluir usuário: " . $e->getMessage();
    }
}

// Construir query base
$query = "
    SELECT u.id, u.name, u.email, u.phone, u.user_type, p.name as plano, s.status 
    FROM users u
    LEFT JOIN subscriptions s ON u.id = s.user_id
    LEFT JOIN plans p ON s.plan_id = p.id
    WHERE 1=1
";

$params = [];

// Adicionar filtros
if (!empty($user_type)) {
    $query .= " AND u.user_type = ?";
    $params[] = $user_type;
}

if (!empty($search)) {
    $query .= " AND (u.name LIKE ? OR u.email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$query .= " ORDER BY u.name";

// Preparar e executar a query
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$clientes = $stmt->fetchAll();

$planos = $pdo->query("SELECT id, name FROM plans")->fetchAll();

// Contagem por tipo de usuário para os badges
$total_usuarios = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$total_admins = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'admin'")->fetchColumn();
$total_clientes = $pdo->query("SELECT COUNT(*) FROM users WHERE user_type = 'customer'")->fetchColumn();
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
    .badge-admin { background-color: #6610f2; }
    .badge-customer { background-color: #20c997; }
    .badge-filter {
      cursor: pointer;
      margin-right: 5px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar">
        <h4 class="px-3 mb-4">LicitHub</h4>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Painel</a>
        <a href="clientes.php" class="active"><i class="bi bi-people"></i> Usuários</a>
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
            <h2>Gerenciamento de Usuários</h2>
          </div>
          <div class="col-md-6 text-end">
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalAdicionarCliente">
              <i class="bi bi-plus-circle"></i> Novo Usuário
            </button>
          </div>
        </div>

        <!-- Filtros e Estatísticas -->
        <div class="card mb-4">
          <div class="card-body">
            <div class="row">
              <div class="col-md-8">
                <form method="get" class="row g-3">
                  <div class="col-md-5">
                    <input type="text" name="search" class="form-control" placeholder="Buscar por nome ou email..." value="<?php echo htmlspecialchars($search); ?>">
                  </div>
                  <div class="col-md-4">
                    <select name="user_type" class="form-select">
                      <option value="">Todos os tipos</option>
                      <option value="admin" <?php echo $user_type === 'admin' ? 'selected' : ''; ?>>Administradores</option>
                      <option value="customer" <?php echo $user_type === 'customer' ? 'selected' : ''; ?>>Clientes</option>
                    </select>
                  </div>
                <div class="col-md-3">
                <div class="d-flex gap-2">
                 <button type="submit" class="btn btn-primary w-50">Filtrar</button>
                 <a href="clientes.php" class="btn btn-outline-secondary w-50">Limpar</a>
               </div>
                </div>
                </form>
              </div>
              <div class="col-md-4">
                <div class="d-flex justify-content-end">
                  <span class="badge bg-secondary me-2">Total: <?php echo $total_usuarios; ?></span>
                  <span class="badge bg-primary me-2">Admins: <?php echo $total_admins; ?></span>
                  <span class="badge bg-success">Clientes: <?php echo $total_clientes; ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabela de Usuários -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Telefone</th>
                    <th>Tipo</th>
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
                    <td>
                      <span class="badge <?php echo $cliente['user_type'] === 'admin' ? 'badge-admin' : 'badge-customer'; ?>">
                        <?php echo $cliente['user_type'] === 'admin' ? 'Administrador' : 'Cliente'; ?>
                      </span>
                    </td>
                    <td><?php echo htmlspecialchars($cliente['plano'] ?? 'N/A'); ?></td>
                    <td>
                      <?php 
                        $statusClass = strtolower($cliente['status'] ?? '') === 'ativo' 
                            ? 'badge-ativo' 
                            : (strtolower($cliente['status'] ?? '') === 'pendente' 
                                ? 'badge-pendente' 
                                : 'badge-inativo');
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
                         onclick="return confirm('Tem certeza que deseja excluir este usuário?')">
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
                <h5 class="modal-title" id="modalAdicionarClienteLabel">Novo Usuário</h5>
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
                      <label for="tipo_usuario" class="form-label">Tipo de Usuário</label>
                      <select class="form-select" id="tipo_usuario" name="tipo_usuario" required>
                        <option value="customer">Cliente</option>
                        <option value="admin">Administrador</option>
                      </select>
                    </div>
                  </div>
                  <div class="row" id="plano-section">
                    <div class="col-md-6 mb-3">
                      <label for="plano_id" class="form-label">Plano</label>
                      <select class="form-select" id="plano_id" name="plano_id">
                        <option value="">Selecione um plano</option>
                        <?php foreach ($planos as $plano): ?>
                        <option value="<?php echo $plano['id']; ?>"><?php echo htmlspecialchars($plano['name']); ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" id="status" name="status">
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

    // Mostrar/ocultar seção de plano conforme tipo de usuário
    document.getElementById('tipo_usuario').addEventListener('change', function() {
      const planoSection = document.getElementById('plano-section');
      if (this.value === 'admin') {
        planoSection.style.display = 'none';
        document.getElementById('plano_id').required = false;
        document.getElementById('status').required = false;
      } else {
        planoSection.style.display = 'flex';
        document.getElementById('plano_id').required = true;
        document.getElementById('status').required = true;
      }
    });

    // Inicializar visibilidade da seção de plano
    document.addEventListener('DOMContentLoaded', function() {
      const tipoUsuario = document.getElementById('tipo_usuario');
      if (tipoUsuario.value === 'admin') {
        document.getElementById('plano-section').style.display = 'none';
      }
    });
  </script>
</body>
</html>