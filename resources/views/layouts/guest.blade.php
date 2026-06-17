<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ReplyRadar')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logoSoloSinFondo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .auth-gradient {
            min-height: 100vh;
            background: linear-gradient(135deg, #08080f 0%, #0d0d1a 50%, #08080f 100%);
            display: flex;
            flex-direction: column;
        }
        .auth-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            top: -200px;
            right: -200px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-glow-2 {
            position: fixed;
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -150px;
            background: radial-gradient(circle, rgba(168,85,247,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
    </style>
</head>
<body class="font-['Figtree']">
    <div class="auth-gradient">
        <div class="auth-glow"></div>
        <div class="auth-glow-2"></div>

        {{-- Nav minimal --}}
        <nav class="relative z-10 border-b border-white/[0.04]" style="background: rgba(8,8,15,0.6); backdrop-filter: blur(20px);">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <a href="{{ url('/') }}" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo/logoSoloSinFondo.png') }}" alt="ReplyRadar" class="h-8 w-auto">
                        <span class="hidden sm:inline font-bold text-lg text-white">ReplyRadar</span>
                    </a>
                    <div class="flex items-center gap-3">
                        {{-- Language switcher --}}
                        <div class="flex items-center gap-1 text-xs">
                            <a href="{{ route('language.switch', 'en') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">EN</a>
                            <span class="text-white/[0.1]">|</span>
                            <a href="{{ route('language.switch', 'es') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'es' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">ES</a>
                        </div>
                        @if(Route::has('register') && !request()->routeIs('register'))
                            <a href="{{ route('register') }}" class="glass-btn-primary !px-4 !py-1.5 text-sm">{{ __('Register') }}</a>
                        @elseif(!request()->routeIs('login'))
                            <a href="{{ route('login') }}" class="glass-btn-secondary !px-4 !py-1.5 text-sm">{{ __('Log in') }}</a>
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Flash --}}
        @if(session('status'))
            <div class="relative z-10 max-w-md mx-auto mt-6 px-4">
                <div class="glass !rounded-xl px-4 py-3 border border-green-500/20 bg-green-500/10 text-sm text-green-400 text-center">
                    {{ session('status') }}
                </div>
            </div>
        @endif

        {{-- Content --}}
        <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-12">
            @yield('content')
        </div>
    </div>
</body>
</html>
