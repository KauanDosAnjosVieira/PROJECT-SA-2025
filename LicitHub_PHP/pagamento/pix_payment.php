<?php
require_once 'config/database.php';
require_once 'includes/header.php';

if (!isset($_GET['subscription_id']) || !isset($_SESSION['user_id'])) {
    header("Location: checkout.php");
    exit();
}

$subscription_id = (int)$_GET['subscription_id'];
$user_id = (int)$_SESSION['user_id'];

$conn = getDBConnection();

// Buscar informações da assinatura
$stmt = $conn->prepare("SELECT s.*, p.name as plan_name, p.price 
                       FROM subscriptions s 
                       JOIN plans p ON s.plan_id = p.id 
                       WHERE s.id = ? AND s.user_id = ?");
$stmt->execute([$subscription_id, $user_id]);
$subscription = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$subscription) {
    header("Location: checkout.php");
    exit();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Pagamento via PIX</h3>
                </div>
                <div class="card-body text-center">
                    <div class="alert alert-success">
                        <h4 class="alert-heading">Pagamento Pendente</h4>
                        <p>Por favor, realize o pagamento do PIX para ativar sua assinatura.</p>
                    </div>
                    
                    <div class="pix-payment-container my-4">
                        <div class="pix-qrcode mb-4">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=PIX%3A<?= uniqid() ?>" 
                                 alt="QR Code PIX" class="img-fluid">
                            <p class="mt-2">Escaneie este QR Code com seu app bancário</p>
                        </div>
                        
                        <div class="pix-code mb-4">
                            <h5>Ou copie o código abaixo:</h5>
                            <div class="input-group mb-3">
                                <input type="text" id="pixCode" class="form-control" 
                                       value="00020126360014BR.GOV.BCB.PIX0136<?= uniqid() ?>5204000053039865404<?= number_format($subscription['price'], 2, '.', '') ?>5802BR5913<?= urlencode($_SESSION['user_name']) ?>6008BRASILIA62070503***6304<?= strtoupper(substr(md5(uniqid()), 0, 4)) ?>" 
                                       readonly>
                                <button class="btn btn-outline-primary" onclick="copyPixCode()">
                                    <i class="fas fa-copy"></i> Copiar
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="pix-instructions bg-light p-4 rounded">
                        <h4 class="mb-3">Como pagar com PIX:</h4>
                        <ol class="text-start">
                            <li class="mb-2">Abra o aplicativo do seu banco ou carteira digital</li>
                            <li class="mb-2">Selecione a opção PIX ou Pagar com PIX</li>
                            <li class="mb-2">Escolha "Ler QR Code" e aponte para a imagem acima</li>
                            <li class="mb-2">Confira os dados e confirme o pagamento</li>
                            <li>Sua assinatura será ativada automaticamente</li>
                        </ol>
                    </div>
                    
                    <div class="payment-info mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Valor:</span>
                            <strong>R$ <?= number_format($subscription['price'], 2, ',', '.') ?></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Plano:</span>
                            <strong><?= htmlspecialchars($subscription['plan_name']) ?></strong>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Vencimento:</span>
                            <strong>Hoje, <?= date('H:i') ?></strong>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="dashboard.php" class="btn btn-primary">
                            <i class="fas fa-check-circle me-2"></i> Já efetuei o pagamento
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function copyPixCode() {
    const pixCode = document.getElementById('pixCode');
    pixCode.select();
    pixCode.setSelectionRange(0, 99999);
    document.execCommand('copy');
    
    // Feedback visual
    const originalText = event.target.innerHTML;
    event.target.innerHTML = '<i class="fas fa-check"></i> Copiado!';
    setTimeout(() => {
        event.target.innerHTML = originalText;
    }, 2000);
}
</script>

<?php require_once 'includes/footer.php'; ?>