@extends('layouts.guest')

@section('title', __('Create account'))

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mx-auto mb-4">
                <img src="{{ asset('images/logo/logoSoloSinFondo.png') }}" alt="ReplyRadar" class="h-12 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-white">{{ __('Get started free') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('Monitor Reddit and find opportunities') }}</p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name"
                        class="glass-input" placeholder="{{ __('Your name') }}">
                    @error('name') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email') }}" required autocomplete="username"
                        class="glass-input" placeholder="{{ __('you@email.com') }}">
                    @error('email') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Password') }}</label>
                    <input type="password" name="password" required autocomplete="new-password"
                        class="glass-input" placeholder="••••••••">
                    @error('password') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Confirm password') }}</label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="glass-input" placeholder="••••••••">
                </div>

                <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                    {{ __('Create account') }}
                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            {{ __('Already have an account?') }}
            <a href="{{ route('login') }}" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors">{{ __('Log in') }}</a>
        </p>
    </div>
@endsection
