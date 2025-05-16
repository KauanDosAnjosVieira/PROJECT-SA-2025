let pixKey;
let pixTimer;
let pixTimeLeft = 300; // 5 minutos em segundos

// Inicializa o PIX
function initPIX() {
    generatePIXKey();
    setupPIXEvents();
}

// Gera uma chave PIX aleatória
function generatePIXKey() {
    const chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
    let key = '';
    
    for (let i = 0; i < 36; i++) {
        if ([8, 13, 18, 23].includes(i)) {
            key += '-';
        } else {
            key += chars.charAt(Math.floor(Math.random() * chars.length));
        }
    }
    
    pixKey = key;
    document.getElementById('pixKey').value = key;
    
    // Gera o QR Code
    generateQRCode();
}

// Gera o QR Code
function generateQRCode() {
    const qrContainer = document.getElementById('pixQrCode');
    qrContainer.innerHTML = '';
    
    new QRCode(qrContainer, {
        text: `00020126580014BR.GOV.BCB.PIX0136${pixKey}5204000053039865405150.005802BR5913MERCADO PAGO6009SAO PAULO62070503***6304`,
        width: 192,
        height: 192,
        colorDark: "#000000",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
}

// Configura eventos do PIX
function setupPIXEvents() {
    // Botão copiar chave
    document.getElementById('copyPixKey').addEventListener('click', copyPIXKey);
    
    // Inicia o timer quando a seção PIX é mostrada
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (!mutation.target.classList.contains('hidden')) {
                startPIXTimer();
            } else {
                resetPIXTimer();
            }
        });
    });
    
    observer.observe(document.querySelector('.pix-section'), {
        attributes: true,
        attributeFilter: ['class']
    });
}

// Copia a chave PIX
function copyPIXKey() {
    navigator.clipboard.writeText(pixKey).then(() => {
        const btn = document.getElementById('copyPixKey');
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        
        setTimeout(() => {
            btn.innerHTML = '<i class="far fa-copy"></i> Copiar';
        }, 2000);
    });
}

// Inicia o timer do PIX
function startPIXTimer() {
    clearInterval(pixTimer);
    pixTimeLeft = 300;
    updatePIXTimerDisplay();
    
    pixTimer = setInterval(() => {
        pixTimeLeft--;
        updatePIXTimerDisplay();
        
        if (pixTimeLeft <= 0) {
            clearInterval(pixTimer);
            generatePIXKey();
            startPIXTimer();
        }
    }, 1000);
}

// Reseta o timer do PIX
function resetPIXTimer() {
    clearInterval(pixTimer);
    pixTimeLeft = 300;
    updatePIXTimerDisplay();
}

// Atualiza o display do timer
function updatePIXTimerDisplay() {
    const minutes = Math.floor(pixTimeLeft / 60);
    const seconds = pixTimeLeft % 60;
    document.getElementById('pixTimer').textContent = 
        `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    
    // Muda a cor quando está acabando o tempo
    if (pixTimeLeft <= 60) {
        document.querySelector('.expiry-timer').style.color = '#f72585';
    } else if (pixTimeLeft <= 120) {
        document.querySelector('.expiry-timer').style.color = '#f8961e';
    } else {
        document.querySelector('.expiry-timer').style.color = '';
    }
}