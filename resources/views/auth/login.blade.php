@extends('layouts.guest')

@section('title', __('Log in'))

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mx-auto mb-4">
                <img src="{{ asset('images/logo/logoSoloSinFondo.png') }}" alt="ReplyRadar" class="h-12 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-white">{{ __('Welcome back') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('Access your business opportunities') }}</p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                        class="glass-input" placeholder="{{ __('you@email.com') }}">
                    @error('email') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-medium text-gray-300">{{ __('Password') }}</label>
                        @if(Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors">{{ __('Forgot your password?') }}</a>
                        @endif
                    </div>
                    <input type="password" name="password" required autocomplete="current-password"
                        class="glass-input" placeholder="••••••••">
                    @error('password') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-2.5 mb-6">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded border-white/[0.1] bg-white/[0.05] text-indigo-500 focus:ring-indigo-500/40 focus:ring-offset-0"
                        style="accent-color: #6366f1">
                    <label for="remember" class="text-sm text-gray-400 cursor-pointer select-none">{{ __('Remember me') }}</label>
                </div>

                <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                    {{ __('Enter ReplyRadar') }}
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            {{ __("Don't have an account?") }}
            <a href="{{ route('register') }}" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors">{{ __('Start free') }}</a>
        </p>
    </div>
@endsection
