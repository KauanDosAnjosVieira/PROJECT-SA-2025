<?php
session_start();

$erro = ''; // Variável para armazenar o erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'licithub');

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? ''; // Alterado de 'senha' para 'password'

    // Verificar se o usuário existe
    $sql = "SELECT id, name, email, password, user_type FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Verifica a senha
        if (password_verify($password, $usuario['password'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['usuario_nome'] = $usuario['name'];
            $_SESSION['usuario_tipo'] = $usuario['user_type'];
            
            header("Location: ../inicial/inicial.php");
            exit();
        } else {
            $erro = 'Senha incorreta!';
        }
    } else {
        $erro = 'Email não encontrado!';
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link rel="stylesheet" href="css/login.css" />
  <script src="js/login.js" defer></script>
  <title>Login</title>
</head>
<body>
  <div class="container">
    <div class="login-panel">
      <h2>Faça seu login</h2>
      <form method="POST" action="login.php">
        <label for="email">Email</label>
        <input type="email" name="email" id="email" placeholder="Email" required>

        <label for="senha">Senha</label>
        <input type="password" placeholder="Senha" name="password" id="password" required>
        <span class="toggle-password" onclick="togglePasswordVisibility('password')"></span>

        <a href="forgotpassword.html" class="forgot">Esqueci minha senha</a>
        <button type="submit" class="login-btn">Entrar</button>
      </form>

      <?php
      if ($erro) {
          echo "<script>alert('$erro'); window.location.href = 'login.php';</script>";
      }
      ?>

      <a href="cadastro.php" class="register">Ainda não tenho uma conta</a>
    </div>
    <div class="image-panel">
    </div>
  </div>
</body>
</html>