function login(event) {
    event.preventDefault(); // Impede o envio tradicional do formulário

    const user = document.getElementById('email').value;
    const password = document.getElementById('senha').value;

    if (user && password) {
        alert('Login bem-sucedido!');

        // Salva que o usuário está logado
        localStorage.setItem('usuarioLogado', 'true');
        localStorage.setItem('nomeUsuario', user);

        // Redireciona para a página inicial
        window.location.href = '../inicial/index.html';

    } else {
        alert('Por favor, preencha todos os campos.');
    }
}
