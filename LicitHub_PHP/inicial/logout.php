<?php
// logout.php
session_start();

// Destrói todos os dados da sessão
$_SESSION = array();

// Destrói o cookie de sessão
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], 
        $params["domain"], 
        $params["secure"], 
        $params["httponly"]
    );
}

// Destrói a sessão
session_destroy();

// Redirecionamento ABSOLUTO e explícito
header("Location: ../login/login.php");
exit(); // Importante: sempre usar exit após header Location
?>