<?php
session_start();

$erro = ''; // Variável para armazenar o erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'licithub'); // Corrigido: licithub

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $email = trim($_POST['email'] ?? '');
    $senha = trim($_POST['senha'] ?? '');

    if (!empty($email) && !empty($senha)) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $resultado = $stmt->get_result();

        if ($resultado->num_rows > 0) {
            $usuario = $resultado->fetch_assoc();
            
            if (password_verify($senha, $usuario['password'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_nome'] = $usuario['name'];
                $_SESSION['usuario_tipo'] = $usuario['user_type'];

                // Redirecionar para inicial
                header("Location: ../inicial/index.php");
                exit();
            } else {
                $erro = 'Senha incorreta!';
            }
        } else {
            $erro = 'Email não encontrado!';
        }

        $stmt->close();
    } else {
        $erro = 'Por favor, preencha todos os campos!';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - LicitHub</title>
    <link rel="stylesheet" href="css/login.css"> <!-- Seu CSS original mantido -->
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <form method="POST" action="login.php">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="senha" placeholder="Senha" required>
            <button type="submit">Entrar</button>
        </form>

        <?php
        if ($erro) {
            echo "<div class='erro'>$erro</div>"; // Usando div para erro, estilize no seu CSS
        }
        ?>
    </div>
</body>
</html>
