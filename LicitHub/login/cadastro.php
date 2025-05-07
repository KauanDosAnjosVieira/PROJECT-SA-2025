<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root"; // Usuário do MySQL
$password = ""; // Senha do MySQL
$dbname = "licithub_db"; // Nome correto do banco de dados

// Criar a conexão
$conn = new mysqli('localhost', 'root', '', 'licithub_db');

// Verificar se houve erro na conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $senha = $_POST['senha'];
    
    // Verificar se a senha e a confirmação de senha são iguais
    if ($_POST['senha'] === $_POST['confirmar_senha']) {
        // Criptografar a senha
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
        
        // Inserir os dados no banco de dados
        $sql = "INSERT INTO usuarios (email, senha) VALUES ('$email', '$senha_hash')";
        
        if ($conn->query($sql) === TRUE) {
            echo "Cadastro realizado com sucesso!";
        } else {
            echo "Erro: " . $sql . "<br>" . $conn->error;
        }
    } else {
        echo "As senhas não coincidem!";
    }
}

// Fechar a conexão
$conn->close();
?>
