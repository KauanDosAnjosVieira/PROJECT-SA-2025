function register() {
    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    if (email && password) {
        alert('Cadastro realizado com sucesso!');
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}

function redirectToLogin() {
    window.location.href = 'login.html';
}

function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = passwordField.nextElementSibling; // Seleciona o ícone ao lado do campo
    const isPassword = passwordField.type === "password";

    passwordField.type = isPassword ? "text" : "password";
    toggleIcon.classList.toggle('show', !isPassword); // Alterna a classe para mudar o ícone
}

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

function register() {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (password !== confirmPassword) {
        alert('As senhas não coincidem!');
        event.preventDefault(); // Impede envio
    }
}
