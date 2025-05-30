<?php
// contatos.php
require_once '../conexao.php';
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /LicitHub_PHP/login/login.php");
    exit();
}

// Conexão com o banco de dados
try {
    $pdo = getConexao();
    
    // Filtros
    $status = $_GET['status'] ?? '';
    $search = $_GET['search'] ?? '';
    
    // Construir a query base
    $query = "SELECT * FROM contacts WHERE 1=1";
    $params = [];
    
    // Adicionar filtros
    if (!empty($status)) {
        $query .= " AND status = ?";
        $params[] = $status;
    }
    
    if (!empty($search)) {
        $query .= " AND (name LIKE ? OR email LIKE ?)";
        $params[] = "%$search%";
        $params[] = "%$search%";
    }
    
    // Ordenação
    $query .= " ORDER BY created_at DESC";
    
    // Preparar e executar a query
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $contatos = $stmt->fetchAll();
    
    // Contagem por status para os badges
    $total_contatos = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
    $pendentes = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'pendente'")->fetchColumn();
    $respondidos = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'respondido'")->fetchColumn();
    $cancelados = $pdo->query("SELECT COUNT(*) FROM contacts WHERE status = 'cancelado'")->fetchColumn();

} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contatos - Painel Administrativo</title>
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
    .badge-filter {
      cursor: pointer;
      margin-right: 5px;
    }
    .message-preview {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      max-width: 300px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row full-height">
      <div class="col-md-2 sidebar">
        <h4 class="px-3 mb-4">LicitHub</h4>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Painel</a>
        <a href="clientes.php"><i class="bi bi-people"></i> Clientes</a>
        <a href="planos.php"><i class="bi bi-card-list"></i> Planos</a>
        <a href="contatos.php" class="active"><i class="bi bi-envelope"></i> Contatos</a>
        <a href="config.php"><i class="bi bi-gear"></i> Configurações</a>
        <a href="../inicial/inicial.php" class="mt-4"><i class="bi bi-box-arrow-left"></i> Sair</a>
      </div>

      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Gerenciamento de Contatos</h2>
          <div class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
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
                    <select name="status" class="form-select">
                      <option value="">Todos os status</option>
                      <option value="pendente" <?php echo $status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                      <option value="respondido" <?php echo $status === 'respondido' ? 'selected' : ''; ?>>Respondido</option>
                      <option value="cancelado" <?php echo $status === 'cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                    </select>
                  </div>
                  <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                  </div>
                </form>
              </div>
              <div class="col-md-4">
                <div class="d-flex justify-content-end">
                  <span class="badge bg-secondary me-2">Total: <?php echo $total_contatos; ?></span>
                  <span class="badge bg-warning text-dark me-2">Pendentes: <?php echo $pendentes; ?></span>
                  <span class="badge bg-success me-2">Respondidos: <?php echo $respondidos; ?></span>
                  <span class="badge bg-danger">Cancelados: <?php echo $cancelados; ?></span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tabela de Contatos -->
        <div class="card">
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-hover">
                <thead>
                  <tr>
                    <th>Nome</th>
                    <th>Email</th>
                    <th>Mensagem</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($contatos as $contato): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($contato['name']); ?></td>
                    <td><?php echo htmlspecialchars($contato['email']); ?></td>
                    <td class="message-preview" title="<?php echo htmlspecialchars($contato['message']); ?>">
                      <?php echo htmlspecialchars(substr($contato['message'], 0, 50)); ?>...
                    </td>
                    <td><?php echo date('d/m/Y H:i', strtotime($contato['created_at'])); ?></td>
                    <td>
                      <span class="badge <?php 
                        echo $contato['status'] === 'respondido' ? 'bg-success' : 
                             ($contato['status'] === 'pendente' ? 'bg-warning text-dark' : 'bg-danger'); 
                      ?> status-badge">
                        <?php echo ucfirst($contato['status']); ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-outline-primary view-message" data-id="<?php echo $contato['id']; ?>">
                        <i class="bi bi-eye"></i>
                      </button>
                      <?php if ($contato['status'] === 'pendente'): ?>
                        <button class="btn btn-sm btn-outline-success mark-responded" data-id="<?php echo $contato['id']; ?>">
                          <i class="bi bi-check"></i>
                        </button>
                      <?php endif; ?>
                      <button class="btn btn-sm btn-outline-danger delete-contact" data-id="<?php echo $contato['id']; ?>">
                        <i class="bi bi-trash"></i>
                      </button>
                    </td>
                  </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal para visualizar mensagem -->
  <div class="modal fade" id="messageModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Detalhes da Mensagem</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <strong>Nome:</strong> <span id="modal-name"></span>
          </div>
          <div class="mb-3">
            <strong>Email:</strong> <span id="modal-email"></span>
          </div>
          <div class="mb-3">
            <strong>Data:</strong> <span id="modal-date"></span>
          </div>
          <div class="mb-3">
            <strong>Status:</strong> <span id="modal-status" class="badge"></span>
          </div>
          <div>
            <strong>Mensagem:</strong>
            <p id="modal-message" class="mt-2 p-3 bg-light rounded"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-success" id="markAsRespondedBtn">Marcar como Respondido</button>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Mostrar modal com detalhes da mensagem
    document.querySelectorAll('.view-message').forEach(button => {
      button.addEventListener('click', function() {
        const row = this.closest('tr');
        const name = row.cells[0].textContent;
        const email = row.cells[1].textContent;
        const message = row.cells[2].getAttribute('title');
        const date = row.cells[3].textContent;
        const status = row.cells[4].textContent.trim();
        const statusClass = row.cells[4].querySelector('.badge').className;
        const contactId = this.getAttribute('data-id');
        
        document.getElementById('modal-name').textContent = name;
        document.getElementById('modal-email').textContent = email;
        document.getElementById('modal-message').textContent = message;
        document.getElementById('modal-date').textContent = date;
        document.getElementById('modal-status').textContent = status;
        document.getElementById('modal-status').className = 'badge ' + statusClass;
        
        // Configurar botão de marcar como respondido
        const markBtn = document.getElementById('markAsRespondedBtn');
        markBtn.setAttribute('data-id', contactId);
        markBtn.style.display = status === 'pendente' ? 'block' : 'none';
        
        const modal = new bootstrap.Modal(document.getElementById('messageModal'));
        modal.show();
      });
    });

    // Marcar como respondido
    document.getElementById('markAsRespondedBtn').addEventListener('click', function() {
      const contactId = this.getAttribute('data-id');
      updateContactStatus(contactId, 'respondido');
    });

    // Botões de marcar como respondido na tabela
    document.querySelectorAll('.mark-responded').forEach(button => {
      button.addEventListener('click', function() {
        const contactId = this.getAttribute('data-id');
        updateContactStatus(contactId, 'respondido');
      });
    });

    // Botões de deletar
    document.querySelectorAll('.delete-contact').forEach(button => {
      button.addEventListener('click', function() {
        if (confirm('Tem certeza que deseja excluir este contato?')) {
          const contactId = this.getAttribute('data-id');
          updateContactStatus(contactId, 'cancelado');
        }
      });
    });

    // Função para atualizar status do contato
    function updateContactStatus(id, status) {
      fetch('update_contact.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `id=${id}&status=${status}`
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert('Status atualizado com sucesso!');
          location.reload();
        } else {
          alert('Erro ao atualizar status: ' + data.message);
        }
      })
      .catch(error => {
        console.error('Error:', error);
        alert('Erro ao processar requisição');
      });
    }
  </script>
</body>
</html>