<?php
require_once 'config/database.php';
require_once 'config/mailer.php';
require_once 'includes/functions.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $conn = getDBConnection();
        $stmt = $conn->prepare("SELECT id, name FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Gerar token seguro
            $token = bin2hex(random_bytes(32));
            $expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
            
            // Salvar token no banco
            $stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
            $stmt->execute([$token, $expires, $user['id']]);
            
            // Enviar e-mail com PHPMailer
            try {
                $mail = configureMailer();
                
                // Destinatário
                $mail->addAddress($email, $user['name']);
                
                // Assunto
                $mail->Subject = 'Redefinição de Senha - LictHub';
                
                // Corpo do e-mail em HTML
                $resetLink = "https://" . $_SERVER['HTTP_HOST'] . "/reset-password.php?token=" . $token;
                
                $mail->isHTML(true);
                $mail->Body = "
                    <html>
                    <head>
                        <style>
                            body { font-family: Arial, sans-serif; line-height: 1.6; }
                            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                            .button { 
                                display: inline-block; 
                                padding: 10px 20px; 
                                background-color: #4361ee; 
                                color: white !important; 
                                text-decoration: none; 
                                border-radius: 5px; 
                                margin: 15px 0;
                            }
                            .footer { 
                                margin-top: 30px; 
                                font-size: 12px; 
                                color: #777;
                            }
                        </style>
                    </head>
                    <body>
                        <div class='container'>
                            <h2>Redefinição de Senha</h2>
                            <p>Olá {$user['name']},</p>
                            <p>Recebemos uma solicitação para redefinir a senha da sua conta LictHub.</p>
                            <p>Clique no botão abaixo para criar uma nova senha:</p>
                            <p>
                                <a href='{$resetLink}' class='button'>Redefinir Senha</a>
                            </p>
                            <p>Se você não solicitou esta redefinição, por favor ignore este e-mail.</p>
                            <p>O link expirará em 1 hora.</p>
                            <div class='footer'>
                                <p>Atenciosamente,<br>Equipe LictHub</p>
                            </div>
                        </div>
                    </body>
                    </html>
                ";
                
                // Versão alternativa em texto simples
                $mail->AltBody = "Olá {$user['name']},\n\nPara redefinir sua senha, acesse: {$resetLink}\n\nEste link expira em 1 hora.\n\nSe você não solicitou esta redefinição, ignore este e-mail.";
                
                $mail->send();
                $message = '<div class="alert alert-success">Um link de redefinição foi enviado para seu e-mail!</div>';
            } catch (Exception $e) {
                error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
                $message = '<div class="alert alert-danger">Ocorreu um erro ao enviar o e-mail. Por favor, tente novamente mais tarde.</div>';
            }
        } else {
            $message = '<div class="alert alert-info">E-mail não encontrado em nosso sistema.</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Por favor, insira um e-mail válido.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - LictHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f7ff;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        .forgot-container {
            max-width: 500px;
            margin: 5rem auto;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        .forgot-container h2 {
            color: #4361ee;
            font-weight: 700;
            margin-bottom: 1rem;
        }
        .forgot-container p {
            color: #6c757d;
            margin-bottom: 2rem;
        }
        .form-control {
            padding: 0.75rem 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
        .btn-forgot {
            background-color: #4361ee;
            color: white;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            width: 100%;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-forgot:hover {
            background-color: #3a56d4;
            transform: translateY(-2px);
        }
        .back-to-login {
            text-align: center;
            margin-top: 1.5rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="forgot-container">
            <div class="text-center mb-4">
                <i class="fas fa-lock fa-3x mb-3" style="color: #4361ee;"></i>
                <h2>Recuperar Senha</h2>
                <p>Insira seu e-mail para receber um link de redefinição de senha.</p>
            </div>
            
            <?php echo $message; ?>
            
            <form method="POST" action="forgot-password.php">
                <div class="mb-3">
                    <label for="email" class="form-label">E-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-forgot">
                    <i class="fas fa-paper-plane me-2"></i> Enviar Link
                </button>
            </form>
            
            <p class="back-to-login mt-4">
                Lembrou sua senha? <a href="login.php">Voltar ao Login</a>
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>