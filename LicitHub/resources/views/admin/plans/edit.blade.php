@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Editar Plano: {{ $plan->name }}</h3>
    </div>
    <div class="card-body">
        <form action="{{ route('plans.update', $plan) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Nome do Plano*</label>
                        <input type="text" name="name" id="name" class="form-control" value="{{ $plan->name }}" required>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="slug">Slug*</label>
                        <input type="text" name="slug" id="slug" class="form-control" value="{{ $plan->slug }}" required>
                        <small class="text-muted">Identificador único para o plano</small>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="description">Descrição</label>
                <textarea name="description" id="description" class="form-control" rows="3">{{ $plan->description }}</textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="price">Preço* (R$)</label>
                        <input type="number" name="price" id="price" class="form-control" step="0.01" min="0" 
                               value="{{ $plan->price }}" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="interval">Intervalo*</label>
                        <select name="interval" id="interval" class="form-control" required>
                            <option value="month" {{ $plan->interval == 'month' ? 'selected' : '' }}>Mensal</option>
                            <option value="year" {{ $plan->interval == 'year' ? 'selected' : '' }}>Anual</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="trial_days">Dias de Trial</label>
                        <input type="number" name="trial_days" id="trial_days" class="form-control" min="0" 
                               value="{{ $plan->trial_days }}">
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="stripe_price_id">ID do Preço no Stripe</label>
                <input type="text" name="stripe_price_id" id="stripe_price_id" class="form-control" 
                       value="{{ $plan->stripe_price_id }}">
            </div>
            
            <div class="form-group">
                <div class="custom-control custom-switch">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" class="custom-control-input" id="is_active" name="is_active" value="1" 
                           {{ $plan->is_active ? 'checked' : '' }}>
                    <label class="custom-control-label" for="is_active">Plano Ativo</label>
                </div>
            </div>
            
            <div class="form-group">
                <label>Recursos do Plano</label>
                <div id="features-container">
                @php
                    $features = is_array($plan->features) ? $plan->features : explode(',', $plan->features);
                @endphp 

                @if(!empty($features) && count($features) > 0)
                    @foreach($features as $feature)

                            <div class="input-group mb-2">
                                <input type="text" name="features[]" class="form-control" value="{{ $feature }}">
                                <div class="input-group-append">
                                    <button class="btn btn-outline-danger remove-feature" type="button">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="input-group mb-2">
                            <input type="text" name="features[]" class="form-control" placeholder="Recurso incluído">
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary add-feature" type="button">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
            
            <button type="submit" class="btn btn-primary">Atualizar Plano</button>
            <a href="{{ route('plans.index') }}" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Adicionar novo campo de recurso
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('add-feature')) {
                const container = document.getElementById('features-container');
                const newInput = document.createElement('div');
                newInput.className = 'input-group mb-2';
                newInput.innerHTML = `
                    <input type="text" name="features[]" class="form-control" placeholder="Recurso incluído">
                    <div class="input-group-append">
                        <button class="btn btn-outline-danger remove-feature" type="button">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                `;
                container.appendChild(newInput);
            }
            
            // Remover campo de recurso
            if (e.target.classList.contains('remove-feature')) {
                e.target.closest('.input-group').remove();
            }
        });
    });
</script>
@endpush