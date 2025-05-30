<?php
// Conexão com o banco de dados
$host = 'localhost';
$dbname = 'licithub';
$username = 'root'; // Altere para seu usuário do MySQL
$password = ''; // Altere para sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

// Processar o formulário quando enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $cpf = $_POST['cpf'] ?? '';
    $email = $_POST['email'] ?? '';
    $endereco = $_POST['endereco'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $assunto = $_POST['assunto'] ?? '';
    $mensagem = $_POST['mensagem'] ?? '';
    
    // Validação básica
    if (!empty($nome) && !empty($email) && !empty($mensagem)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO contacts (name, email, message, status, created_at) 
                                  VALUES (:nome, :email, :mensagem, 'pendente', NOW())");
            
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            
            // Criar mensagem completa com todos os dados
            $mensagemCompleta = "CPF: $cpf\n";
            $mensagemCompleta .= "Endereço: $endereco, $numero\n";
            $mensagemCompleta .= "Assunto: $assunto\n";
            $mensagemCompleta .= "Mensagem:\n$mensagem";
            
            $stmt->bindParam(':mensagem', $mensagemCompleta);
            
            if ($stmt->execute()) {
                $success = "Mensagem enviada com sucesso! Entraremos em contato em breve.";
            } else {
                $error = "Erro ao enviar mensagem. Por favor, tente novamente.";
            }
        } catch (PDOException $e) {
            $error = "Erro no banco de dados: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, preencha todos os campos obrigatórios.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Contato | LicitHub</title>

  <!-- Bootstrap e Font Awesome -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Estilos personalizados -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

  <!-- Header -->
  <header class="bg-primary py-4">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../inicial/inicial.php" class="logo-link h4 m-0">LicitHub</a>
    </div>
    <img class="logo" src="img/logo.png" >
  </header>
    
  <!-- Seção de Contato -->
  <main class="py-5">
    <div class="container position-relative">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card p-4 shadow">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <h2 class="text-primary">Entre em Contato</h2>
              <button class="btn btn-sm btn-outline-secondary" onclick="toggleContatoLateral()">
                <i class="fas fa-eye-slash me-1"></i> Info
              </button>
            </div>
            
            <?php if (isset($success)): ?>
              <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php elseif (isset($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form id="form-contato" method="POST" action="">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" required>
              </div>
              <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control" id="cpf" name="cpf" required pattern="\d{3}\.\d{3}\.\d{3}-\d{2}" placeholder="CPF (ex: 123.456.789-00)" maxlength="14">
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
              </div>
              <div class="mb-3">
                <label for="endereco" class="form-label">Endereço</label>
                <input type="text" class="form-control" id="endereco" name="endereco" required>
              </div>
              <div class="mb-3">
                <label for="numero" class="form-label">Número</label>
                <input type="text" class="form-control" id="numero" name="numero" required pattern="\d+" placeholder="Número da residência">
              </div>
              <div class="mb-3">
                <label for="assunto" class="form-label">Assunto</label>
                <input type="text" class="form-control" id="assunto" name="assunto" required>
              </div>
              <div class="mb-3">
                <label for="mensagem" class="form-label">Mensagem</label>
                <textarea class="form-control" id="mensagem" name="mensagem" rows="4" required></textarea>
              </div>
              <button type="submit" class="btn btn-primary w-100">
                <i class="fas fa-paper-plane"></i> Enviar Mensagem
              </button>
            </form>
          </div>
        </div>
      </div>
  
      <!-- Painel lateral -->
      <div id="painel-contato" class="position-absolute top-0 end-0 bg-white border-start shadow p-4" style="width: 300px; height: 100%; display: none; transition: all 0.3s;">
        <h4 class="text-primary">Informações de Contato</h4>
        <ul class="list-unstyled mt-3">
          <li><i class="fas fa-map-marker-alt me-2"></i> Rua Arno Waldemar Döhler, 957 - Joinville/SC</li><br>
          <li><i class="fas fa-phone me-2"></i> (47) 3456-7890</li><br>
          <li><i class="fas fa-envelope me-2"></i> kgb.licithub@gmail.com</li><br>
          <li><i class="fas fa-clock me-2"></i> Seg à Sex, das 08h às 17h</li>
        </ul>
        <a href="../inicial/index.html" class="btn btn-outline-primary mt-3 w-100">
          <i class="fas fa-home"></i> Voltar ao Início
        </a>
      </div>
    </div>
  </main>
  

  <!-- Footer -->
  <footer class="bg-dark text-white py-3">
    <div class="container text-center">
      <p>&copy; 2025 LicitHub. Todos os direitos reservados.</p>
    </div>
  </footer>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Função para mostrar/esconder o painel lateral
    function toggleContatoLateral() {
      const painel = document.getElementById('painel-contato');
      painel.style.display = painel.style.display === 'none' ? 'block' : 'none';
    }
    
    // Máscara para CPF
    document.getElementById('cpf').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      
      if (value.length > 3) {
        value = value.substring(0, 3) + '.' + value.substring(3);
      }
      if (value.length > 7) {
        value = value.substring(0, 7) + '.' + value.substring(7);
      }
      if (value.length > 11) {
        value = value.substring(0, 11) + '-' + value.substring(11);
      }
      
      e.target.value = value.substring(0, 14);
    });
  </script>
</body>
</html>