<?php
// index.php
session_start();
require_once '../conexao.php';

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Obter informações do plano se um ID de plano foi fornecido
$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : null;
$plan = null;
$user = null;

try {
    // Obter informações do plano
    if ($plan_id) {
        $stmt = $pdo->prepare("SELECT * FROM plans WHERE id = ?");
        $stmt->execute([$plan_id]);
        $plan = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Obter informações do usuário
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$plan) {
        throw new Exception("Plano selecionado não encontrado.");
    }
} catch (Exception $e) {
    $_SESSION['error'] = $e->getMessage();
    header('Location: plans.php');
    exit;
}

// Calcular valores
$setup_fee = 0; // Sem taxa de instalação para assinaturas
$subtotal = $plan['price'];
$tax = round($subtotal * 0.10, 2);
$total = $subtotal + $tax;

// Armazenar dados na sessão para uso posterior
$_SESSION['checkout'] = [
    'plan_id' => $plan['id'],
    'plan_name' => $plan['name'],
    'plan_price' => $plan['price'],
    'total' => $total,
    'tax' => $tax
];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Compra - LicitHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #4e73df;
            --secondary-color: #f8f9fc;
            --accent-color: #2e59d9;
        }
        
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .card {
            border-radius: 0.5rem;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: none;
        }
        
        .payment-method {
            border: 1px solid #e3e6f0;
            border-radius: 0.35rem;
            transition: all 0.3s;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .payment-method:hover {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.05);
        }
        
        .payment-method.active {
            border-color: var(--primary-color);
            background-color: rgba(78, 115, 223, 0.1);
        }
        
        .payment-icon {
            font-size: 1.75rem;
            margin-right: 0.5rem;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 0.5rem 1.5rem;
        }
        
        .btn-primary:hover {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }
        
        .summary-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #e3e6f0;
        }
        
        .total-box {
            background-color: var(--secondary-color);
            border-radius: 0.35rem;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .plan-name {
            font-weight: 600;
            color: var(--primary-color);
        }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h2 class="h4 text-center mb-4">Finalizar Assinatura</h2>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 33%;" aria-valuenow="33" aria-valuemin="0" aria-valuemax="100">1. Pagamento</div>
                    </div>
                    
                    <div class="row">
                        <!-- Payment Form -->
                        <div class="col-md-7">
                            <div class="card mb-4">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Método de Pagamento</h5>
                                </div>
                                <div class="card-body">
                                    <form id="paymentForm" action="process_payment.php" method="POST">
                                        <div class="payment-method active" id="creditCardOption" onclick="selectPayment('credit_card')">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" value="credit_card" id="creditCard" checked>
                                                <label class="form-check-label d-flex align-items-center" for="creditCard">
                                                    <i class="bi bi-credit-card payment-icon"></i>
                                                    <div>
                                                        <strong>Cartão de Crédito</strong><br>
                                                        <small class="text-muted">Visa, Mastercard, American Express</small>
                                                    </div>
                                                </label>
                                            </div>
                                            
                                            <!-- Credit Card Form (shown when selected) -->
                                            <div id="creditCardForm" class="mt-3">
                                                <div class="mb-3">
                                                    <label for="cardNumber" class="form-label">Número do Cartão</label>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456" maxlength="19">
                                                        <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="row">
                                                    <div class="col-md-6 mb-3">
                                                        <label for="cardExpiry" class="form-label">Validade</label>
                                                        <input type="text" class="form-control" id="cardExpiry" name="card_expiry" placeholder="MM/AA" maxlength="5">
                                                    </div>
                                                    <div class="col-md-6 mb-3">
                                                        <label for="cardCvc" class="form-label">CVC</label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control" id="cardCvc" name="card_cvc" placeholder="123" maxlength="4">
                                                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <label for="cardName" class="form-label">Nome no Cartão</label>
                                                    <input type="text" class="form-control" id="cardName" name="card_name" placeholder="Nome como impresso no cartão">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="payment-method" id="pixOption" onclick="selectPayment('pix')">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" value="pix" id="pix">
                                                <label class="form-check-label d-flex align-items-center" for="pix">
                                                    <i class="bi bi-upc-scan payment-icon"></i>
                                                    <div>
                                                        <strong>PIX</strong><br>
                                                        <small class="text-muted">Pagamento instantâneo</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="payment-method" id="paypalOption" onclick="selectPayment('paypal')">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="payment_method" value="paypal" id="paypal">
                                                <label class="form-check-label d-flex align-items-center" for="paypal">
                                                    <i class="bi bi-paypal payment-icon"></i>
                                                    <div>
                                                        <strong>PayPal</strong><br>
                                                        <small class="text-muted">Pague com sua conta PayPal</small>
                                                    </div>
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid gap-2 mt-4">
                                            <button type="submit" class="btn btn-primary btn-lg">
                                                <i class="bi bi-lock-fill"></i> Finalizar Compra - R$ <?= number_format($total, 2, ',', '.') ?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            
                            <div class="text-center text-muted small">
                                <p>Pagamento seguro criptografado com SSL<br>
                                <img src="https://img.icons8.com/color/48/000000/ssl-logo.png" width="30">
                                <img src="https://img.icons8.com/color/48/000000/safety-checked.png" width="30"></p>
                            </div>
                        </div>
                        
                        <!-- Order Summary -->
                        <div class="col-md-5">
                            <div class="card sticky-top" style="top: 20px;">
                                <div class="card-header bg-white py-3">
                                    <h5 class="mb-0">Resumo da Assinatura</h5>
                                </div>
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <div class="plan-name"><?= htmlspecialchars($plan['name']) ?></div>
                                        <div class="text-end">
                                            <span class="h5">R$ <?= number_format($plan['price'], 2, ',', '.') ?></span><br>
                                            <small class="text-muted"><?= $plan['interval'] === 'yearly' ? 'por ano' : 'por mês' ?></small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <h6 class="mb-2">Recursos incluídos:</h6>
                                        <?php 
                                        $features = json_decode($plan['features'], true);
                                        if ($features): ?>
                                            <ul class="list-unstyled">
                                                <?php foreach ($features as $key => $value): ?>
                                                    <li class="mb-2">
                                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                        <?php 
                                                        if (is_array($value)) {
                                                            echo ucfirst($key) . ': ' . implode(', ', $value);
                                                        } else {
                                                            echo ucfirst($key) . ': ' . $value;
                                                        }
                                                        ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <hr>
                                    
                                    <div class="summary-item d-flex justify-content-between">
                                        <div>Subtotal</div>
                                        <div>R$ <?= number_format($subtotal, 2, ',', '.') ?></div>
                                    </div>
                                    
                                    <div class="summary-item d-flex justify-content-between">
                                        <div>Taxas</div>
                                        <div>R$ <?= number_format($tax, 2, ',', '.') ?></div>
                                    </div>
                                    
                                    <div class="total-box">
                                        <div class="d-flex justify-content-between fw-bold fs-5">
                                            <div>Total</div>
                                            <div>R$ <?= number_format($total, 2, ',', '.') ?></div>
                                        </div>
                                        <small class="text-muted"><?= $plan['trial_days'] > 0 ? 'Inclui ' . $plan['trial_days'] . ' dias grátis' : '' ?></small>
                                    </div>
                                    
                                    <div class="mt-3 text-center small text-muted">
                                        <i class="bi bi-arrow-repeat"></i> Cancele a qualquer momento
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Formatar número do cartão
    document.getElementById('cardNumber').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\s+/g, '');
        if (value.length > 0) {
            value = value.match(new RegExp('.{1,4}', 'g')).join(' ');
        }
        e.target.value = value;
    });
    
    // Formatar data de expiração
    document.getElementById('cardExpiry').addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length > 2) {
            value = value.substring(0, 2) + '/' + value.substring(2, 4);
        }
        e.target.value = value;
    });
    
    // Selecionar método de pagamento
    function selectPayment(method) {
        // Atualizar visual dos cards
        document.querySelectorAll('.payment-method').forEach(el => {
            el.classList.remove('active');
        });
        document.getElementById(method + 'Option').classList.add('active');
        
        // Atualizar radio button
        document.getElementById(method).checked = true;
        
        // Mostrar/ocultar formulários específicos
        document.getElementById('creditCardForm').style.display = method === 'credit_card' ? 'block' : 'none';
    }
    
    // Validação do formulário antes de enviar
    document.getElementById('paymentForm').addEventListener('submit', function(e) {
        const method = document.querySelector('input[name="payment_method"]:checked').value;
        
        if (method === 'credit_card') {
            const cardNumber = document.getElementById('cardNumber').value.replace(/\s+/g, '');
            const cardExpiry = document.getElementById('cardExpiry').value;
            const cardCvc = document.getElementById('cardCvc').value;
            const cardName = document.getElementById('cardName').value;
            
            if (!cardNumber || cardNumber.length < 16) {
                alert('Por favor, insira um número de cartão válido');
                e.preventDefault();
                return;
            }
            
            if (!cardExpiry || cardExpiry.length !== 5) {
                alert('Por favor, insira uma data de validade válida (MM/AA)');
                e.preventDefault();
                return;
            }
            
            if (!cardCvc || cardCvc.length < 3) {
                alert('Por favor, insira um CVC válido');
                e.preventDefault();
                return;
            }
            
            if (!cardName) {
                alert('Por favor, insira o nome como no cartão');
                e.preventDefault();
                return;
            }
        }
    });
</script>
</body>
</html> 