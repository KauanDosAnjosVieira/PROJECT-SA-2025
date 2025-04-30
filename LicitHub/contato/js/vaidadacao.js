function voltarPagina() {
    window.history.back();
  }
  
  // Bloquear números no campo Nome
  document.getElementById('nome').addEventListener('input', function(e) {
    this.value = this.value.replace(/[0-9]/g, '');
  });
  
  // Bloquear letras no campo Número da residência
  document.getElementById('numero').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
  });
  
  // Máscara CPF
  document.getElementById('cpf').addEventListener('input', function (e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    if (value.length > 9)
      e.target.value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, '$1.$2.$3-$4');
    else if (value.length > 6)
      e.target.value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, '$1.$2.$3');
    else if (value.length > 3)
      e.target.value = value.replace(/(\d{3})(\d{1,3})/, '$1.$2');
    else
      e.target.value = value;
  });
  
  // Validação do formulário
  document.getElementById('form-contato').addEventListener('submit', function(e) {
    e.preventDefault();
  
    const nome = document.getElementById('nome').value.trim();
    let cpf = document.getElementById('cpf').value.trim();
    const email = document.getElementById('email').value.trim();
    const endereco = document.getElementById('endereco').value.trim();
    const numero = document.getElementById('numero').value.trim();
    const assunto = document.getElementById('assunto').value.trim();
    const mensagem = document.getElementById('mensagem').value.trim();
  
    cpf = cpf.replace(/\D/g, '');
  
    if (!nome || !cpf || !email || !endereco || !numero || !assunto || !mensagem) {
      alert("Por favor, preencha todos os campos.");
      return;
    }
  
    if (cpf.length !== 11) {
      alert("CPF inválido. Digite apenas 11 números.");
      return;
    }
  
    if (!email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
      alert("Digite um e-mail válido.");
      return;
    }
  
    if (!numero.match(/^\d+$/)) {
      alert("O número do endereço deve conter apenas números.");
      return;
    }
  
    alert("Mensagem enviada com sucesso!");
    document.getElementById('form-contato').reset();
  });
  
  function toggleContato() {
    const info = document.getElementById('info-contato');
    if (info.style.display === 'none') {
      info.style.display = 'block';
    } else {
      info.style.display = 'none';
    }
  }

  function toggleContatoLateral() {
    const painel = document.getElementById('painel-contato');
    if (painel.style.display === 'none' || painel.style.display === '') {
      painel.style.display = 'block';
    } else {
      painel.style.display = 'none';
    }
  }