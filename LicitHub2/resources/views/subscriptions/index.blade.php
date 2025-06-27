{{-- resources/views/subscriptions/index.blade.php --}}
@isset($plans)
<style>
    
</style>
<div class="plans-section">
    <div class="container">
        <h2 class="section-title">Planos Disponíveis</h2>
        
        <div class="plans-grid">
            @foreach ($plans as $plan)
            <div class="plan-card">
                <div class="plan-header">
                    <h3>{{ $plan->name }}</h3>
                    <div class="plan-price">
                        R$ {{ number_format($plan->price, 2, ',', '.') }} 
                        <span>/{{ $plan->interval }}</span>
                    </div>
                </div>
                
                <div class="plan-body">
                    <p class="plan-description">{{ $plan->description }}</p>
                    
                    @php
                $features = is_array($plan->features) ? $plan->features : explode("\n", $plan->features);
            @endphp
            
            <ul class="plan-features">
                @foreach($features as $feature)
                    @if(trim($feature))
                        <li>{{ $feature }}</li>
                    @endif
                @endforeach
            </ul>
                </div>
                
                <div class="plan-footer">
                    <a href="{{ route('subscriptions.checkout', $plan->id) }}" class="btn btn-subscribe">
                        Assinar Plano
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endisset