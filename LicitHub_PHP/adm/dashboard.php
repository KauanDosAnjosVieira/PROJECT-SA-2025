<?php
// dashboard.php
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
    
    // Buscar estatísticas
    $clientes = $pdo->query("SELECT COUNT(*) as total FROM users WHERE user_type = 'customer'")->fetch()['total'];
    $planos = $pdo->query("SELECT COUNT(*) as total FROM subscriptions WHERE status = 'active'")->fetch()['total'];
    $contatos = $pdo->query("SELECT COUNT(*) as total FROM contacts")->fetch()['total'];
    
    // Calcular faturamento (exemplo simplificado)
    $faturamento = $pdo->query("SELECT SUM(amount) as total FROM payments WHERE status = 'paid'")->fetch()['total'];
    $faturamento = $faturamento ? number_format($faturamento, 2, ',', '.') : '0,00';
    
    // Últimos contatos
    $contatos_recentes = $pdo->query("SELECT * FROM contacts ORDER BY created_at DESC LIMIT 3")->fetchAll();
    
    // Dados para o gráfico (exemplo com dados fixos, pode ser substituído por consulta real)
    $meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
    $novos_clientes = [200, 250, 300, 400, 380, 450, 500, 470, 530, 600, 620, 700];
    $planos_fechados = [50, 60, 80, 90, 100, 110, 130, 140, 150, 170, 180, 0];

} catch (PDOException $e) {
    die("Erro ao conectar com o banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Painel de Licitações</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="../css/dashboard.css">
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
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row full-height">
      <div class="col-md-2 sidebar">
        <h4 class="px-3 mb-4">LicitHub</h4>
        <a href="dashboard.php" class="active"><i class="bi bi-speedometer2"></i> Painel</a>
        <a href="clientes.php"><i class="bi bi-people"></i> Clientes</a>
        <a href="planos.php"><i class="bi bi-card-list"></i> Planos</a>
        <a href="contatos.php"><i class="bi bi-envelope"></i> Contatos</a>
        <a href="config.php"><i class="bi bi-gear"></i> Configurações</a>
        <a href="../login/logout.php" class="mt-4"><i class="bi bi-box-arrow-left"></i> Sair</a>
      </div>

      <div class="col-md-10 content">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2>Painel Administrativo</h2>
          <div class="text-muted">Bem-vindo, <?php echo htmlspecialchars($_SESSION['user_name']); ?></div>
        </div>

        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card bg-light text-dark p-3">
              <h6>Clientes</h6>
              <h3><?php echo number_format($clientes, 0, ',', '.'); ?></h3>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-light text-dark p-3">
              <h6>Planos Ativos</h6>
              <h3><?php echo number_format($planos, 0, ',', '.'); ?></h3>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-light text-dark p-3">
              <h6>Contatos Recebidos</h6>
              <h3><?php echo number_format($contatos, 0, ',', '.'); ?></h3>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card bg-light text-dark p-3">
              <h6>Faturamento</h6>
              <h3>R$ <?php echo $faturamento; ?></h3>
            </div>
          </div>
        </div>

        <div class="card mb-4">
          <div class="card-body">
            <h5 class="card-title">Resumo Mensal</h5>
            <canvas id="graficoResumo"></canvas>
          </div>
        </div>

        <div class="card">
          <div class="card-body">
            <h5 class="card-title">Últimos Contatos</h5>
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Data</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($contatos_recentes as $contato): ?>
                <tr>
                  <td><?php echo htmlspecialchars($contato['name']); ?></td>
                  <td><?php echo htmlspecialchars($contato['email']); ?></td>
                  <td><?php echo date('d/m/Y', strtotime($contato['created_at'])); ?></td>
                  <td>
                    <span class="badge <?php 
                      echo $contato['status'] === 'respondido' ? 'bg-success' : 
                           ($contato['status'] === 'pendente' ? 'bg-warning text-dark' : 'bg-danger'); 
                    ?> status-badge">
                      <?php echo ucfirst($contato['status']); ?>
                    </span>
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

  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <script>
    const ctx = document.getElementById('graficoResumo');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?php echo json_encode($meses); ?>,
        datasets: [
          {
            label: 'Novos Clientes',
            data: <?php echo json_encode($novos_clientes); ?>,
            borderColor: '#007bff',
            backgroundColor: 'rgba(0,123,255,0.1)',
            tension: 0.4,
            fill: true
          },
          {
            label: 'Planos Fechados',
            data: <?php echo json_encode($planos_fechados); ?>,
            borderColor: '#28a745',
            backgroundColor: 'rgba(40,167,69,0.1)',
            tension: 0.4,
            fill: true
          }
        ]
      },
      options: {
        responsive: true,
        plugins: { legend: { position: 'bottom' } }
      }
    });
  </script>
</body>
</html>