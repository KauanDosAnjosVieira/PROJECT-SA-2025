<?php
require_once 'config/database.php';
session_start();

// Verificar se usuário está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: checkout.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection();

// Buscar informações do usuário
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Buscar assinaturas do usuário
$stmt = $conn->prepare("SELECT s.*, p.name as plan_name, p.price, p.interval 
                       FROM subscriptions s 
                       JOIN plans p ON s.plan_id = p.id 
                       WHERE s.user_id = ? 
                       ORDER BY s.created_at DESC");
$stmt->execute([$user_id]);
$subscriptions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Buscar pagamentos do usuário
$stmt = $conn->prepare("SELECT p.* 
                       FROM payments p 
                       JOIN subscriptions s ON p.subscription_id = s.id 
                       WHERE s.user_id = ? 
                       ORDER BY p.paid_at DESC");
$stmt->execute([$user_id]);
$payments = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once 'includes/header.php';
?>

<div class="container my-5">
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?= htmlspecialchars($_GET['success']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Minha Conta</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto" 
                             style="width: 80px; height: 80px; font-size: 2rem;">
                            <?= strtoupper(substr($user['name'], 0, 1)) ?>
                        </div>
                        <h5 class="mt-3"><?= htmlspecialchars($user['name']) ?></h5>
                        <p class="text-muted"><?= htmlspecialchars($user['email']) ?></p>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <a href="logout.php" class="btn btn-outline-danger">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Minha Assinatura</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($subscriptions)): ?>
                        <div class="alert alert-info">
                            Você não possui assinaturas ativas. <a href="checkout.php" class="alert-link">Assine agora</a>.
                        </div>
                    <?php else: ?>
                        <?php foreach ($subscriptions as $sub): ?>
                            <div class="subscription-item mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h5><?= htmlspecialchars($sub['plan_name']) ?></h5>
                                    <span class="badge <?= $sub['status'] == 'active' ? 'bg-success' : 'bg-secondary' ?>">
                                        <?= $sub['status'] == 'active' ? 'Ativa' : ucfirst($sub['status']) ?>
                                    </span>
                                </div>
                                
                                <div class="subscription-details">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Valor:</strong> R$ <?= number_format($sub['price'], 2, ',', '.') ?></p>
                                            <p><strong>Ciclo:</strong> <?= $sub['interval'] == 'monthly' ? 'Mensal' : 'Anual' ?></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Início:</strong> <?= date('d/m/Y', strtotime($sub['starts_at'])) ?></p>
                                            <p><strong>Próxima cobrança:</strong> <?= date('d/m/Y', strtotime($sub['ends_at'])) ?></p>
                                        </div>
                                    </div>
                                    
                                    <?php if ($sub['trial_ends_at'] && strtotime($sub['trial_ends_at']) > time()): ?>
                                        <div class="alert alert-info mt-2">
                                            <i class="fas fa-gift me-2"></i> Período de teste até <?= date('d/m/Y', strtotime($sub['trial_ends_at'])) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="d-flex gap-2 mt-3">
                                    <?php if ($sub['status'] == 'active'): ?>
                                        <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                                            <i class="fas fa-times-circle me-1"></i> Cancelar Assinatura
                                        </button>
                                    <?php endif; ?>
                                    <a href="#" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-file-invoice me-1"></i> Ver Detalhes
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">Histórico de Pagamentos</h4>
                </div>
                <div class="card-body">
                    <?php if (empty($payments)): ?>
                        <div class="alert alert-info">Nenhum pagamento encontrado.</div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Valor</th>
                                        <th>Status</th>
                                        <th>Método</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($payments as $payment): ?>
                                    <tr>
                                        <td><?= date('d/m/Y H:i', strtotime($payment['paid_at'])) ?></td>
                                        <td>R$ <?= number_format($payment['amount'], 2, ',', '.') ?></td>
                                        <td>
                                            <span class="badge <?= $payment['status'] == 'paid' ? 'bg-success' : 'bg-warning' ?>">
                                                <?= $payment['status'] == 'paid' ? 'Pago' : ucfirst($payment['status']) ?>
                                            </span>
                                        </td>
                                        <td><?= ucfirst($payment['gateway']) ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-receipt me-1"></i> Recibo
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Cancelamento -->
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Cancelar Assinatura</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja cancelar sua assinatura?</p>
                <p class="text-danger">Você perderá acesso ao serviço ao final do período atual.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Voltar</button>
                <button type="button" class="btn btn-danger">Confirmar Cancelamento</button>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>