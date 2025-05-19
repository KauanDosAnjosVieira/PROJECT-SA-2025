<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

function configureMailer() {
    $mail = new PHPMailer(true);
    
    // Configurações do servidor SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.seuprovedor.com'; // Seu servidor SMTP
    $mail->SMTPAuth = true;
    $mail->Username = 'seuemail@seudominio.com'; // Seu e-mail SMTP
    $mail->Password = 'suasenha'; // Sua senha SMTP
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Ou ENCRYPTION_SMTPS
    $mail->Port = 587; // Porta SMTP (587 para TLS, 465 para SSL)
    
    // Configurações do remetente
    $mail->setFrom('nao-responder@licthub.com', 'LictHub');
    $mail->addReplyTo('suporte@licthub.com', 'Suporte LictHub');
    
    return $mail;
}
?>