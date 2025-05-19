<?php
require_once 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: checkout.php");
    exit();
}

$conn = getDBConnection();

// Validar e sanitizar dados
$plan_id = (int)$_POST['plan_id'];
$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$cpf = filter_input(INPUT_POST, 'cpf', FILTER_SANITIZE_STRING);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_STRING);
$payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_STRING);

// Buscar informações do plano
$stmt = $conn->prepare("SELECT * FROM plans WHERE id = ?");
$stmt->execute([$plan_id]);
$plan = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$plan) {
    $_SESSION['error'] = "Plano não encontrado.";
    header("Location: checkout.php");
    exit();
}

// Simular processamento de pagamento
$payment_status = 'paid';
$gateway_id = 'sim_' . uniqid();

try {
    $conn->beginTransaction();
    
    // Criar usuário (simulação)
    $stmt = $conn->prepare("INSERT INTO users (name, email, user_type, created_at, updated_at, phone) 
                           VALUES (?, ?, 'customer', NOW(), NOW(), ?)");
    $stmt->execute([$name, $email, $phone]);
    $user_id = $conn->lastInsertId();
    
    // Criar assinatura
    $trial_ends_at = $plan['trial_days'] > 0 ? date('Y-m-d H:i:s', strtotime("+{$plan['trial_days']} days")) : null;
    $ends_at = date('Y-m-d H:i:s', strtotime("+1 {$plan['interval']}"));
    
    $stmt = $conn->prepare("INSERT INTO subscriptions 
                           (user_id, plan_id, status, trial_ends_at, starts_at, ends_at, gateway, gateway_id, created_at, updated_at) 
                           VALUES (?, ?, ?, ?, NOW(), ?, ?, ?, NOW(), NOW())");
    $stmt->execute([
        $user_id,
        $plan['id'],
        $payment_status,
        $trial_ends_at,
        $ends_at,
        $payment_method == 'credit_card' ? 'stripe' : 'pix',
        $gateway_id
    ]);
    $subscription_id = $conn->lastInsertId();
    
    // Registrar pagamento
    $stmt = $conn->prepare("INSERT INTO payments 
                           (user_id, subscription_id, amount, currency, gateway, gateway_id, status, paid_at, created_at, updated_at) 
                           VALUES (?, ?, ?, 'BRL', ?, ?, ?, NOW(), NOW(), NOW())");
    $stmt->execute([
        $user_id,
        $subscription_id,
        $plan['price'],
        $payment_method == 'credit_card' ? 'stripe' : 'pix',
        $gateway_id,
        $payment_status
    ]);
    
    $conn->commit();
    
    // Redirecionar para página de sucesso
    $_SESSION['subscription_id'] = $subscription_id;
    $_SESSION['user_id'] = $user_id;
    
    if ($payment_method == 'pix') {
        header("Location: pix_payment.php?subscription_id=" . $subscription_id);
    } else {
        header("Location: dashboard.php?success=Assinatura realizada com sucesso!");
    }
    exit();
    
} catch (PDOException $e) {
    $conn->rollBack();
    $_SESSION['error'] = "Erro ao processar pagamento: " . $e->getMessage();
    header("Location: checkout.php");
    exit();
}
?>