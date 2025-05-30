<?php
session_start();
require 'config/database.php';

// Verifica se o token é válido
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if (!$user) {
        $_SESSION['msg'] = 'Token inválido ou expirado. Solicite um novo link.';
        $_SESSION['msg_type'] = 'error';
        header('Location: forgot-password.php');
        exit();
    }
    
    $_SESSION['reset_token'] = $token;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - LictHub</title>
    <link rel="stylesheet" href="css/forgotpassword.css">
    <style>
        .alert {
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="forgot-container">
        <h2>Redefinir Senha</h2>
        
        <?php
// Exibir mensagem de sucesso, se existir
if (isset($_SESSION['success_message'])) {
    echo "<script>alert('" . htmlspecialchars($_SESSION['success_message']) . "');</script>";
    unset($_SESSION['success_message']); // Remover a mensagem após exibi-la
}
?>

<?php if (isset($_SESSION['reset_token'])): ?>
    <form action="process-reset-password.php" method="POST">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_SESSION['reset_token']); ?>">
        <input type="password" name="new_password" placeholder="Nova senha" required>
        <input type="password" name="confirm_password" placeholder="Confirmar nova senha" required>
        <button type="submit" class="forgot-button">Redefinir Senha</button>
    </form>
<?php else: ?>
    <p>Token inválido ou expirado.</p>
    <p><a href="forgot-password.php">Solicitar novo link</a></p>
<?php endif; ?>
    </div>
</body>
</html>