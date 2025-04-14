function sendResetLink() {
    const email = document.getElementById('email').value;
    if (email) {
        alert('Um link de redefinição de senha foi enviado para o seu email!');
    } else {
        alert('Por favor, insira seu email.');
    }
}
