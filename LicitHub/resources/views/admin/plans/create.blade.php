<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Cadastrar Plano</title>
    @vite(['resources/css/admin.css'])
</head>
<body class="light-mode">
    <main class="content-wrapper">
        <div class="page-header">
            <h1 class="page-title">Cadastrar Novo Plano</h1>
        </div>

        <form action="{{ route('plans.store') }}" method="POST" style="max-width: 800px;">
            @csrf

            <div class="form-group">
                <label for="name">Nome do Plano*</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="slug">Slug*</label>
                <input type="text" name="slug" id="slug" class="form-control" required>
                <small class="text-muted">Identificador único (ex: plano-basico)</small>
            </div>

            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" class="form-control" rows="3"></textarea>
            </div>

            <div class="form-group">
                <label for="price">Preço* (R$)</label>
                <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="interval">Intervalo*</label>
                <select name="interval" id="interval" class="form-control" required>
                    <option value="month">Mensal</option>
                    <option value="year">Anual</option>
                </select>
            </div>

            <div class="form-group">
                <label for="trial_days">Dias de Trial</label>
                <input type="number" name="trial_days" id="trial_days" class="form-control" min="0" value="0">
            </div>

            <div class="form-group">
                <label for="stripe_price_id">ID do Preço no Stripe</label>
                <input type="text" name="stripe_price_id" id="stripe_price_id" class="form-control">
            </div>

            <div class="form-group">
                <label>
                    <input type="checkbox" name="is_active" value="1" checked>
                    Plano Ativo
                </label>
            </div>

            <div class="form-group">
                <label>Recursos Ativos (features)</label>
                <div id="features-container">
                    <div class="input-group mb-2">
                        <input type="text" name="features[]" class="form-control" placeholder="Recurso incluído">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary add-feature" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label>Recursos Bloqueados (features_off)</label>
                <div id="features-off-container">
                    <div class="input-group mb-2">
                        <input type="text" name="features_off[]" class="form-control" placeholder="Recurso bloqueado">
                        <div class="input-group-append">
                            <button class="btn btn-outline-secondary add-feature-off" type="button">+</button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-group" style="margin-top: 1rem;">
                <button type="submit" class="btn btn-success">Salvar Plano</button>
                <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
            </div>
        </form>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelector('.add-feature').addEventListener('click', function () {
                const container = document.getElementById('features-container');
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="features[]" class="form-control" placeholder="Recurso incluído">
                    <div class="input-group-append">
                        <button class="btn btn-danger remove-feature" type="button">-</button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            document.querySelector('.add-feature-off').addEventListener('click', function () {
                const container = document.getElementById('features-off-container');
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="features_off[]" class="form-control" placeholder="Recurso bloqueado">
                    <div class="input-group-append">
                        <button class="btn btn-danger remove-feature-off" type="button">-</button>
                    </div>
                `;
                container.appendChild(newInput);
            });

            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-feature')) {
                    e.target.closest('.input-group').remove();
                }
                if (e.target.classList.contains('remove-feature-off')) {
                    e.target.closest('.input-group').remove();
                }
            });
        });
    </script>
</body>
</html>
