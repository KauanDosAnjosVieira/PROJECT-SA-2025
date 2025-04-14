
/* script.js */
function login() {
    const user = document.getElementById('user').value;
    const password = document.getElementById('password').value;
    if (user && password) {
        alert('Login bem-sucedido!');
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}
