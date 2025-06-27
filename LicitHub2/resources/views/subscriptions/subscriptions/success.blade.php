@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Assinatura Confirmada</div>

                <div class="card-body">
                    <div class="alert alert-success">
                        Sua assinatura foi processada com sucesso!
                    </div>
                    
                    <p>Obrigado por assinar nosso serviço. Sua assinatura está ativa e você já pode aproveitar todos os benefícios.</p>
                    
                    <div class="d-grid gap-2">
                        <a href="{{ route('home') }}" class="btn btn-primary">Voltar para o Dashboard</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
