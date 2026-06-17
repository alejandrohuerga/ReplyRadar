@extends('layouts.guest')

@section('title', __('Forgot password'))

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">🔑</span>
            </div>
            <h1 class="text-2xl font-bold text-white">{{ __('Forgot your password?') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __("We'll send you a reset link") }}</p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus
                        class="glass-input" placeholder="{{ __('you@email.com') }}">
                    @error('email') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                    {{ __('Send link') }}
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            <a href="{{ route('login') }}" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors">{{ __('Back to login') }}</a>
        </p>
    </div>
@endsection
