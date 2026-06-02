<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\WebhookController;
use App\Http\Controllers\LanguageController;
use Illuminate\Support\Facades\Route;

Route::get('/language/{locale}', LanguageController::class)->name('language.switch');

Route::get('/', [\App\Http\Controllers\LandingController::class, 'index'])->name('landing');

Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard principal
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard');

    // Proyectos
    Route::get('/projects',              [ProjectController::class, 'index'])->name('projects.index');
    Route::post('/projects',             [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/projects/{project}',    [ProjectController::class, 'show'])->name('projects.show');
    Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');

    // Keywords (anidadas bajo proyecto)
    Route::post('/projects/{project}/keywords',      [KeywordController::class, 'store'])->name('keywords.store');
    Route::patch('/keywords/{keyword}/toggle',       [KeywordController::class, 'toggle'])->name('keywords.toggle');
    Route::match(['delete', 'post'], '/keywords/{keyword}', [KeywordController::class, 'destroy'])->name('keywords.destroy');

    // Billing
    Route::get('/billing/plans',    [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/checkout', [BillingController::class, 'checkout'])->name('billing.checkout');
    Route::post('/billing/promo-14', [BillingController::class, 'checkoutPromo14'])->name('billing.promo14');
    Route::get('/billing/success',  [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/portal',   [BillingController::class, 'portal'])->name('billing.portal');

    // Export (Pro only)
    Route::get('/export/posts', [\App\Http\Controllers\ExportController::class, 'posts'])->name('export.posts');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Stripe webhook (sin auth, sin CSRF)
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);


require __DIR__.'/auth.php';