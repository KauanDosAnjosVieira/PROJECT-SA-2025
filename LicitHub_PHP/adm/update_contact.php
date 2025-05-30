<?php
// update_contact.php
require_once '../conexao.php';
session_start();

// Verifica se o usuário está logado e é admin
if (!isset($_SESSION['user_id']))    {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['success' => false, 'message' => 'Não autorizado']);
    exit();
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("HTTP/1.1 405 Method Not Allowed");
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit();
}

// Valida os dados recebidos
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

if (!$id || !in_array($status, ['pendente', 'respondido', 'cancelado'])) {
    echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
    exit();
}

try {
    $pdo = getConexao();
    
    // Atualiza o status do contato
    $stmt = $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    
    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Nenhum registro afetado']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erro no banco de dados: ' . $e->getMessage()]);
}