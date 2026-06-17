<?php
namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhook;

class WebhookController extends CashierWebhook
{
    public function handleCustomerSubscriptionDeleted(array $payload)
    {
        $customerId = $payload['data']['object']['customer'];
        $user = \App\Models\User::where('stripe_id', $customerId)->first();

        if ($user) {
            $user->update(['plan' => 'free']);
        }

        return response()->json(['status' => 'ok']);
    }

    public function handleCustomerSubscriptionUpdated(array $payload)
    {
        // Cashier maneja esto automáticamente
        return parent::handleCustomerSubscriptionUpdated($payload);
    }
}
