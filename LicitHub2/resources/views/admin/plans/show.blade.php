@extends('layouts.admin')

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Detalhes do Plano: {{ $plan->name }}</h3>
        <div class="card-tools">
            <a href="{{ route('plans.edit', $plan) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Editar
            </a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="info-box">
                    <h5>Informações Básicas</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Nome:</strong>
                            <span>{{ $plan->name }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Slug:</strong>
                            <span>{{ $plan->slug }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Preço:</strong>
                            <span>R$ {{ number_format($plan->price, 2, ',', '.') }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Intervalo:</strong>
                            <span>{{ $plan->interval == 'month' ? 'Mensal' : 'Anual' }}</span>
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="info-box">
                    <h5>Configurações</h5>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Status:</strong>
                            <span class="badge {{ $plan->is_active ? 'badge-success' : 'badge-secondary' }}">
                                {{ $plan->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Trial:</strong>
                            <span>{{ $plan->trial_days }} dias</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>ID Stripe:</strong>
                            <span>{{ $plan->stripe_price_id ?? 'Não configurado' }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <strong>Assinantes:</strong>
                            <span class="badge badge-primary">{{ $plan->subscriptions()->count() }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-box">
                    <h5>Descrição</h5>
                    <p>{{ $plan->description ?? 'Nenhuma descrição fornecida.' }}</p>
                </div>
            </div>
        </div>
        
        @if($plan->features && count($plan->features) > 0)
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="info-box">
                    <h5>Recursos Incluídos</h5>
                    <ul class="list-group">
                        @foreach($plan->features as $feature)
                            <li class="list-group-item">
                                <i class="fas fa-check-circle text-success mr-2"></i> {{ $feature }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif
    </div>
    <div class="card-footer">
        <small class="text-muted">Criado em: {{ $plan->created_at->format('d/m/Y H:i') }}</small>
    </div>
</div>
@endsection 