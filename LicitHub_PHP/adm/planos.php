<?php
// planos.php
require_once '../conexao.php';
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: /LicitHub_PHP/login/login.php");
    exit();
}

// Conexão com o banco de dados
$pdo = getConexao();

// Processar formulário de plano
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $preco = (float) str_replace(['R$', '.', ','], ['', '', '.'], $_POST['preco']);
    $intervalo = $_POST['intervalo'] === 'yearly' ? 'yearly' : 'monthly';
    $status = $_POST['status'] === 'ativo' ? 1 : 0;
    
    try {
        $slug = strtolower(preg_replace('/[^a-z0-9]+/i', '-', $nome));
        
        $stmt = $pdo->prepare("INSERT INTO plans 
            (name, slug, price, interval, is_active, created_at, updated_at) 
            VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
        
        $stmt->execute([$nome, $slug, $preco, $intervalo, $status]);
        
        $_SESSION['mensagem'] = "Plano adicionado com sucesso!";
        header("Location: planos.php");
        exit();
        
    } catch (PDOException $e) {
        $erro = "Erro ao adicionar plano: " . $e->getMessage();
        error_log("Erro no plano: " . $e->getMessage());
    }
}

// Processar exclusão de plano
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    
    try {
        $pdo->beginTransaction();
        
        // Verificar se há assinaturas ativas
        $assinaturas = $pdo->query("SELECT COUNT(*) FROM subscriptions WHERE plan_id = $id AND status = 'active'")->fetchColumn();
        
        if ($assinaturas > 0) {
            $_SESSION['erro'] = "Não é possível excluir um plano com assinaturas ativas!";
        } else {
            $pdo->exec("DELETE FROM plans WHERE id = $id");
            $_SESSION['mensagem'] = "Plano excluído com sucesso!";
        }
        
        $pdo->commit();
        header("Location: planos.php");
        exit();
    } catch (PDOException $e) {
        $pdo->rollBack();
        $erro = "Erro ao excluir plano: " . $e->getMessage();
    }
}

