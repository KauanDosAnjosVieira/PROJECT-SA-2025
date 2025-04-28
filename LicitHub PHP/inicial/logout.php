<?php
session_start();
session_destroy();  // Destroi todas as variáveis da sessão
header("Location: ../login/login.html");  // Redireciona para a página de login
exit();
?>
