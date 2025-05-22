<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Plan;
use App\Services\SubscriptionSyncService;

class SubscriptionController extends Controller
{
    protected $syncService;

    public function __construct()
{
    // Remova a linha com $this->middleware('auth');
    $this->syncService = app(SubscriptionSyncService::class);
}


    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('subscriptions.index', compact('plans'));
    }

    public function checkout($planId)
{
    $plan = Plan::findOrFail($planId);
    
    // Criar o Setup Intent diretamente
    $stripe = new \Stripe\StripeClient(config('services.stripe.secret'));
    $intent = $stripe->setupIntents->create([
        'payment_method_types' => ['card'],
    ]);
    
    $stripeKey = config('services.stripe.key');
    return view('subscriptions.checkout', compact('plan', 'intent', 'stripeKey'));
}

    public function process(Request $request, $planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = $request->user();

        // Valide o token de pagamento
        $request->validate([
            'payment_method' => 'required',
        ]);

        try {
            // Crie ou obtenha o cliente no Stripe
            $user->createOrGetStripeCustomer();
            
            // Atualize o método de pagamento padrão
            $user->updateDefaultPaymentMethod($request->payment_method);
            
            // Crie a assinatura no Stripe via Cashier
            $cashierSubscription = $user->newSubscription('default', $plan->stripe_price_id)
                ->create($request->payment_method);
            
            // Sincronize com suas tabelas personalizadas
            $subscription = $this->syncService->syncSubscriptionData($cashierSubscription);
            
            return redirect()->route('subscriptions.success');
            
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function success()
    {
        return view('subscriptions.success');
    }

    public function cancel(Request $request)
    {
        $user = $request->user();
        
        // Cancele a assinatura no Stripe via Cashier
        if ($user->subscription('default')) {
            $user->subscription('default')->cancel();
        }
        
        return redirect()->route('subscriptions.index')
            ->with('success', 'Sua assinatura foi cancelada.');
    }
    
    
}
