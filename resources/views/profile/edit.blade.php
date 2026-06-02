@extends('layouts.app')

@section('title', __('Profile'))

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">{{ __('Profile') }}</h1>
        <p class="text-gray-400 mt-1 text-sm">{{ __('Manage your personal info and security') }}</p>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="glass !p-6">
            <h2 class="text-lg font-semibold text-white mb-1">{{ __('Profile information') }}</h2>
            <p class="text-sm text-gray-400 mb-6">{{ __('Update your name and email') }}</p>

            <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
                @csrf
                @method('PATCH')

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}"
                        class="glass-input">
                    @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Email address') }}</label>
                    <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}"
                        class="glass-input">
                    @error('email') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                @if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail())
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-sm text-amber-400">
                        {{ __('Your email is unverified.') }}
                        <form method="POST" action="{{ route('verification.send') }}" class="inline">
                            @csrf
                            <button type="submit" class="underline ml-1">{{ __('Resend verification') }}</button>
                        </form>
                    </div>
                @endif

                @if(session('status') === 'verification-link-sent')
                    <div class="p-3 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-400">
                        {{ __('A new verification link has been sent to your email address.') }}
                    </div>
                @endif

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="glass-btn-primary">{{ __('Save') }}</button>
                    @if(session('success'))
                        <span class="text-sm text-green-400">{{ __('✓ Saved') }}</span>
                    @endif
                </div>
            </form>
        </div>

        <div class="glass !p-6">
            <h2 class="text-lg font-semibold text-white mb-1">{{ __('Update password') }}</h2>
            <p class="text-sm text-gray-400 mb-6">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>

            <form method="POST" action="{{ route('password.update') }}" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Current password') }}</label>
                    <input type="password" name="current_password" class="glass-input" autocomplete="current-password">
                    @error('current_password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('New password') }}</label>
                    <input type="password" name="password" class="glass-input" autocomplete="new-password">
                    @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Confirm password') }}</label>
                    <input type="password" name="password_confirmation" class="glass-input" autocomplete="new-password">
                    @error('password_confirmation') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="glass-btn-primary">{{ __('Save') }}</button>
                    @if(session('status') === 'password-updated')
                        <span class="text-sm text-green-400">{{ __('✓ Saved') }}</span>
                    @endif
                </div>
            </form>
        </div>

        <div class="glass !p-6 border-red-500/10">
            <h2 class="text-lg font-semibold text-red-400 mb-1">{{ __('Delete account') }}</h2>
            <p class="text-sm text-gray-400 mb-6">
                {{ __('Once your account is deleted, all data will be permanently deleted.') }}
            </p>

            <form method="POST" action="{{ route('profile.destroy') }}"
                onsubmit="return confirm('{{ __('Are you sure you want to delete your account? This action cannot be undone.') }}')"
                class="space-y-4">
                @csrf
                @method('DELETE')

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5">{{ __('Confirm with your password') }}</label>
                    <input type="password" name="password" class="glass-input" placeholder="{{ __('Your current password') }}" autocomplete="current-password">
                    @error('password') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="glass-btn-danger !px-5 !py-2.5 text-sm">
                    {{ __('Delete account') }}
                </button>
            </form>
        </div>
    </div>
@endsection
