<?php
session_start();
require '../vendor/autoload.php';
require 'config/database.php';

// Verifica se o e-mail foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    
    // Verifica se o e-mail existe no banco de dados
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Cria um token único
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", strtotime('+1 hour'));
        
        // Armazena o token no banco de dados
        $stmt = $db->prepare("UPDATE users SET reset_token = ?, reset_token_expires = ? WHERE id = ?");
        $stmt->execute([$token, $expires, $user['id']]);
        
        // Configuração do PHPMailer
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);
        
        try {
            // Configurações do servidor SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com'; // Seu servidor SMTP
            $mail->SMTPAuth = true;
            $mail->Username = 'kgb.licithub@gmail.com'; // Seu e-mail SMTP
            $mail->Password = 'ccky rnnb xutm mmdc'; // Sua senha SMTP
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;
            
            // Remetente e destinatário
            $mail->setFrom('nao-responda@lichub.com', 'LicitHub');
            $mail->addAddress($email);
            
            // Conteúdo do e-mail
            $mail->isHTML(true);
            $mail->Subject = 'Redefinicao de Senha - LicitHub';
            
            $resetLink = "http://localhost:8080/LICITHUB_PHP/login/reset-password.php?token=$token";
            
            $mail->Body = "
                <h1>Redefinir Senha</h1>
                <p>Você solicitou a redefinição de senha para sua conta no Licithub.</p>
                <p>Clique no link abaixo para redefinir sua senha:</p>
                <p><a href='$resetLink'>$resetLink</a></p>
                <p>Este link expirará em 1 hora.</p>
                <p>Se você não solicitou esta redefinição, por favor ignore este e-mail.</p>
            ";
            
            $mail->AltBody = "Para redefinir sua senha, acesse: $resetLink";
            
            $mail->send();
            
            $_SESSION['msg'] = 'Um link de redefinição foi enviado para seu e-mail!';
            $_SESSION['msg_type'] = 'success';
        } catch (Exception $e) {
            $_SESSION['msg'] = 'Ocorreu um erro ao enviar o e-mail. Tente novamente mais tarde.';
            $_SESSION['msg_type'] = 'error';
            error_log("Erro ao enviar e-mail: " . $mail->ErrorInfo);
        }
    } else {
        $_SESSION['msg'] = 'E-mail não encontrado em nosso sistema.';
        $_SESSION['msg_type'] = 'error';
    }
    
    header('Location: forgot-password.php');
    exit();
}