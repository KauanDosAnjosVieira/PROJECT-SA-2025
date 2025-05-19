<?php
require_once 'config/database.php';
require_once 'includes/header.php';

$conn = getDBConnection();
$stmt = $conn->query("SELECT * FROM plans WHERE is_active = 1");
$plans = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container my-5">
    <h1 class="text-center mb-5">Escolha seu Plano</h1>
    
    <div class="row">
        <?php foreach ($plans as $plan): ?>
        <div class="col-md-4 mb-4">
            <div class="card plan-card">
                <div class="card-header bg-primary text-white">
                    <h3 class="text-center"><?= htmlspecialchars($plan['name']) ?></h3>
                </div>
                <div class="card-body">
                    <h4 class="card-title pricing-card-title text-center">
                        R$ <?= number_format($plan['price'], 2, ',', '.') ?>
                        <small class="text-muted">/mês</small>
                    </h4>
                    
                    <?php 
                    $features = json_decode($plan['features'], true);
                    if ($features): ?>
                    <ul class="list-unstyled mt-3 mb-4">
                        <?php foreach ($features as $key => $value): ?>
                            <?php if (is_array($value)): ?>
                                <li><i class="fas fa-check text-success"></i> <?= ucfirst($key) ?>: <?= implode(', ', $value) ?></li>
                            <?php else: ?>
                                <li><i class="fas fa-check text-success"></i> <?= ucfirst($key) ?>: <?= $value ?></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                    <?php endif; ?>
                    
                    <?php if ($plan['trial_days'] > 0): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-gift"></i> <?= $plan['trial_days'] ?> dias grátis
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-center">
                    <a href="checkout.php?plan_id=<?= $plan['id'] ?>" class="btn btn-primary btn-lg">
                        Assinar Agora
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>