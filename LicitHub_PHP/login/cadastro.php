<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco
    $conn = new mysqli('localhost', 'root', '', 'licithub_db');

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Recebe e sanitiza dados
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirmarSenha = $_POST['confirmarSenha'];

    // Verificar se as senhas são iguais
    if ($senha !== $confirmarSenha) {
        echo "<script>alert('As senhas não coincidem.'); window.history.back();</script>";
        exit();
    }

    // Criptografar a senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir no banco
    $sql = "INSERT INTO usuarios (email, senha) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $senhaHash);

    if ($stmt->execute()) {
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.html';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar. Tente novamente.'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
