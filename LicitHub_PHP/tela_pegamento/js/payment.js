// Inicializa o módulo de pagamento
function initPayment() {
    setupPaymentEvents();
}

// Configura eventos de pagamento
function setupPaymentEvents() {
    // Botão confirmar pagamento
    document.getElementById('confirmPayment').addEventListener('click', processPayment);
    
    // Botão enviar comprovante
    document.getElementById('sendReceipt').addEventListener('click', sendReceipt);
    
    // Botão voltar à loja
    document.getElementById('backToShop').addEventListener('click', backToShop);
}

// Processa o pagamento
function processPayment() {
    const btn = document.getElementById('confirmPayment');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processando...';
    
    // Simula o processamento do pagamento
    setTimeout(() => {
        showStep(3);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-lock"></i> Confirmar Pagamento';
    }, 2000);
}

// Envia o comprovante por email
function sendReceipt() {
    alert('Comprovante enviado para o email cadastrado!');
}

// Volta para a loja
function backToShop() {
    // Aqui você redirecionaria para a página inicial
    alert('Redirecionando para a loja...');
    // window.location.href = '/';
}