const etapas = [
    'step-plano',
    'step-forma-pagamento',
    'step-pagamento-cartao',
    'step-pagamento-boleto',
    'step-pagamento-pix',
    'step-confirmar',
    'step-senha',
    'step-finalizacao'
  ];
  
  const stepIndicators = [
    document.getElementById('step-indicator-1'),
    document.getElementById('step-indicator-2'),
    document.getElementById('step-indicator-3'),
    document.getElementById('step-indicator-4'),
    document.getElementById('step-indicator-5'),
    document.getElementById('step-indicator-6')
  ];
  
  const form = document.getElementById('checkout-form');
  
  let etapaAtual = 0;
  
  function esconderTodasEtapas() {
    etapas.forEach(id => {
      const el = document.getElementById(id);
      if (el) el.style.display = 'none';
    });
    stepIndicators.forEach(el => el.classList.remove('active'));
  }
  
  function mostrarEtapa(indice) {
    esconderTodasEtapas();
  
    if (indice === 2) {
      document.getElementById('step-pagamento-cartao').style.display = 'block';
      stepIndicators[2].classList.add('active');
    } else if (indice === 3) {
      document.getElementById('step-pagamento-boleto').style.display = 'block';
      stepIndicators[2].classList.add('active');
    } else if (indice === 4) {
      document.getElementById('step-pagamento-pix').style.display = 'block';
      stepIndicators[2].classList.add('active');
    } else if (indice === 6) {
      document.getElementById('step-senha').style.display = 'block';
      stepIndicators[4].classList.add('active');
    } else {
      const id = etapas[indice];
      if (id) {
        document.getElementById(id).style.display = 'block';
        if (indice === 0) stepIndicators[0].classList.add('active');
        if (indice === 1) stepIndicators[1].classList.add('active');
        if (indice === 5) stepIndicators[3].classList.add('active');
        if (indice === 7) stepIndicators[5].classList.add('active');
      }
    }
  }
  
  // Função para preencher resumo na etapa de confirmação
  function preencherResumo() {
    const resumo = document.getElementById('resumo-pagamento');
    const forma = form['forma-pagamento'].value;
  
    let detalhes = `<p><strong>Plano:</strong> Plano Mensal</p>`;
    detalhes += `<p><strong>Valor:</strong> R$ 49,90</p>`;
    detalhes += `<p><strong>Forma de pagamento:</strong> ${forma === 'cartao' ? 'Cartão de Crédito' : (forma === 'boleto' ? 'Boleto Bancário' : 'PIX')}</p>`;
  
    if (forma === 'cartao') {
      detalhes += `<p><strong>Nome no cartão:</strong> ${form['nome-cartao'].value}</p>`;
      detalhes += `<p><strong>Número do cartão:</strong> ${form['numero-cartao'].value.replace(/\d{4}(?=.)/g, '**** ')}</p>`;
      detalhes += `<p><strong>Validade:</strong> ${form['validade-cartao'].value}</p>`;
    }
  
    resumo.innerHTML = detalhes;
  }
  
  function validarEtapa() {
    if (etapaAtual === 0) {
      return true; // não há formulário para validar
    }
  
    if (etapaAtual === 1) {
      // validar se forma de pagamento foi selecionada
      return !!form['forma-pagamento'].value;
    }
  
    if (etapaAtual === 2) {
      // validar dados do cartão
      return form['nome-cartao'].checkValidity() &&
             form['numero-cartao'].checkValidity() &&
             form['validade-cartao'].checkValidity() &&
             form['cvv-cartao'].checkValidity();
    }
  
    if (etapaAtual === 6) {
      // validar senha
      return form['senha-pagamento'].checkValidity();
    }
  
    return true;
  }
  
  function proximaEtapa() {
    if (!validarEtapa()) {
      alert('Por favor, preencha os dados corretamente antes de continuar.');
      return;
    }
  
    if (etapaAtual === 1) {
      const forma = form['forma-pagamento'].value;
      if (forma === 'cartao') {
        etapaAtual = 2;
      } else if (forma === 'boleto') {
        etapaAtual = 3;
      } else if (forma === 'pix') {
        etapaAtual = 4;
      }
    } else if (etapaAtual === 3 || etapaAtual === 4) {
      etapaAtual = 5;
      preencherResumo();
    } else if (etapaAtual === 5) {
      const forma = form['forma-pagamento'].value;
      if (forma === 'cartao') {
        etapaAtual = 6;
      } else {
        etapaAtual = 7;
      }
    } else if (etapaAtual === 6) {
      // Finaliza pagamento cartão
      etapaAtual = 7;
    } else {
      etapaAtual++;
    }
    mostrarEtapa(etapaAtual);
  }
  
  function voltarEtapa() {
    if (etapaAtual === 2 || etapaAtual === 3 || etapaAtual === 4) {
      etapaAtual = 1;
    } else if (etapaAtual === 5) {
      const forma = form['forma-pagamento'].value;
      if (forma === 'cartao') {
        etapaAtual = 2;
      } else if (forma === 'boleto') {
        etapaAtual = 3;
      } else {
        etapaAtual = 4;
      }
    } else if (etapaAtual === 6) {
      etapaAtual = 5;
    } else if (etapaAtual === 1) {
      etapaAtual = 0;
    }
    mostrarEtapa(etapaAtual);
  }
  
  // Inicialização e listeners
  
  document.getElementById('btn-proximo-plano').addEventListener('click', () => {
    etapaAtual = 1;
    mostrarEtapa(etapaAtual);
  });
  
  document.getElementById('btn-voltar-forma').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-proximo-forma').addEventListener('click', () => {
    proximaEtapa();
  });
  
  document.getElementById('btn-voltar-cartao').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-proximo-cartao').addEventListener('click', () => {
    proximaEtapa();
  });
  
  document.getElementById('btn-voltar-boleto').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-proximo-boleto').addEventListener('click', () => {
    proximaEtapa();
  });
  
  document.getElementById('btn-voltar-pix').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-proximo-pix').addEventListener('click', () => {
    proximaEtapa();
  });
  
  document.getElementById('btn-voltar-confirmar').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-proximo-confirmar').addEventListener('click', () => {
    proximaEtapa();
  });
  
  document.getElementById('btn-voltar-senha').addEventListener('click', () => {
    voltarEtapa();
  });
  document.getElementById('btn-finalizar').addEventListener('click', () => {
    if (!validarEtapa()) {
      alert('Por favor, digite sua senha corretamente.');
      return;
    }
    proximaEtapa();
  });
  
  // Mostrar etapa inicial
  mostrarEtapa(etapaAtual);
  