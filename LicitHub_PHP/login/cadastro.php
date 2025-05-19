<?php
session_start();
require_once '../conexao.php';

// Função para exibir alerta e redirecionar
function showAlertAndRedirect($message, $isError = true, $redirectTo = 'cadastro.php') {
    echo "<script>
        alert('" . addslashes($message) . "');
        window.location.href = '" . $redirectTo . "';
    </script>";
    exit();
}

try {
    $pdo = getConexao();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome']);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = $_POST['senha'];
        $confirmarSenha = $_POST['confirmarSenha'];
        
        $errors = [];
        
        // Validações
        if (empty($nome)) {
            $errors[] = "Por favor, insira seu nome completo.";
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "Por favor, insira um e-mail válido.";
        }
        
        if (strlen($senha) < 8) {
            $errors[] = "A senha deve ter pelo menos 8 caracteres.";
        }
        
        if ($senha !== $confirmarSenha) {
            $errors[] = "As senhas não coincidem.";
        }
        
        // Verificar se email já existe
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $errors[] = "Este e-mail já está cadastrado.";
        }
        
        if (!empty($errors)) {
            showAlertAndRedirect(implode("\n", $errors));
        }
        
        // Cadastrar usuário
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
        
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, user_type, created_at, updated_at) 
                              VALUES (?, ?, ?, 'customer', NOW(), NOW())");
        $stmt->execute([$nome, $email, $senhaHash]);
        
        // Buscar dados do usuário recém-criado
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        // Criar sessão
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_type'] = $user['user_type'];
        
        showAlertAndRedirect("Cadastro realizado com sucesso!", false, 'login.php');
    }

    // Se não for POST, exibe o formulário
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
                <input type="text" placeholder="Nome Completo" name="nome" id="nome" required>
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

   

        <script>
            function togglePassword(fieldId) {
                const field = document.getElementById(fieldId);
                const icon = field.nextElementSibling;
                
                if (field.type === 'password') {
                    field.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    field.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        </script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    </body>
    </html>
    <?php
} catch (PDOException $e) {
    showAlertAndRedirect("Ocorreu um erro no cadastro. Por favor, tente novamente mais tarde.");
}
?>