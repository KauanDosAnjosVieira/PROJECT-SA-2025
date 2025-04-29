
  document.addEventListener("DOMContentLoaded", function () {
    const planoSelect = document.getElementById("plano");
    const resumoPlano = document.querySelector(".purchase-summary .summary-item span strong");
    const resumoPreco = document.getElementById("resumo-preco");
  
    // Função para atualizar o nome do plano e o preço
    planoSelect.addEventListener("change", function () {
      const selectedOption = planoSelect.options[planoSelect.selectedIndex];
      const planoNome = selectedOption.textContent.split(" - ")[0]; // Obtém o nome do plano
      const planoPreco = selectedOption.value; // Preço do plano
  
      // Atualiza os elementos de resumo
      resumoPlano.textContent = planoNome;
      resumoPreco.textContent = `R$${planoPreco}`;
    });
  });

  document.addEventListener("DOMContentLoaded", function() {
    // Máscara para CPF
    Inputmask("999.999.999-99").mask(document.getElementById("cpf"));
    
    // Máscara para telefone
    Inputmask("(99) 99999-9999").mask(document.getElementById("telefone"));
  
    // Validação do formulário
    const form = document.querySelector('.payment-form');
  
    form.addEventListener('submit', function(event) {
      // Impede o envio do formulário se os campos obrigatórios não estiverem preenchidos corretamente
      const nome = document.getElementById("nome").value.trim();
      const cpf = document.getElementById("cpf").value.trim();
      const email = document.getElementById("email").value.trim();
      const telefone = document.getElementById("telefone").value.trim();
      const plano = document.getElementById("plano").value;
  
      if (!nome || !cpf || !email || !telefone || plano === "") {
        event.preventDefault();
        alert("Por favor, preencha todos os campos obrigatórios corretamente.");
        return false;
      }
      
      // Verificação de CPF e telefone corretamente preenchidos
      if (!validateCPF(cpf)) {
        event.preventDefault();
        alert("CPF inválido. Por favor, insira um CPF válido.");
        return false;
      }
      if (!validatePhone(telefone)) {
        event.preventDefault();
        alert("Telefone inválido. Por favor, insira um telefone válido.");
        return false;
      }
  
      return true;
    });
  
    // Função para validar CPF (apenas uma verificação simples)
    function validateCPF(cpf) {
      cpf = cpf.replace(/[^\d]+/g, '');
      if (cpf.length !== 11) return false;
      return true;
    }
  
    // Função para validar telefone (verifica se o número de telefone tem 11 dígitos)
    function validatePhone(phone) {
      phone = phone.replace(/[^\d]+/g, '');
      return phone.length === 11;
    }
  });
  