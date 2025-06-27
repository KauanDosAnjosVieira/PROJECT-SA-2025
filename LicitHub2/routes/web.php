<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Auth\ResetPasswordController;

use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\StripeWebhookController;

use App\Http\Controllers\AdminClientController;
use App\Http\Controllers\AdminChatController;
use App\Http\Controllers\ClientChatController;
use App\Http\Controllers\AdminAdminController;
use App\Http\Controllers\AdminPlanController;
use App\Http\Controllers\DashboardController;

// Rota principal
Route::get('/', [HomeController::class, 'index'])->name('site.home');

// Rotas de autenticação personalizadas
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register']);

// Rotas de recuperação de senha
Route::get("forgot-password", [ForgotPasswordController::class, "showLinkRequestForm"])
    ->middleware("guest")
    ->name("password.request");

Route::post("forgot-password", [ForgotPasswordController::class, "sendResetLinkEmail"])
    ->middleware("guest")
    ->name("password.email");

Route::get("reset-password/{token}", [ResetPasswordController::class, "showResetForm"])
    ->middleware("guest")
    ->name("password.reset");

Route::post("reset-password", [ResetPasswordController::class, "reset"])
    ->middleware("guest")
    ->name("password.reset.update");

// Rotas de verificação de e-mail
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', fn() => view('auth.verify-email'))->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect('/dashboard');
    })->middleware(['signed'])->name('verification.verify');

    Route::post('/email/verification-notification', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware(['throttle:6,1'])->name('verification.send');
});

// Rotas autenticadas comuns
Route::middleware(['auth'])->group(function () {
    // Redirecionamento de dashboard conforme tipo de usuário
    Route::get('/dashboard', function () {
        return Auth::user()->user_type === 'admin'
            ? redirect('/admin/dashboard')
            : redirect('/client/dashboard');
    })->name('dashboard');

    // Rotas de perfil
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Rota para atualização de senha (AJUSTADA)
    Route::put('/password/update', [ProfileController::class, 'updatePassword'])->name('password.update');

    // Assinaturas com suporte a PIX
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/{plan}/checkout', [SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
    Route::post('/subscriptions/{plan}/process', [SubscriptionController::class, 'process'])->name('subscriptions.process');
    Route::get('/subscriptions/success', [SubscriptionController::class, 'success'])->name('subscriptions.success');
    Route::post('/subscriptions/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
});

// Rotas específicas para administradores
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');

    Route::resource('clients', AdminClientController::class);
    Route::resource('admins', AdminAdminController::class);
    Route::resource('plans', AdminPlanController::class);

    Route::get('/chat', [AdminChatController::class, 'index'])->name('admin.chat');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('admin.chat.send');
});
 
// Rotas específicas para clientes
Route::middleware(['auth'])->prefix('client')->group(function () {
    Route::get('/dashboard', fn() => view('client.dashboard'))->name('client.dashboard');

    Route::get('/chat', [ClientChatController::class, 'index'])->name('client.chat');
    Route::post('/chat/send', [ClientChatController::class, 'send'])->name('client.chat.send');
});

// Webhook do Stripe
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook'])
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);