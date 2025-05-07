function register(event) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    const email = document.getElementById('email').value;
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm-password').value;

    if (email && password && confirmPassword) {
        if (password !== confirmPassword) {
            alert('As senhas não coincidem.');
            return;
        }

        alert('Cadastro realizado com sucesso!');

        // Após cadastrar, redireciona para login (não loga automático)
        window.location.href = 'login.html';
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}

function redirectToLogin() {
    window.location.href = 'login.html';
}

function togglePasswordVisibility(fieldId) {
    const passwordField = document.getElementById(fieldId);
    const toggleIcon = passwordField.nextElementSibling;
    const isPassword = passwordField.type === "password";

    passwordField.type = isPassword ? "text" : "password";
    toggleIcon.classList.toggle('show', !isPassword);
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
