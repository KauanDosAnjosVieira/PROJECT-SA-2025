<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use App\Models\Plan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReceiptController extends Controller
{
    public function generate($subscriptionId)
    {
        $subscription = Subscription::with(['user', 'plan'])
                        ->findOrFail($subscriptionId);
                        
        // Verificar se o usuário tem permissão
        if ($subscription->user_id !== Auth::id()) {
            abort(403);
        }

        $plan = Plan::where('stripe_price_id', $subscription->stripe_price)
                  ->first();

        $pdf = Pdf::loadView('receipts.show', [
            'subscription' => $subscription,
            'plan' => $plan,
            'user' => $subscription->user,
            'date' => now()->format('d/m/Y H:i:s')
        ]);

        return $pdf->download("recibo-assinatura-{$subscription->id}.pdf");
    }
}