function login(event) {
    event.preventDefault(); // impede o envio do formulário

    const user = document.getElementById('email').value;
    const password = document.getElementById('senha').value;

    if (user && password) {
        alert('Login bem-sucedido!');
        window.location.href = '../inicial/index.html'; // redireciona para a página inicial
    } else {
        alert('Por favor, preencha todos os campos.');
    }
}

function togglePasswordVisibility(id) {
    const input = document.getElementById(id);
    if (input.type === "password") {
        input.type = "text";
    } else {
        input.type = "password";
    }
}
