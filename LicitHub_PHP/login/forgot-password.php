<?php
session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - LictHub</title>
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
        <h2>Recuperar Senha</h2>
        
        <?php
        if (isset($_SESSION['msg'])) {
            echo '<div class="alert '.$_SESSION['msg_type'].'">'.$_SESSION['msg'].'</div>';
            unset($_SESSION['msg']);
            unset($_SESSION['msg_type']);
        }
        ?>
        
        <p>Insira aqui o seu e-mail para receber um link de redefinição de senha.</p>
        <form action="send-reset-link.php" method="POST">
            <input type="email" name="email" placeholder="Email" id="email" required>
            <button type="submit" class="forgot-button">Enviar Link</button>
        </form>
        <p class="back-to-login">Lembrou sua senha? <a href="login.php">Voltar ao Login</a></p>
    </div>
</body>
</html>