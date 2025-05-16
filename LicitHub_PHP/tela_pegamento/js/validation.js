// Inicializa a validação
function initValidation() {
    // Formata o número do cartão
    document.getElementById('cardNumber').addEventListener('input', formatCardNumber);
    
    // Validação em tempo real
    document.getElementById('cardName').addEventListener('blur', validateCardName);
    document.getElementById('cardNumber').addEventListener('blur', validateCardNumber);
    document.getElementById('cardMonth').addEventListener('blur', validateExpiry);
    document.getElementById('cardYear').addEventListener('blur', validateExpiry);
    document.getElementById('cardCvc').addEventListener('blur', validateCvc);
    
    // Atualiza a visualização do cartão
    document.getElementById('cardName').addEventListener('input', updateCardPreview);
    document.getElementById('cardNumber').addEventListener('input', updateCardPreview);
    document.getElementById('cardMonth').addEventListener('input', updateCardPreview);
    document.getElementById('cardYear').addEventListener('input', updateCardPreview);
    document.getElementById('cardCvc').addEventListener('input', updateCardPreview);
}

// Formata o número do cartão (adiciona espaços a cada 4 dígitos)
function formatCardNumber(e) {
    let value = e.target.value.replace(/\s+/g, '').replace(/[^0-9]/gi, '');
    let formatted = '';
    
    for (let i = 0; i < value.length; i++) {
        if (i > 0 && i % 4 === 0) formatted += ' ';
        formatted += value[i];
    }
    
    e.target.value = formatted;
    detectCardBrand(value);
}

// Detecta a bandeira do cartão
function detectCardBrand(cardNumber) {
    const brands = {
        visa: /^4/,
        mastercard: /^5[1-5]/,
        amex: /^3[47]/,
        elo: /^(4011|4312|4389|4514|4573|5041|5067|5090|6277|6362|6363|6500|6504|6505|6507|6509|6516|6550)/,
        hipercard: /^(3841|6062)/,
        diners: /^3(?:0[0-5]|[68])/
    };
    
    const container = document.querySelector('.card-brands');
    container.innerHTML = '';
    
    for (const [brand, pattern] of Object.entries(brands)) {
        if (pattern.test(cardNumber)) {
            const img = document.createElement('div');
            img.className = `card-brand ${brand} active`;
            img.style.backgroundImage = `url('img/card-brands/${brand}.svg')`;
            container.appendChild(img);
            return;
        }
    }
    
    // Se não encontrou nenhuma bandeira conhecida
    const unknown = document.createElement('div');
    unknown.className = 'card-brand unknown active';
    unknown.textContent = '••••';
    container.appendChild(unknown);
}

// Valida o nome no cartão
function validateCardName() {
    const input = document.getElementById('cardName');
    const errorElement = input.nextElementSibling;
    const value = input.value.trim();
    
    if (value === '' || value.length < 3) {
        errorElement.textContent = 'Por favor, insira o nome como no cartão';
        errorElement.style.display = 'block';
        input.classList.add('error');
        return false;
    }
    
    errorElement.style.display = 'none';
    input.classList.remove('error');
    return true;
}

// Valida o número do cartão
function validateCardNumber() {
    const input = document.getElementById('cardNumber');
    const errorElement = input.nextElementSibling;
    const value = input.value.replace(/\s/g, '');
    
    if (value.length < 16 || !luhnCheck(value)) {
        errorElement.textContent = 'Número de cartão inválido';
        errorElement.style.display = 'block';
        input.classList.add('error');
        return false;
    }
    
    errorElement.style.display = 'none';
    input.classList.remove('error');
    return true;
}

// Algoritmo de Luhn para validar número de cartão
function luhnCheck(cardNumber) {
    let sum = 0;
    let shouldDouble = false;
    
    for (let i = cardNumber.length - 1; i >= 0; i--) {
        let digit = parseInt(cardNumber.charAt(i));
        
        if (shouldDouble) {
            if ((digit *= 2) > 9) digit -= 9;
        }
        
        sum += digit;
        shouldDouble = !shouldDouble;
    }
    
    return (sum % 10) === 0;
}

// Valida a data de expiração
function validateExpiry() {
    const monthInput = document.getElementById('cardMonth');
    const yearInput = document.getElementById('cardYear');
    const errorElement = document.querySelector('.expiry-fields').nextElementSibling;
    
    const month = parseInt(monthInput.value);
    const year = parseInt(yearInput.value);
    const currentYear = new Date().getFullYear() % 100;
    const currentMonth = new Date().getMonth() + 1;
    
    let isValid = true;
    
    if (isNaN(month) || month < 1 || month > 12) {
        monthInput.classList.add('error');
        isValid = false;
    } else {
        monthInput.classList.remove('error');
    }
    
    if (isNaN(year) || year < currentYear || year > currentYear + 20) {
        yearInput.classList.add('error');
        isValid = false;
    } else {
        yearInput.classList.remove('error');
    }
    
    // Verifica se a data já expirou
    if (isValid && (year === currentYear && month < currentMonth)) {
        isValid = false;
        monthInput.classList.add('error');
        yearInput.classList.add('error');
    }
    
    if (!isValid) {
        errorElement.textContent = 'Data de expiração inválida';
        errorElement.style.display = 'block';
        return false;
    }
    
    errorElement.style.display = 'none';
    return true;
}

// Valida o CVC
function validateCvc() {
    const input = document.getElementById('cardCvc');
    const errorElement = input.nextElementSibling;
    const value = input.value;
    
    if (value.length !== 3 || !/^\d+$/.test(value)) {
        errorElement.textContent = 'Código de segurança inválido';
        errorElement.style.display = 'block';
        input.classList.add('error');
        return false;
    }
    
    errorElement.style.display = 'none';
    input.classList.remove('error');
    return true;
}

// Valida todo o formulário do cartão
function validateCreditCard() {
    const isNameValid = validateCardName();
    const isNumberValid = validateCardNumber();
    const isExpiryValid = validateExpiry();
    const isCvcValid = validateCvc();
    
    return isNameValid && isNumberValid && isExpiryValid && isCvcValid;
}

// Atualiza a visualização do cartão
function updateCardPreview() {
    // Nome
    const cardName = document.getElementById('cardName').value || 'NOME NO CARTÃO';
    document.querySelector('.card-name').textContent = cardName.toUpperCase();
    
    // Número
    const cardNumber = document.getElementById('cardNumber').value || '•••• •••• •••• ••••';
    document.querySelector('.card-number').textContent = cardNumber;
    
    // Data
    const month = document.getElementById('cardMonth').value || 'MM';
    const year = document.getElementById('cardYear').value || 'AA';
    document.querySelector('.card-expiry').textContent = `${month}/${year}`;
    
    // CVC
    const cvc = document.getElementById('cardCvc').value || '•••';
    document.querySelector('.card-cvc').textContent = cvc;
}