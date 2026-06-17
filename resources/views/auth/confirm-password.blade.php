@extends('layouts.app')

@section('title', __('Confirm password'))

@section('content')
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">🔒</span>
        </div>
        <h1 class="text-2xl font-bold text-white">{{ __('Confirm your password') }}</h1>
        <p class="text-sm text-gray-400 mt-1">{{ __('This is a secure area. Please confirm your password before continuing.') }}</p>
    </div>

    <div class="glass !p-8">
        <form method="POST" action="{{ route('password.confirm') }}">
            @csrf

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Password') }}</label>
                <input type="password" name="password" required autocomplete="current-password"
                    class="glass-input" placeholder="••••••••">
                @error('password') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                {{ __('Confirm') }}
            </button>
        </form>
    </div>
</div>
@endsection
