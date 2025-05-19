<?php
require_once '../conexao.php';
try {
    $pdo = co::getConexao();
    echo "Conexão bem-sucedida!";
} catch (Exception $e) {
    echo $e->getMessage();
}