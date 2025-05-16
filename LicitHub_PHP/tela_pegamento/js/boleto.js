let boletoCode;

// Inicializa o boleto
function initBoleto() {
    generateBoleto();
    setupBoletoEvents();
}

// Gera um código de boleto aleatório
function generateBoleto() {
    let code = '';
    
    for (let i = 0; i < 47; i++) {
        if ([5, 11, 17, 23, 29, 35, 41].includes(i)) {
            code += ' ';
        } else {
            code += Math.floor(Math.random() * 10);
        }
    }
    
    boletoCode = code.replace(/\s/g, '');
    document.getElementById('boletoCode').value = code;
    
    // Gera o código de barras
    generateBarcode();
}

// Gera a imagem do código de barras
function generateBarcode() {
    JsBarcode('#boletoBarcode', boletoCode, {
        format: 'ITF',
        lineColor: '#000000',
        width: 2,
        height: 50,
        displayValue: false
    });
}

// Configura eventos do boleto
function setupBoletoEvents() {
    // Botão copiar código
    document.getElementById('copyBoletoCode').addEventListener('click', copyBoletoCode);
    
    // Botão imprimir
    document.getElementById('printBoleto').addEventListener('click', printBoleto);
    
    // Botão baixar
    document.getElementById('downloadBoleto').addEventListener('click', downloadBoleto);
    
    // Define a data de vencimento (3 dias no futuro)
    const dueDate = new Date();
    dueDate.setDate(dueDate.getDate() + 3);
    document.getElementById('boletoDueDate').textContent = dueDate.toLocaleDateString('pt-BR');
}

// Copia o código do boleto
function copyBoletoCode() {
    navigator.clipboard.writeText(boletoCode).then(() => {
        const btn = document.getElementById('copyBoletoCode');
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        
        setTimeout(() => {
            btn.innerHTML = '<i class="far fa-copy"></i> Copiar';
        }, 2000);
    });
}

// Imprime o boleto
function printBoleto() {
    // Aqui você poderia implementar a geração de um PDF ou abrir uma janela de impressão
    alert('Funcionalidade de impressão seria implementada aqui');
}

// Baixa o boleto como PDF
function downloadBoleto() {
    // Aqui você poderia implementar o download de um PDF gerado
    alert('Funcionalidade de download seria implementada aqui');
}