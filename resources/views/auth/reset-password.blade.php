@extends('layouts.guest')

@section('title', __('Reset password'))

@section('content')
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">🔄</span>
            </div>
            <h1 class="text-2xl font-bold text-white">{{ __('Reset password') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('Choose a secure new password') }}</p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Email') }}</label>
                    <input type="email" name="email" value="{{ old('email', $email) }}" required autofocus autocomplete="username"
                        class="glass-input" placeholder="{{ __('you@email.com') }}">
                    @error('email') <p class="text-xs text-red-400 mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('New password') }}</label>
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
                    {{ __('Reset Password') }}
                </button>
            </form>
        </div>
    </div>
@endsection
