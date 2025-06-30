<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard do Cliente</title>
    <!-- Você pode substituir por seu próprio CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Seu CSS personalizado aqui */
        .subscription-info {
            padding: 10px;
        }
        .subscription-details {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-top: 15px;
        }
        .card-header {
            font-weight: 600;
        }
    </style>
</head>
<body>
    @php
        $user = auth()->user();
        if (!($user->user_type === 'customer' && $user->hasActiveCashierSubscription())) {
            header('Location: ' . url('/'));
            exit;
        }

        // Obter dados da assinatura
        $cashierSubscription = $user->cashierSubscriptions()
            ->where('stripe_status', 'active')
            ->where(function($query) {
                $query->whereNull('ends_at')
                      ->orWhere('ends_at', '>', now());
            })
            ->first();
            
        if (!$cashierSubscription) {
            $cashierSubscription = $user->cashierSubscriptions()
                ->orderBy('created_at', 'desc')
                ->first();
        }
            
        $plan = null;
        if ($cashierSubscription) {
            $plan = \App\Models\Plan::where('stripe_price_id', $cashierSubscription->stripe_price)->first();
        }

        // Obter próxima data de cobrança
        $nextBillingDate = null;
        if ($cashierSubscription && $cashierSubscription->stripe_status === 'active') {
            try {
                \Stripe\Stripe::setApiKey(config('cashier.secret'));
                $stripeSubscription = \Stripe\Subscription::retrieve($cashierSubscription->stripe_id);
                if (isset($stripeSubscription->current_period_end)) {
                    $nextBillingDate = \Carbon\Carbon::createFromTimestamp(
                        $stripeSubscription->current_period_end
                    );
                }
            } catch (\Exception $e) {
                $nextBillingDate = $cashierSubscription->created_at->addMonth();
            }
        }

        // Obter histórico de pagamentos
        $invoices = [];
        try {
            if ($user->stripe_id) {
                \Stripe\Stripe::setApiKey(config('cashier.secret'));
                $stripeInvoices = \Stripe\Invoice::all([
                    'customer' => $user->stripe_id,
                    'limit' => 5,
                ]);
                $invoices = $stripeInvoices->data;
            }
        } catch (\Exception $e) {
            // Silenciar erros
        }
    @endphp

    <div class="container py-4">
        <h1 class="mb-4">Dashboard do Cliente</h1>
        <p class="lead">Bem-vindo, {{ $user->name }}!</p>

        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Informações da Assinatura</h5>
                    </div>
                    <div class="card-body">
                        @if($cashierSubscription)
                            <div class="subscription-info">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="text-primary mb-0">Status da Assinatura</h5>
                                    <span class="badge bg-{{ $cashierSubscription->stripe_status === 'active' ? 'success' : ($cashierSubscription->stripe_status === 'trialing' ? 'info' : 'warning') }} px-3 py-2">
                                        @if($cashierSubscription->stripe_status === 'active')
                                            Ativa
                                        @elseif($cashierSubscription->stripe_status === 'trialing')
                                            Em período de teste
                                        @elseif($cashierSubscription->stripe_status === 'past_due')
                                            Pagamento pendente
                                        @elseif($cashierSubscription->stripe_status === 'canceled')
                                            Cancelada
                                        @else
                                            {{ ucfirst($cashierSubscription->stripe_status) }}
                                        @endif
                                    </span>
                                </div>
        
                                <div class="subscription-details">
                                    <div class="row mb-2">
                                        <div class="col-md-5 fw-bold">Plano:</div>
                                        <div class="col-md-7">{{ $plan ? $plan->name : 'Plano ' . $cashierSubscription->stripe_price }}</div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-md-5 fw-bold">Preço:</div>
                                        <div class="col-md-7">
                                            R$ {{ $plan ? number_format($plan->price, 2, ',', '.') : number_format(0, 2, ',', '.') }}/mês
                                        </div>
                                    </div>
                                    
                                    <div class="row mb-2">
                                        <div class="col-md-5 fw-bold">Data de início:</div>
                                        <div class="col-md-7">{{ $cashierSubscription->created_at->format('d/m/Y') }}</div>
                                    </div>
                                    
                                    @if($cashierSubscription->trial_ends_at)
                                        <div class="row mb-2">
                                            <div class="col-md-5 fw-bold">Período de teste até:</div>
                                            <div class="col-md-7">
                                                {{ $cashierSubscription->trial_ends_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @if($cashierSubscription->ends_at)
                                        <div class="row mb-2">
                                            <div class="col-md-5 fw-bold">Assinatura expira em:</div>
                                            <div class="col-md-7">
                                                {{ $cashierSubscription->ends_at->format('d/m/Y') }}
                                            </div>
                                        </div>
                                    @elseif($cashierSubscription->stripe_status === 'active' && $nextBillingDate)
                                        <div class="row mb-2">
                                            <div class="col-md-5 fw-bold">Próxima cobrança:</div>
                                            <div class="col-md-7">
                                                {{ $nextBillingDate->format('d/m/Y') }}
                                                <span class="ms-2 badge bg-info">
                                                    @php
                                                        $dias = (int)now()->diffInDays($nextBillingDate);
                                                    @endphp
                                                    @if($dias > 0)
                                                        Em {{ $dias }} {{ $dias == 1 ? 'dia' : 'dias' }}
                                                    @elseif($dias == 0)
                                                        Hoje!
                                                    @else
                                                        Expirada
                                                    @endif
                                                </span>
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <div class="mt-4">
                                    @if($cashierSubscription->stripe_status === 'active' || $cashierSubscription->stripe_status === 'trialing')
                                    <form action="{{ route('subscriptions.cancel') }}" method="POST" class="d-inline"
                                        onsubmit="return confirm('Tem certeza que deseja cancelar sua assinatura?');">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger">
                                            Cancelar Assinatura
                                        </button>
                                    </form>
                                        
                                        @if($plan)
                                            <a href="{{ route('subscriptions.index') }}" class="btn btn-outline-primary ms-2">
                                                Alterar Plano
                                            </a>
                                        @endif
                                    @elseif($cashierSubscription->stripe_status === 'canceled' && $cashierSubscription->ends_at && $cashierSubscription->ends_at->isFuture())
                                        <a href="{{ route('subscriptions.resume') }}" class="btn btn-success">
                                            Reativar Assinatura
                                        </a>
                                    @else
                                        <a href="{{ route('subscriptions.index') }}" class="btn btn-primary">
                                            Assinar Novamente
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="alert alert-warning">
                                <p>Você não possui uma assinatura ativa no momento.</p>
                                <a href="{{ route('subscriptions.index') }}" class="btn btn-primary mt-2">
                                    Ver Planos Disponíveis
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">Histórico de Pagamentos</h5>
                    </div>
                    <div class="card-body">
                        @if(count($invoices) > 0)
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Data</th>
                                            <th>Valor</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($invoices as $invoice)
                                            <tr>
                                                <td>{{ \Carbon\Carbon::createFromTimestamp($invoice->created)->format('d/m/Y') }}</td>
                                                <td>R$ {{ number_format($invoice->amount_paid / 100, 2, ',', '.') }}</td>
                                                <td>
                                                    <span class="badge bg-{{ $invoice->paid ? 'success' : 'warning' }}">
                                                        {{ $invoice->paid ? 'Pago' : 'Pendente' }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p>Nenhum pagamento registrado.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts opcionais -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Seus scripts personalizados aqui
    </script>
</body>
</html>