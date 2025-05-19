document.addEventListener('DOMContentLoaded', function() {
  // Máscaras para formulários
  if (document.getElementById('cpf')) {
      IMask(document.getElementById('cpf'), {
          mask: '000.000.000-00'
      });
  }

  if (document.getElementById('phone')) {
      IMask(document.getElementById('phone'), {
          mask: '(00) 00000-0000'
      });
  }

  if (document.getElementById('cardNumber')) {
      IMask(document.getElementById('cardNumber'), {
          mask: '0000 0000 0000 0000'
      });
  }

  if (document.getElementById('cardExpiry')) {
      IMask(document.getElementById('cardExpiry'), {
          mask: 'MM/YY',
          blocks: {
              MM: {
                  mask: IMask.MaskedRange,
                  from: 1,
                  to: 12
              },
              YY: {
                  mask: IMask.MaskedRange,
                  from: 0,
                  to: 99
              }
          }
      });
  }

  if (document.getElementById('cardCvv')) {
      IMask(document.getElementById('cardCvv'), {
          mask: '000'
      });
  }

  // Validação do formulário
  const paymentForm = document.getElementById('paymentForm');
  if (paymentForm) {
      paymentForm.addEventListener('submit', function(e) {
          let isValid = true;
          
          // Validação básica - em um sistema real, seria mais completa
          const requiredFields = paymentForm.querySelectorAll('[required]');
          requiredFields.forEach(field => {
              if (!field.value.trim()) {
                  field.classList.add('is-invalid');
                  isValid = false;
              } else {
                  field.classList.remove('is-invalid');
              }
          });

          if (!isValid) {
              e.preventDefault();
              alert('Por favor, preencha todos os campos obrigatórios.');
          }
      });
  }

  // Alternar entre métodos de pagamento
  const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
  if (paymentMethods.length > 0) {
      paymentMethods.forEach(method => {
          method.addEventListener('change', function() {
              const creditCardForm = document.getElementById('creditCardForm');
              const pixForm = document.getElementById('pixForm');
              
              if (this.value === 'credit_card') {
                  creditCardForm.style.display = 'block';
                  pixForm.style.display = 'none';
              } else {
                  creditCardForm.style.display = 'none';
                  pixForm.style.display = 'block';
              }
          });
      });
  }
});