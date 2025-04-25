document.addEventListener('DOMContentLoaded', function() {

  //Salvar imagem de perfil
  const inputFoto = document.getElementById('input-foto');
const fotoPerfil = document.getElementById('foto-perfil');

inputFoto.addEventListener('change', function () {
  const file = this.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = function (e) {
      fotoPerfil.src = e.target.result;
      localStorage.setItem('fotoPerfil', e.target.result); // Salva no localStorage
    };
    reader.readAsDataURL(file);
  }
});

// Carrega a imagem do localStorage ao iniciar
const imagemSalva = localStorage.getItem('fotoPerfil');
if (imagemSalva) {
  fotoPerfil.src = imagemSalva;
}

  // Botões
  const perfilBtn = document.getElementById('perfil-btn');
  const configuracoesBtn = document.getElementById('configuracoes-btn');
  const sairBtn = document.getElementById('sair-btn');

  // Seções
  const perfilSection = document.getElementById('perfil');
  const configuracoesSection = document.getElementById('configuracoes');
  const sairSection = document.getElementById('sair');

  // Função para mudar a seção ativa
  function mostrarSeção(seçãoAtiva) {
    // Esconde todas as seções
    perfilSection.style.display = 'none';
    configuracoesSection.style.display = 'none';
    sairSection.style.display = 'none';

    // Exibe a seção selecionada
    seçãoAtiva.style.display = 'block';
  }

  // Exibe a seção de Perfil por padrão
  mostrarSeção(perfilSection);

  // Evento para o botão de "Perfil"
  perfilBtn.addEventListener('click', function() {
    mostrarSeção(perfilSection);
  });

  // Evento para o botão de "Configurações"
  configuracoesBtn.addEventListener('click', function() {
    mostrarSeção(configuracoesSection);
  });

  // Evento para o botão de "Sair"
  sairBtn.addEventListener('click', function() {
    mostrarSeção(sairSection);
  });

 
  });
  


function salvarInformacoes() {
  const dados = {
    nome: document.getElementById('nome').value,
    cpf: document.getElementById('cpf').value,
    email: document.getElementById('email').value,
    telefone: document.getElementById('telefone').value,
    endereco: document.getElementById('endereco').value
  };
  localStorage.setItem('dadosPerfil', JSON.stringify(dados));
  alert('Informações salvas com sucesso!');
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
}


document.getElementById('salvar-btn').addEventListener('click', salvarInformacoes);
carregarInformacoes();


