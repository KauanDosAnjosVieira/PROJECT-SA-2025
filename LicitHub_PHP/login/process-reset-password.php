<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['token'])) {
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];
    
    // Validações
    if ($newPassword !== $confirmPassword) {
        $_SESSION['msg'] = 'As senhas não coincidem.';
        $_SESSION['msg_type'] = 'error';
        header('Location: reset-password.php?token='.$token);
        exit();
    }
    
    if (strlen($newPassword) < 8) {
        $_SESSION['msg'] = 'A senha deve ter pelo menos 8 caracteres.';
        $_SESSION['msg_type'] = 'error';
        header('Location: reset-password.php?token='.$token);
        exit();
    }
    
    // Verifica o token
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT id FROM users WHERE reset_token = ? AND reset_token_expires > NOW()");
    $stmt->execute([$token]);
    $user = $stmt->fetch();
    
    if ($user) {
        // Atualiza a senha
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $db->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_token_expires = NULL WHERE id = ?");
        $stmt->execute([$hashedPassword, $user['id']]);
        
        $_SESSION['msg'] = 'Senha redefinida com sucesso!';
        $_SESSION['msg_type'] = 'success';
        unset($_SESSION['reset_token']);
        header('Location: login.php');
        exit();
    } else {
        $_SESSION['msg'] = 'Token inválido ou expirado. Solicite um novo link.';
        $_SESSION['msg_type'] = 'error';
        header('Location: forgot-password.php');
        exit();
    }
}