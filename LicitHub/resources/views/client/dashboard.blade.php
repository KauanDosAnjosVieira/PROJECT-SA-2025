@php
    $user = auth()->user();
    if (!($user->user_type === 'customer' && $user->hasActiveCashierSubscription())) {
        header('Location: ' . url('/'));
        exit;
    }
@endphp



@if ($user->user_type === 'customer' && $user->hasActiveCashierSubscription())

{{-- filepath: resources/views/client/dashboard.blade.php --}}

@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Dashboard do Cliente</h1>
        <p>Bem-vindo ao painel do cliente!</p>
    </div>
@endsection
@endif