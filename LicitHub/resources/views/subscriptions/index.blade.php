@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Planos de Assinatura</div>

                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row">
                        @foreach ($plans as $plan)
                            <div class="col-md-4 mb-4">
                                <div class="card h-100">
                                    <div class="card-header">{{ $plan->name }}</div>
                                    <div class="card-body">
                                        <h5 class="card-title">R$ {{ number_format($plan->price, 2, ',', '.') }}</h5>
                                        <p class="card-text">{{ $plan->description }}</p>
                                        
                                        @if ($plan->features)
                                            <ul class="list-group list-group-flush mb-3">
                                                @foreach ($plan->features as $feature => $value)
                                                    <li class="list-group-item">{{ $feature }}: {{ $value }}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        
                                        <a href="{{ route('subscriptions.checkout', $plan->id) }}" class="btn btn-primary">Assinar</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
