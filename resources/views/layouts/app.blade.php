<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>ReplyRadar</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo/logoSoloSinFondo.png') }}">
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body>

    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/[0.04]" style="background: rgba(8,8,15,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo/logoSoloSinFondo.png') }}" alt="ReplyRadar" class="h-8 w-auto">
                    <span class="hidden sm:inline font-bold text-lg text-white">ReplyRadar</span>
                </a>

                {{-- Right --}}
                <div class="flex items-center gap-4">
                    {{-- Language switcher --}}
                    <div class="flex items-center gap-1 text-xs">
                        <a href="{{ route('language.switch', 'en') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">EN</a>
                        <span class="text-white/[0.1]">|</span>
                        <a href="{{ route('language.switch', 'es') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'es' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">ES</a>
                    </div>
                    <a href="{{ route('projects.index') }}" class="text-sm text-gray-400 hover:text-white transition-colors {{ request()->routeIs('projects.*') ? 'text-white' : '' }}">{{ __('Projects') }}</a>
                    <a href="{{ route('billing.plans') }}" class="text-sm text-gray-400 hover:text-white transition-colors {{ request()->routeIs('billing.*') ? 'text-white' : '' }}">{{ __('Plans') }}</a>

                    {{-- User dropdown --}}
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 glass-btn-secondary text-sm !px-3 !py-1.5">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-xs font-bold text-white">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <span class="hidden sm:inline">{{ auth()->user()->name }}</span>
                            <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 origin-top-right" style="display: none">
                            <div class="glass !p-1.5 !rounded-xl shadow-xl shadow-black/50">
                                <a href="{{ route('profile.edit') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/[0.06] rounded-lg transition-colors">{{ __('Profile') }}</a>
                                <a href="{{ route('billing.plans') }}" class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/[0.06] rounded-lg transition-colors">{{ __('Subscription') }}</a>
                                <hr class="border-white/[0.06] my-1">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors">{{ __('Logout') }}</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Flash messages --}}
    @if(session('success') || session('error') || $errors->any())
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-40 w-full max-w-md px-4">
            @if(session('success'))
                <div class="glass !rounded-xl px-4 py-3 border border-green-500/20 bg-green-500/10 text-sm text-green-400 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="glass !rounded-xl px-4 py-3 border border-red-500/20 bg-red-500/10 text-sm text-red-400 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('error') }}
                </div>
            @endif
            @if($errors->any())
                @foreach($errors->all() as $error)
                    <div class="glass !rounded-xl px-4 py-3 border border-red-500/20 bg-red-500/10 text-sm text-red-400 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $error }}
                    </div>
                @endforeach
            @endif
        </div>
    @endif

    {{-- Main content --}}
    <main class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @yield('content')
        </div>
    </main>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
