<?php
session_start();

// Capturar parâmetros da URL com valores padrão
$plano = isset($_GET['plano']) ? htmlspecialchars($_GET['plano']) : 'Plano não especificado';
$preco = isset($_GET['preco']) ? (float)$_GET['preco'] : 0.00;
$plan_id = isset($_GET['plan_id']) ? (int)$_GET['plan_id'] : 0;

// Armazenar na sessão para uso posterior
$_SESSION['plano_selecionado'] = $plano;
$_SESSION['preco_plano'] = $preco;
$_SESSION['plan_id'] = $plan_id;
?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Finalizar Assinatura | LicitHub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <style>
    :root {
      --primary-color: #4361ee;
      --secondary-color: #3f37c9;
      --accent-color: #4895ef;
      --light-color: #f8f9fa;
      --dark-color: #212529;
      --success-color: #4cc9f0;
      --warning-color: #f72585;
    }
    
    body {
      font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
      background-color: #f5f7ff;
      color: var(--dark-color);
      line-height: 1.6;
    }
    
    .checkout-container {
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .header {
      background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
      color: white;
      padding: 2rem 0;
      border-radius: 0 0 20px 20px;
      box-shadow: 0 4px 20px rgba(67, 97, 238, 0.15);
      margin-bottom: 2rem;
    }
    
    .plan-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 30px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
      border: none;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .plan-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
    }
    
    .plan-name {
      color: var(--primary-color);
      font-weight: 700;
      font-size: 1.5rem;
      margin-bottom: 0.5rem;
    }
    
    .plan-price {
      font-size: 2.5rem;
      font-weight: 800;
      color: var(--dark-color);
      margin-bottom: 0;
    }
    
    .plan-period {
      font-size: 1rem;
      color: #6c757d;
    }
    
    .payment-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 6px 30px rgba(0, 0, 0, 0.05);
      padding: 2rem;
      margin-bottom: 2rem;
    }
    
    .payment-method {
      display: flex;
      align-items: center;
      padding: 1rem;
      border: 2px solid #e9ecef;
      border-radius: 8px;
      margin-bottom: 1rem;
      cursor: pointer;
      transition: all 0.3s ease;
    }
    
    .payment-method:hover {
      border-color: var(--accent-color);
    }
    
    .payment-method.active {
      border-color: var(--primary-color);
      background-color: rgba(67, 97, 238, 0.05);
    }
    
    .payment-method i {
      font-size: 1.5rem;
      margin-right: 1rem;
      color: var(--primary-color);
    }
    
    .form-control {
      padding: 0.75rem 1rem;
      border-radius: 8px;
      border: 1px solid #e9ecef;
    }
    
    .form-control:focus {
      border-color: var(--accent-color);
      box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.15);
    }
    
    .btn-primary {
      background-color: var(--primary-color);
      border-color: var(--primary-color);
      padding: 0.75rem 1.5rem;
      border-radius: 8px;
      font-weight: 600;
      transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
      background-color: var(--secondary-color);
      border-color: var(--secondary-color);
      transform: translateY(-2px);
    }
    
    .security-badge {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 2rem;
      color: #6c757d;
      font-size: 0.9rem;
    }
    
    .security-badge i {
      color: var(--success-color);
      margin-right: 0.5rem;
      font-size: 1.2rem;
    }
    
    .card-icon {
      height: 24px;
      margin-left: 10px;
    }
    
    @media (max-width: 768px) {
      .plan-price {
        font-size: 2rem;
      }
    }
  </style>
