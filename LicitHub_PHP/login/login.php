<?php
session_start();

$erro = ''; // Variável para armazenar o erro

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conn = new mysqli('localhost', 'root', '', 'licithub_db');

    if ($conn->connect_error) {
        die("Erro de conexão: " . $conn->connect_error);
    }

    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    // Verificar se o usuário existe
    $sql = "SELECT * FROM usuarios WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        
        // Verifica se a senha está correta
        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_email'] = $usuario['email'];            
            // Redireciona para a página inicial após login correto
            header("Location: ../inicial/index.php");
            exit();
        } else {
            // Senha incorreta
            $erro = 'Senha incorreta!';
        }
    } else {
        // Email não encontrado
        $erro = 'Email não encontrado!';
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - LicitHub</title>
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>

        <?php
        // Se houver um erro, exibe o alert e redireciona
        if ($erro) {
            echo "<script>alert('$erro'); window.location.href = 'login.html';</script>";
        }
        ?>

