<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conexão com o banco
    $conn = new mysqli('localhost', 'root', '', 'licithub');

    if ($conn->connect_error) {
        die("Conexão falhou: " . $conn->connect_error);
    }

    // Recebe e sanitiza dados
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $user_type = 'customer'; // Valor padrão para novos cadastros

    // Verificar se as senhas são iguais
    if ($password !== $confirmPassword) {
        echo "<script>alert('As senhas não coincidem.'); window.history.back();</script>";
        exit();
    }

    // Verificar se o email já existe
    $check_email = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();
    
    if ($check_email->num_rows > 0) {
        echo "<script>alert('Este email já está cadastrado.'); window.history.back();</script>";
        exit();
    }
    $check_email->close();

    // Criptografar a senha
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // Inserir no banco
    $sql = "INSERT INTO users (name, email, password, user_type, created_at, updated_at) 
            VALUES (?, ?, ?, ?, NOW(), NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $passwordHash, $user_type);

    if ($stmt->execute()) {
        // Opcional: atribuir um role padrão (se necessário)
        $user_id = $stmt->insert_id;
        
        // Buscar o role padrão 'customer' (você precisa ter isso na tabela roles)
        $role_query = $conn->query("SELECT id FROM roles WHERE name = 'customer' LIMIT 1");
        if ($role_query->num_rows > 0) {
            $role = $role_query->fetch_assoc();
            $conn->query("INSERT INTO user_roles (user_id, role_id, created_at) 
                         VALUES ($user_id, {$role['id']}, NOW())");
        }
        
        echo "<script>alert('Cadastro realizado com sucesso!'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Erro ao cadastrar: " . $conn->error . "'); window.history.back();</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - LicitHub</title>
    <link rel="stylesheet" href="css/cadastro.css">
</head>
<body>
    <div class="register-container">
        <div class="register-left">
            <h2>Bem-vindo de volta!</h2>
            <p>Grandes oportunidades nascem de boas escolhas. Faça parte da melhor licitação hoje!</p>
            <button class="signin-button" onclick="window.location.href='login.html'">Entrar</button> <!-- Redirecionamento para a página de login -->
        </div>
        <div class="register-right">
            <h2>Crie Sua Conta</h2>
            <form id="register-form" method="POST" action="cadastro.php">
                <input type="email" placeholder="Email" name="email" id="email" required>  
                <div class="password-container">
                    <input type="password" placeholder="Senha" name="senha" id="password" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('password')"></span>
                </div>
                
                <div class="password-container">
                    <input type="password" placeholder="Confirmar Senha" id="confirm-password" name="confirmarSenha" required>
                    <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')"></span>
                </div>
                
                
                <button class="register-button" type="submit">Criar Conta</button>
            </form>
        </div>
    </div>

    <script src="js/cadastro.js"></script> <!-- Certifique-se de que este arquivo exista e esteja correto -->
</body>
</html>
