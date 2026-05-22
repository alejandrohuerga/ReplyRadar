<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function plans(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Billing/Plans', [
            'auth' => [
                'user' => $user,
            ],
            'currentPlan' => $user->plan,
            'onTrial'     => $user->onTrial(),
            'subscribed'  => $user->subscribed('default'),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['plan' => 'required|in:pro,business']);

        $user     = $request->user();
        $priceId  = config("replyradar.stripe_prices.{$request->plan}");

        if ($user->subscribed('default')) {
            return Inertia::location(
                $user->redirectToBillingPortal(route('billing.plans'))->getTargetUrl()
            );
        }

        $checkout = $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.success') . '?plan=' . $request->plan,
                'cancel_url'  => route('billing.plans'),
            ]);

        return Inertia::location($checkout->url);
    }

    public function success(Request $request)
    {
        $plan = $request->query('plan', 'pro');
        $request->user()->update(['plan' => $plan]);

        return redirect()->route('dashboard')
            ->with('success', 'Bienvenido a ReplyRadar ' . ucfirst($plan) . '!');
    }

    public function portal(Request $request)
    {
        return Inertia::location(
            $request->user()->redirectToBillingPortal(route('billing.plans'))->getTargetUrl()
        );
    }
}