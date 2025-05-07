document.addEventListener('DOMContentLoaded', function() {

  const inputFoto = document.getElementById('input-foto');
  const fotoPerfil = document.getElementById('foto-perfil');
  const salvarBtn = document.getElementById('salvar-btn');
  const editarBtn = document.getElementById('editar-btn');
  const inputs = document.querySelectorAll('.informacoes-usuario input');

  function bloquearCampos() {
    inputs.forEach(input => input.disabled = true);
  }

  function destravarCampos() {
    inputs.forEach(input => input.disabled = false);
  }

  // Função de Validação
  function validarCampos() {
    const nome = document.getElementById('nome').value.trim();
    const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
    const email = document.getElementById('email').value.trim();
    const telefone = document.getElementById('telefone').value.replace(/\D/g, '');
    const endereco = document.getElementById('endereco').value.trim();

    if (nome.length < 3) {
      alert('Por favor, preencha um nome válido (mínimo 3 letras).');
      return false;
    }
    if (cpf.length !== 11) {
      alert('CPF inválido. Deve conter 11 números.');
      return false;
    }
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
      alert('Email inválido.');
      return false;
    }
    if (telefone.length < 10 || telefone.length > 11) {
      alert('Número de telefone inválido. Deve ter 10 ou 11 números.');
      return false;
    }
    if (endereco.length < 5) {
      alert('Por favor, preencha um endereço válido.');
      return false;
    }
    return true;
  }

  function salvarInformacoes() {
    if (!validarCampos()) {
      return; // Se não passar na validação, não salva
    }

    const dados = {
      nome: document.getElementById('nome').value,
      cpf: document.getElementById('cpf').value,
      email: document.getElementById('email').value,
      telefone: document.getElementById('telefone').value,
      endereco: document.getElementById('endereco').value
    };
    localStorage.setItem('dadosPerfil', JSON.stringify(dados));
    alert('Informações salvas com sucesso!');
    bloquearCampos();
    salvarBtn.disabled = true;
  }

  function carregarInformacoes() {
    const dados = JSON.parse(localStorage.getItem('dadosPerfil'));
    if (dados) {
      document.getElementById('nome').value = dados.nome || '';
      document.getElementById('cpf').value = dados.cpf || '';
      document.getElementById('email').value = dados.email || '';
      document.getElementById('telefone').value = dados.telefone || '';
      document.getElementById('endereco').value = dados.endereco || '';
    }
    bloquearCampos();
    salvarBtn.disabled = true;
  }

  salvarBtn.addEventListener('click', salvarInformacoes);

  editarBtn.addEventListener('click', function() {
    destravarCampos();
    salvarBtn.disabled = false;
  });

  // Upload de imagem de perfil
  inputFoto.addEventListener('change', function () {
    const file = this.files[0];
    if (file) {
      const reader = new FileReader();
      reader.onload = function (e) {
        fotoPerfil.src = e.target.result;
        localStorage.setItem('fotoPerfil', e.target.result);
      };
      reader.readAsDataURL(file);
    }
  });

  const imagemSalva = localStorage.getItem('fotoPerfil');
  if (imagemSalva) {
    fotoPerfil.src = imagemSalva;
  }

  carregarInformacoes();

  // Máscaras
  aplicarMascaraCPF(document.getElementById('cpf'));
  aplicarMascaraTelefone(document.getElementById('telefone'));

});

// Máscara CPF
function aplicarMascaraCPF(cpfInput) {
  cpfInput.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    value = value.replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d)/, '$1.$2')
                 .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    this.value = value;
  });
}

// Máscara Telefone
function aplicarMascaraTelefone(telefoneInput) {
  telefoneInput.addEventListener('input', function () {
    let value = this.value.replace(/\D/g, '');
    if (value.length > 11) value = value.slice(0, 11);
    if (value.length <= 10) {
      value = value.replace(/(\d{2})(\d)/, '($1) $2')
                   .replace(/(\d{4})(\d)/, '$1-$2');
    } else {
      value = value.replace(/(\d{2})(\d)/, '($1) $2')
                   .replace(/(\d{5})(\d)/, '$1-$2');
    }
    this.value = value;
  });
}