// Buscar planos
$planos = $pdo->query("
    SELECT p.*, COUNT(s.id) as total_clientes
    FROM plans p
    LEFT JOIN subscriptions s ON p.id = s.plan_id
    GROUP BY p.id
    ORDER BY p.price
")->fetchAll();

// Estatísticas
$stats = $pdo->query("
    SELECT 
        COUNT(*) as total_planos,
        SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as planos_ativos,
        SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as planos_inativos,
        (SELECT COUNT(*) FROM subscriptions WHERE status = 'canceled') as cancelamentos,
        (SELECT SUM(amount) FROM payments WHERE status = 'paid') as faturamento
    FROM plans
")->fetch();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Planos - LicitHub</title>
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
    .badge-inativo { background-color: #dc3545; }
    .badge-cancelado { background-color: #6c757d; }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-2 sidebar">
        <h4 class="px-3 mb-4">LicitHub</h4>
        <a href="dashboard.php"><i class="bi bi-speedometer2"></i> Painel</a>
        <a href="clientes.php"><i class="bi bi-people"></i> Clientes</a>
        <a href="planos.php" class="active"><i class="bi bi-card-list"></i> Planos</a>
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
        
        <?php if (isset($_SESSION['erro'])): ?>
          <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?>
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
          <div class="col-md-4">
            <div class="card bg-light text-dark p-3">
              <h6>Planos Ativos</h6>
              <h3><?php echo $stats['planos_ativos']; ?></h3>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light text-dark p-3">
              <h6>Cancelamentos</h6>
              <h3><?php echo $stats['cancelamentos']; ?></h3>
            </div>
          </div>
          <div class="col-md-4">
            <div class="card bg-light text-dark p-3">
              <h6>Faturamento</h6>
              <h3>R$ <?php echo number_format($stats['faturamento'] ?? 0, 2, ',', '.'); ?></h3>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Distribuição de Planos</h5>
            <canvas id="graficoPlanos"></canvas>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h5 class="card-title mb-0">Detalhes dos Planos</h5>
              <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPlano">
                <i class="bi bi-plus-circle"></i> Novo Plano
              </button>
            </div>
            
            <div class="table-responsive">
              <table class="table table-bordered table-hover">
                <thead class="table-dark">
                  <tr>
                    <th>Plano</th>
                    <th>Clientes</th>
                    <th>Preço</th>
                    <th>Intervalo</th>
                    <th>Status</th>
                    <th>Ações</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($planos as $plano): ?>
                  <tr>
                    <td><?php echo htmlspecialchars($plano['name']); ?></td>
                    <td><?php echo $plano['total_clientes']; ?></td>
                    <td>R$ <?php echo number_format($plano['price'], 2, ',', '.'); ?></td>
                    <td><?php echo $plano['interval'] === 'yearly' ? 'Anual' : 'Mensal'; ?></td>
                    <td>
                      <span class="badge <?php echo $plano['is_active'] ? 'badge-ativo' : 'badge-inativo'; ?>">
                        <?php echo $plano['is_active'] ? 'Ativo' : 'Inativo'; ?>
                      </span>
                    </td>
                    <td>
                      <button class="btn btn-sm btn-warning" 
                              onclick="editarPlano(
                                <?php echo $plano['id']; ?>,
                                '<?php echo htmlspecialchars($plano['name'], ENT_QUOTES); ?>',
                                <?php echo $plano['price']; ?>,
                                '<?php echo htmlspecialchars($plano['description'], ENT_QUOTES); ?>',
                                '<?php echo $plano['interval']; ?>',
                                <?php echo $plano['is_active']; ?>,
                                `<?php echo str_replace("\n", "\\n", implode("\n", json_decode($plano['features']))); ?>`
                              )">
                        <i class="bi bi-pencil"></i> Editar
                      </button>
                      <a href="planos.php?excluir=<?php echo $plano['id']; ?>" 
                         class="btn btn-sm btn-danger"
                         onclick="return confirm('Tem certeza que deseja excluir este plano?')">
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

        <!-- Modal Plano -->
        <div class="modal fade" id="modalPlano" tabindex="-1" aria-labelledby="modalPlanoLabel" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalPlanoLabel">Novo Plano</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
              </div>
              <form method="POST">
                <input type="hidden" id="planoId" name="id">
                <div class="modal-body">
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="nome" class="form-label">Nome do Plano</label>
                      <input type="text" class="form-control" id="nome" name="nome" required>
                    </div>
                    <div class="col-md-6">
                      <label for="preco" class="form-label">Preço (R$)</label>
                      <input type="text" class="form-control" id="preco" name="preco" required>
                    </div>
                  </div>
                  <div class="row mb-3">
                    <div class="col-md-6">
                      <label for="intervalo" class="form-label">Intervalo</label>
                      <select class="form-select" id="intervalo" name="intervalo" required>
                        <option value="monthly">Mensal</option>
                        <option value="yearly">Anual</option>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="status" class="form-label">Status</label>
                      <select class="form-select" id="status" name="status" required>
                        <option value="ativo">Ativo</option>
                        <option value="inativo">Inativo</option>
                      </select>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="descricao" class="form-label">Descrição</label>
                    <textarea class="form-control" id="descricao" name="descricao" rows="2"></textarea>
                  </div>
                  <div class="mb-3">
                    <label for="recursos" class="form-label">Recursos (um por linha)</label>
                    <textarea class="form-control" id="recursos" name="recursos" rows="4"></textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  <button type="submit" class="btn btn-primary">Salvar Plano</button>
                </div>
              </form>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    // Gráfico de distribuição de planos
    const ctx = document.getElementById('graficoPlanos');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: [<?php echo implode(',', array_map(function($p) { return "'" . addslashes($p['name']) . "'"; }, $planos)); ?>],
        datasets: [{
          label: 'Clientes por Plano',
          data: [<?php echo implode(',', array_column($planos, 'total_clientes')); ?>],
          backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6c757d', '#17a2b8']
        }]
      },
      options: {
        responsive: true,
        plugins: { legend: { display: false } }
      }
    });

    // Máscara para preço
    document.getElementById('preco').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = (value / 100).toLocaleString('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
      e.target.value = value.replace('R$', '').trim();
    });

    // Função para editar plano
    function editarPlano(id, nome, preco, descricao, intervalo, status, recursos) {
      const modal = new bootstrap.Modal(document.getElementById('modalPlano'));
      document.getElementById('planoId').value = id;
      document.getElementById('nome').value = nome;
      document.getElementById('preco').value = preco.toLocaleString('pt-BR', {minimumFractionDigits: 2});
      document.getElementById('descricao').value = descricao;
      document.getElementById('intervalo').value = intervalo;
      document.getElementById('status').value = status ? 'ativo' : 'inativo';
      document.getElementById('recursos').value = recursos;
      document.getElementById('modalPlanoLabel').textContent = 'Editar Plano';
      modal.show();
    }

    // Resetar modal ao fechar
    document.getElementById('modalPlano').addEventListener('hidden.bs.modal', function() {
      document.getElementById('planoId').value = '';
      document.getElementById('nome').value = '';
      document.getElementById('preco').value = '';
      document.getElementById('descricao').value = '';
      document.getElementById('recursos').value = '';
      document.getElementById('modalPlanoLabel').textContent = 'Novo Plano';
    });
  </script>
</body>
</html>