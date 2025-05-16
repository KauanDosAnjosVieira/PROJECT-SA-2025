document.addEventListener('DOMContentLoaded', function() {
    // Controle de navegação entre etapas
    const steps = document.querySelectorAll('.step');
    const sections = {
        '1': 'payment-methods-section',
        '2': 'summary-section',
        '3': 'confirmation-section'
    };

    function showStep(stepNumber) {
        // Esconde todas as seções
        document.querySelectorAll('.payment-section').forEach(section => {
            section.classList.add('hidden');
        });
        
        // Mostra a seção atual
        document.getElementById(sections[stepNumber]).classList.remove('hidden');
        
        // Atualiza a indicação visual
        steps.forEach((step, index) => {
            if (index + 1 <= stepNumber) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    // Controle dos métodos de pagamento
    document.querySelectorAll('.method-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.method-card').forEach(c => {
                c.classList.remove('selected');
            });
            this.classList.add('selected');
            
            // Mostra o formulário correspondente
            const method = this.dataset.method;
            document.querySelectorAll('.payment-form').forEach(form => {
                form.classList.add('hidden');
            });
            document.getElementById(`${method}-form`).classList.remove('hidden');
            
            showStep(2);
        });
    });

    // Botões de navegação
    document.getElementById('next-btn')?.addEventListener('click', () => {
        if (validateCurrentStep()) {
            showStep(currentStep + 1);
        }
    });
    
    document.getElementById('back-btn')?.addEventListener('click', () => {
        showStep(currentStep - 1);
    });

    // Inicia na primeira etapa
    showStep(1);
});