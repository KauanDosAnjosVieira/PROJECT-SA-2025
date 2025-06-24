document.addEventListener('DOMContentLoaded', function() {
    // Toggle sidebar on mobile
    const sidebar = document.getElementById('sidebar');
    const toggleBtn = document.getElementById('sidebar-toggle');
    
    toggleBtn.addEventListener('click', function() {
        sidebar.classList.toggle('show');
    });
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(e) {
        if (window.innerWidth < 992 && !sidebar.contains(e.target) && e.target !== toggleBtn) {
            sidebar.classList.remove('show');
        }
    });
    
    // Active menu item based on current URL
    const currentUrl = window.location.href;
    const menuItems = document.querySelectorAll('.menu-item a');
    
    menuItems.forEach(item => {
        if (item.href === currentUrl) {
            item.parentElement.classList.add('active');
        }
    });
});


    document.addEventListener('DOMContentLoaded', function() {
        const featuresContainer = document.getElementById('features-container');
        const addFeatureBtn = document.getElementById('add-feature-btn');
        
        // Adicionar novo campo de recurso
        addFeatureBtn.addEventListener('click', function() {
            const newInputGroup = document.createElement('div');
            newInputGroup.className = 'feature-input-group input-group mb-2';
            newInputGroup.innerHTML = `
                <input type="text" name="features[]" class="form-control" placeholder="Recurso incluído">
                <div class="input-group-append">
                    <button class="btn btn-outline-danger remove-feature" type="button" title="Remover recurso">
                        <i class="fas fa-minus"></i>
                    </button>
                </div>
            `;
            
            // Adiciona animação ao inserir
            newInputGroup.style.opacity = '0';
            featuresContainer.appendChild(newInputGroup);
            
            // Anima a entrada
            setTimeout(() => {
                newInputGroup.style.opacity = '1';
            }, 10);
        });
        
        // Remover campo de recurso (delegação de evento)
        featuresContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-feature')) {
                const inputGroup = e.target.closest('.feature-input-group');
                
                // Anima a saída antes de remover
                inputGroup.style.opacity = '0';
                inputGroup.style.height = '0';
                inputGroup.style.margin = '0';
                inputGroup.style.padding = '0';
                inputGroup.style.overflow = 'hidden';
                
                setTimeout(() => {
                    inputGroup.remove();
                }, 300);
            }
        });
        
        // Opcional: Focar automaticamente no novo campo adicionado
        $(document).on('click', '#add-feature-btn', function() {
            setTimeout(() => {
                const lastFeatureInput = $('.feature-input-group:last-child input');
                lastFeatureInput.focus();
            }, 310);
        });
    });