</head>
<body>
  <div class="header">
    <div class="container">
      <div class="text-center">
        <h1><i class="fas fa-lock me-2"></i>Finalizar Assinatura</h1>
        <p class="lead">Preencha seus dados para concluir o pagamento</p>
      </div>
    </div>
  </div>

  <div class="container checkout-container">
    <div class="row">
      <div class="col-lg-8">
        <div class="payment-card">
          <h2 class="mb-4"><i class="fas fa-credit-card me-2"></i>Informações de Pagamento</h2>
          
          <form id="payment-form" action="process_payment.php" method="POST">
    <input type="hidden" name="plan_id" value="<?php echo $plan_id; ?>">
    <input type="hidden" name="plano" value="<?php echo $plano; ?>">
    <input type="hidden" name="preco" value="<?php echo $preco; ?>">
    
    <h5 class="mb-3">Dados Pessoais</h5>
    <div class="row mb-4">
      <div class="col-md-6 mb-3">
        <label for="name" class="form-label">Nome Completo*</label>
        <input type="text" class="form-control" id="name" name="name" required>
      </div>
      <div class="col-md-6 mb-3">
        <label for="email" class="form-label">E-mail*</label>
        <input type="email" class="form-control" id="email" name="email" required>
      </div>
      <div class="col-md-6">
        <label for="cpf" class="form-label">CPF*</label>
        <input type="text" class="form-control" id="cpf" name="cpf" required>
      </div>
      <div class="col-md-6">
        <label for="phone" class="form-label">Telefone*</label>
        <input type="tel" class="form-control" id="phone" name="phone" required>
      </div>
    </div>
    
    <h5 class="mb-3">Método de Pagamento</h5>
    <div class="payment-methods mb-4">
      <div class="payment-method active" id="credit-card-method">
        <i class="far fa-credit-card"></i>
        <div>
          <h6 class="mb-1">Cartão de Crédito</h6>
          <p class="small text-muted mb-0">Pague com Visa, Mastercard ou outros</p>
        </div>
        <input type="hidden" name="payment_method" id="payment_method" value="credit_card">
      </div>
      
      <div class="payment-method" id="pix-method">
        <i class="fas fa-qrcode"></i>
        <div>
          <h6 class="mb-1">PIX</h6>
          <p class="small text-muted mb-0">Pagamento instantâneo e sem taxas</p>
        </div>
      </div>
    </div>

            
            <div id="credit-card-form">
              <div class="mb-3">
                <label for="card-number" class="form-label">Número do Cartão*</label>
                <input type="text" class="form-control" id="card-number" name="card-number" placeholder="0000 0000 0000 0000" required>
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label for="card-name" class="form-label">Nome no Cartão*</label>
                  <input type="text" class="form-control" id="card-name" name="card-name" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="card-expiry" class="form-label">Validade*</label>
                  <input type="text" class="form-control" id="card-expiry" name="card-expiry" placeholder="MM/AA" required>
                </div>
                <div class="col-md-3 mb-3">
                  <label for="card-cvv" class="form-label">CVV*</label>
                  <input type="text" class="form-control" id="card-cvv" name="card-cvv" placeholder="000" required>
                </div>
              </div>
            </div>
            
            <div id="pix-form" style="display: none;">
              <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i> O QR Code para pagamento será exibido após a confirmação.
              </div>
            </div>
            
            <div class="d-grid mt-4">
              <button type="submit" class="btn btn-primary btn-lg py-3">
                <i class="fas fa-lock me-2"></i> Confirmar Pagamento
              </button>
            </div>
            
            <div class="security-badge">
              <i class="fas fa-shield-alt"></i>
              <span>Pagamento seguro - Seus dados estão protegidos</span>
            </div>
          </form>
        </div>
      </div>
      
      <div class="col-lg-4">
        <div class="plan-card">
          <h3 class="plan-name"><?php echo $plano; ?></h3>
          <div class="d-flex align-items-end mb-3">
            <span class="plan-price">R$ <?php echo number_format($preco, 2, ',', '.'); ?></span>
            <span class="plan-period">/mês</span>
          </div>
          
          <hr>
          
          <h5 class="mb-3">Resumo da Assinatura</h5>
          <ul class="list-unstyled">
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Acesso completo à plataforma</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Suporte prioritário</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Atualizações inclusas</li>
            <li class="mb-2"><i class="fas fa-check-circle text-success me-2"></i> Cancelamento a qualquer momento</li>
          </ul>
          
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    
   // Atualização para definir o método de pagamento
    const paymentMethods = document.querySelectorAll('.payment-method');
    paymentMethods.forEach(method => {
      method.addEventListener('click', function() {
        paymentMethods.forEach(m => m.classList.remove('active'));
        this.classList.add('active');
        
        if (this.id === 'credit-card-method') {
          document.getElementById('payment_method').value = 'credit_card';
          document.getElementById('credit-card-form').style.display = 'block';
          document.getElementById('pix-form').style.display = 'none';
        } else {
          document.getElementById('payment_method').value = 'pix';
          document.getElementById('credit-card-form').style.display = 'none';
          document.getElementById('pix-form').style.display = 'block';
        }
      });
    });

    // Máscaras para os campos
    document.getElementById('cpf').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/(\d{3})(\d)/, '$1.$2');
      value = value.replace(/(\d{3})(\d)/, '$1.$2');
      value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
      e.target.value = value;
    });

    document.getElementById('telefone').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/^(\d{2})(\d)/g, '($1) $2');
      value = value.replace(/(\d)(\d{4})$/, '$1-$2');
      e.target.value = value;
    });

    document.getElementById('card-number').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      value = value.replace(/(\d{4})(?=\d)/g, '$1 ');
      e.target.value = value;
    });

    document.getElementById('card-expiry').addEventListener('input', function(e) {
      let value = e.target.value.replace(/\D/g, '');
      if (value.length > 2) {
        value = value.replace(/^(\d{2})(\d{0,2})/, '$1/$2');
      }
      e.target.value = value;
    });

    // Validação do formulário
    document.getElementById('payment-form').addEventListener('submit', function(e) {
      let isValid = true;
      const requiredFields = this.querySelectorAll('[required]');
      
      requiredFields.forEach(field => {
        if (!field.value.trim()) {
          field.classList.add('is-invalid');
          isValid = false;
        } else {
          field.classList.remove('is-invalid');
        }
      });

      if (!isValid) {
        e.preventDefault();
        alert('Por favor, preencha todos os campos obrigatórios.');
      }
    });
  </script>
</body>
</html>