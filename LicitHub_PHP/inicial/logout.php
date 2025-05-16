<?php
session_start();

// Destruir todas as variáveis de sessão
session_unset();

// Destruir a sessão
session_destroy();

// Redirecionar para a página de login após logout
header("Location: login.html");
exit();
?>
