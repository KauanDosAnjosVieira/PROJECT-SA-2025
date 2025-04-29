function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}

// Validação do formulário ao ser enviado
document.getElementById('register-form').addEventListener('submit', function(event) {
    const senha = document.getElementById('password').value;
    const confirmarSenha = document.getElementById('confirm-password').value;

    if (senha !== confirmarSenha) {
        alert("As senhas não coincidem. Por favor, insira as mesmas senhas.");
        event.preventDefault(); // Impede o envio se as senhas forem diferentes
        return false;
    }

    if (senha.length < 6) {
        alert("A senha deve ter pelo menos 6 caracteres.");
        event.preventDefault();
        return false;
    }

    // Se tudo estiver certo, o formulário é enviado normalmente
});
