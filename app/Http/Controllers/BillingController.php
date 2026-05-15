<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class BillingController extends Controller
{
    public function plans(Request $request)
    {
        return Inertia::render('Billing/Plans', [
            'currentPlan' => $request->user()->plan,
        ]);
    }

    // Simulación de upgrade (sin Stripe real por ahora — lo conectamos al final)
    public function upgrade(Request $request)
    {
        $request->validate(['plan' => 'required|in:pro,business']);

        $request->user()->update(['plan' => $request->plan]);

        return redirect()->route('dashboard')->with('success', "Plan actualizado a {$request->plan}.");
    }
}
