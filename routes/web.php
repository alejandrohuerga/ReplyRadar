<?php
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\KeywordController;
use App\Http\Controllers\BillingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('dashboard'));

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
    Route::delete('/keywords/{keyword}',             [KeywordController::class, 'destroy'])->name('keywords.destroy');

    // Billing
    Route::get('/billing/plans',    [BillingController::class, 'plans'])->name('billing.plans');
    Route::post('/billing/upgrade', [BillingController::class, 'upgrade'])->name('billing.upgrade');
});
Route::middleware('auth')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [\App\Http\Controllers\ProfileController::class, 'destroy'])->name('profile.destroy');
});
require __DIR__.'/auth.php';