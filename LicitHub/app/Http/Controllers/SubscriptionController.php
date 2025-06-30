<?php

namespace App\Http\Controllers;
use Laravel\Cashier\Cashier;
use Illuminate\Http\Request;
use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;



class SubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::where('is_active', true)->get();
        return view('subscriptions.index', compact('plans'));
    }

    public function checkout($planId)
    {
        $plan = Plan::findOrFail($planId);
        $user = Auth::user();

        Stripe::setApiKey(config('services.stripe.secret'));
        $session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'brl',
                    'product_data' => [
                        'name' => $plan->name,
                    ],
                    'unit_amount' => $plan->price * 100, // Stripe espera o valor em centavos
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment', // ou 'subscription' se for um plano recorrente
            'success_url' => route('subscriptions.success'),
            'cancel_url' => route('subscriptions.cancel'),
        ]);
        return redirect($session->url);
    }

public function success(Request $request)
{
    if (!$request->session_id) {
        return redirect()->route('subscriptions.index')
            ->with('error', 'Sessão de checkout inválida.');
    }
    
    // Inicializar Stripe
    \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
    
    try {
        // Recuperar a sessão de checkout
        $session = \Stripe\Checkout\Session::retrieve($request->session_id);
        
        // Recuperar o cliente e a assinatura do Stripe
        $customer = \Stripe\Customer::retrieve($session->customer);
        $subscription = \Stripe\Subscription::retrieve($session->subscription);
        
        // Obter o usuário atual
        $user = Auth::user();
        
        // Atualizar diretamente no banco de dados (sem usar o método save ou update)
        DB::table('users')
            ->where('id', $user->id)
            ->update([
                'stripe_id' => $customer->id,
                'pm_type' => null, // Vamos atualizar isso abaixo se disponível
                'pm_last_four' => null, // Vamos atualizar isso abaixo se disponível
                'trial_ends_at' => $subscription->trial_end ? 
                    date('Y-m-d H:i:s', $subscription->trial_end) : null
            ]);
        
        // Buscar o método de pagamento e atualizar pm_type e pm_last_four
        if ($subscription->default_payment_method) {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($subscription->default_payment_method);
            if ($paymentMethod->type == 'card') {
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'pm_type' => 'card',
                        'pm_last_four' => $paymentMethod->card->last4
                    ]);
            }
        }
        
        // Registrar a assinatura na tabela cashier_subscriptions
        DB::table('cashier_subscriptions')->updateOrInsert(
            ['stripe_id' => $subscription->id],
            [
                'user_id' => $user->id,
                'name' => 'default',
                'stripe_status' => $subscription->status,
                'stripe_price' => $subscription->items->data[0]->price->id,
                'quantity' => $subscription->items->data[0]->quantity ?? 1,
                'trial_ends_at' => $subscription->trial_end ? 
                    date('Y-m-d H:i:s', $subscription->trial_end) : null,
                'ends_at' => $subscription->cancel_at ? 
                    date('Y-m-d H:i:s', $subscription->cancel_at) : null,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        
        // Registrar o pagamento (se houver)
        if ($subscription->latest_invoice) {
            $invoice = \Stripe\Invoice::retrieve($subscription->latest_invoice);
            
            if ($invoice && $invoice->status === 'paid') {
                DB::table('payments')->updateOrInsert(
                    ['gateway_id' => $invoice->id],
                    [
                        'user_id' => $user->id,
                        'subscription_id' => DB::table('cashier_subscriptions')
                            ->where('stripe_id', $subscription->id)
                            ->value('id'),
                        'amount' => $invoice->amount_paid / 100,
                        'currency' => $invoice->currency,
                        'gateway' => 'stripe',
                        'status' => 'paid',
                        'paid_at' => date('Y-m-d H:i:s', $invoice->created),
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                );
            }
        }

          // Obter a assinatura confirmada
    $subscription = auth()->user()->subscriptions()->latest()->first();
    
    // Redirecionar para o recibo
    return redirect()->route('receipt.generate', $subscription->id);
        
        // Adicionar log para depuração
        Log::info('Checkout processado com sucesso', [
            'user_id' => $user->id,
            'stripe_id' => $customer->id,
            'subscription_id' => $subscription->id
        ]);


        
        return view('subscriptions.success');
    } catch (\Exception $e) {
        Log::error('Erro ao processar checkout: ' . $e->getMessage(), [
            'session_id' => $request->session_id,
            'user_id' => Auth::id()
        ]);
        
        return redirect()->route('subscriptions.index')
            ->with('error', 'Erro ao processar o pagamento: ' . $e->getMessage());
    }

}

public function cancel(Request $request): RedirectResponse
{
    $user = $request->user();

    if ($user->subscribed()) {
        $user->subscriptions()->cancel();
    }

    return redirect('/')->with('success', 'Assinatura cancelada com sucesso.');
}

}
