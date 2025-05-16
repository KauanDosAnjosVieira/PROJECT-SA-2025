<?php
session_start();
require_once '../conexao.php';




// Configurações do banco de dados
$host = 'localhost';
$dbname = 'licithub';
$username = 'root';
$password = '';

// Conexão com o banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        
        // Validações básicas
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "<script>alert('Por favor, insira um e-mail válido.');</script>";
        } elseif (empty($password)) {
            echo "<script>alert('Por favor, insira sua senha.');</script>";
        } else {
            // Buscar usuário no banco
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Login bem-sucedido
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_type'] = $user['user_type'];
                
                // Redirecionar conforme tipo de usuário
                if ($user['user_type'] === 'admin') {
                    header("Location: ../inicial/inicial.php");
                } elseif ($user['user_type'] === 'employee') {
                    header("Location: inicial/inicial.php");
                } else {
                    header("Location: ../inicial/inicial.php");
                }
                exit();
            } else {
                echo "<script>alert('E-mail ou senha incorretos.');</script>";
            }
        }
    }
} catch (PDOException $e) {
    echo "<script>alert('Erro no sistema. Tente novamente mais tarde.');</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/login.css" />
  <script src="js/login.js"></script>
  <title>Login</title>
</head>
<body>
  <div class="container">
    <div class="login-panel">
      <h2>Faça seu login</h2>
      <form method="POST">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>

        <label for="senha">Senha</label>
        <div class="password-container">
          <input type="password" placeholder="Senha" name="password" id="password" required>
          <span class="toggle-password" onclick="togglePasswordVisibility()"></span>
        </div>
        
        <a href="forgotpassword.html" class="forgot">Esqueci minha senha</a>
        <button type="submit" class="login-btn">Entrar</button>
      </form>
      <a href="cadastro.html" class="register">Ainda não tenho uma conta</a>
    </div>
    <div class="image-panel"></div>
  </div>

</body>
</html>