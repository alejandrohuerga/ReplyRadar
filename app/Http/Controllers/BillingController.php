<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    public function plans(Request $request)
    {
        $user = $request->user();

        return view('billing.plans', [
            'currentPlan'     => $user->plan,
            'onTrial'         => $user->onTrial(),
            'subscribed'      => $user->subscribed('default'),
            'stripeReady'     => config('replyradar.stripe_prices.pro') && config('replyradar.stripe_prices.business'),
        ]);
    }

    public function checkout(Request $request)
    {
        $request->validate(['plan' => 'required|in:pro,business']);

        $user     = $request->user();
        $priceId  = config("replyradar.stripe_prices.{$request->plan}");

        if (! $priceId) {
            return redirect()->route('billing.plans')
                ->with('error', __('Stripe price not configured for this plan. Contact support.'));
        }

        if ($user->subscribed('default')) {
            return redirect()->away(
                $user->redirectToBillingPortal(route('billing.plans'))->getTargetUrl()
            );
        }

        $checkout = $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.success') . '?plan=' . $request->plan,
                'cancel_url'  => route('billing.plans'),
            ]);

        return redirect()->away($checkout->url);
    }

    public function checkoutPromo14(Request $request)
    {
        $user    = $request->user();
        $priceId = config('replyradar.stripe_prices.promo_14');

        if (! $priceId) {
            return redirect()->route('dashboard')
                ->with('error', __('Promo not available at this time.'));
        }

        if ($user->subscribed('default')) {
            return redirect()->away(
                $user->redirectToBillingPortal(route('billing.plans'))->getTargetUrl()
            );
        }

        $checkout = $user->newSubscription('default', $priceId)
            ->checkout([
                'success_url' => route('billing.success') . '?plan=pro',
                'cancel_url'  => route('dashboard'),
            ]);

        return redirect()->away($checkout->url);
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
        return redirect()->away(
            $request->user()->redirectToBillingPortal(route('billing.plans'))->getTargetUrl()
        );
    }
}