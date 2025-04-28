<?php
$conn = new mysqli('localhost', 'root', '', 'licithub_db');

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
} else {
    echo "Conectado com sucesso!";
}
?>
