<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ __('ReplyRadar — Business opportunities from Reddit') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800,900&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
    <style>
        .landing-gradient { background: linear-gradient(135deg, #08080f 0%, #0d0d1a 50%, #08080f 100%); }
        .hero-glow { position: absolute; width: 600px; height: 600px; top: -200px; left: 50%; transform: translateX(-50%); background: radial-gradient(circle, rgba(99,102,241,0.15) 0%, transparent 70%); pointer-events: none; }
        .grid-bg { background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px); background-size: 60px 60px; }
    </style>
</head>
<body class="landing-gradient min-h-screen font-['Figtree']">

    {{-- Navbar --}}
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/[0.04]" style="background: rgba(8,8,15,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            <div class="flex items-center justify-between h-16">
                <a href="{{ url('/') }}" class="flex items-center gap-2">
                    <img src="{{ asset('images/logo/logoSoloSinFondo.png') }}" alt="ReplyRadar" class="h-8 w-auto">
                    <span class="font-bold text-lg text-white">ReplyRadar</span>
                </a>
                <div class="flex items-center gap-6">
                    <div class="flex items-center gap-1 text-xs">
                        <a href="{{ route('language.switch', 'en') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'en' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">EN</a>
                        <span class="text-white/[0.1]">|</span>
                        <a href="{{ route('language.switch', 'es') }}" class="px-2 py-1 rounded-lg transition-colors {{ app()->getLocale() === 'es' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300' }}">ES</a>
                    </div>
                    <a href="#features" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Features') }}</a>
                    <a href="#pricing" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Pricing') }}</a>
                    <a href="{{ route('login') }}" class="text-sm text-gray-400 hover:text-white transition-colors">{{ __('Login') }}</a>
                    <a href="{{ route('register') }}" class="glass-btn-primary !px-5 !py-2">{{ __('Get started free') }}</a>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative pt-32 pb-20 px-4 overflow-hidden">
        <div class="hero-glow"></div>
        <div class="max-w-4xl mx-auto text-center relative">
            <div class="inline-flex items-center gap-2 glass !rounded-full !px-4 !py-1.5 text-xs text-indigo-300 mb-8">
                <span class="w-2 h-2 bg-indigo-400 rounded-full animate-pulse"></span>
                {{ __('Monitoring Reddit in real-time') }}
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                {{ __('Turn conversations into') }}<br>
                <span class="text-gradient">{{ __('business opportunities') }}</span>
            </h1>
            <p class="text-lg text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
                {{ __('ReplyRadar analyzes Reddit, detects real buyer intent, and delivers ranked actionable opportunities before your competition.') }}
            </p>
            <div class="flex items-center justify-center gap-4 flex-wrap">
                <a href="{{ route('register') }}" class="glass-btn-primary !px-8 !py-3.5 text-base font-semibold">
                    {{ __('Start free, no card needed') }}
                </a>
                <a href="#features" class="glass-btn-secondary !px-8 !py-3.5 text-base font-semibold">
                    {{ __('See how it works') }}
                </a>
            </div>
            <p class="text-sm text-gray-500 mt-4">{{ __('30-second setup · Cancel anytime') }}</p>

            {{-- Preview dashboard --}}
            <div class="mt-16 glass !rounded-2xl !p-6 text-left max-w-2xl mx-auto">
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-3 h-3 rounded-full bg-red-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-yellow-500/60"></div>
                    <div class="w-3 h-3 rounded-full bg-green-500/60"></div>
                    <span class="ml-3 text-xs text-gray-500">replyradar.app/dashboard</span>
                </div>
                <div class="space-y-2">
                    @foreach([
                        ['title' => 'researching subscription management platforms for a growing SaaS', 'score' => 76, 'subreddit' => 'SaaS', 'hot' => true],
                        ['title' => 'Is there a tool that detects buyer intent from Reddit posts?', 'score' => 71, 'subreddit' => 'entrepreneur', 'hot' => true],
                        ['title' => 'what SaaS niche has terrible UX but insane retention?', 'score' => 54, 'subreddit' => 'micro_saas', 'hot' => false],
                        ['title' => 'Is Micro SaaS a good side hustle in 2025?', 'score' => 52, 'subreddit' => 'sidehustle', 'hot' => false],
                    ] as $item)
                        <div class="flex items-center justify-between gap-3 bg-white/[0.03] rounded-xl px-4 py-3 border border-white/[0.04]">
                            <div class="flex-1 min-w-0">
                                <p class="text-sm text-gray-200 truncate">{{ $item['title'] }}</p>
                                <p class="text-xs text-indigo-400 mt-0.5">r/{{ $item['subreddit'] }}</p>
                            </div>
                            <span class="shrink-0 inline-flex items-center gap-1 rounded-full border text-xs px-2.5 py-1 font-medium {{ $item['hot'] ? 'bg-red-500/20 text-red-400 border-red-500/20' : 'bg-orange-500/20 text-orange-400 border-orange-500/20' }}">
                                {{ $item['hot'] ? __('Hot') : __('Warm') }} {{ $item['score'] }}
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    {{-- Use Cases --}}
    <section class="py-20 px-4 border-t border-white/[0.04]">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-white text-center mb-3">{{ __('Who is ReplyRadar for') }}</h2>
            <p class="text-gray-400 text-center mb-12">{{ __('Find your use case and start today') }}</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
                @foreach([
                    ['icon' => '🧑', 'role' => __('Indie Hackers'), 'desc' => __('Validate business ideas before writing a single line of code.')],
                    ['icon' => '📣', 'role' => __('Creators'), 'desc' => __('Find what questions your audience is asking this week.')],
                    ['icon' => '🏢', 'role' => __('SaaS founders'), 'desc' => __('Detect competitor users\' pain points in real-time.')],
                    ['icon' => '🎯', 'role' => __('Marketers'), 'desc' => __('Find conversations where your product is the perfect answer.')],
                ] as $uc)
                    <div class="glass-card !p-6 text-center">
                        <div class="text-3xl mb-3">{{ $uc['icon'] }}</div>
                        <h3 class="text-sm font-bold text-white mb-2">{{ $uc['role'] }}</h3>
                        <p class="text-sm text-gray-400 leading-relaxed">{{ $uc['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Features --}}
    <section id="features" class="py-20 px-4 border-t border-white/[0.04] grid-bg">
        <div class="max-w-5xl mx-auto">
            <h2 class="text-3xl font-bold text-white text-center mb-3">{{ __('Everything you need') }}</h2>
            <p class="text-gray-400 text-center mb-12">{{ __('No complex setup. No APIs to connect.') }}</p>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach([
                    ['icon' => '🎯', 'title' => __('Real buyer intent'), 'desc' => __('Our engine detects if a user is actively seeking a solution, not just browsing.')],
                    ['icon' => '⚡', 'title' => __('Auto updates'), 'desc' => __('Scans Reddit every 30 minutes. Open the dashboard and opportunities are already ranked.')],
                    ['icon' => '📊', 'title' => __('Opportunity score'), 'desc' => __('Each post gets a 0-100 score combining intent, relevance, and engagement.')],
                    ['icon' => '🔍', 'title' => __('Multi-keyword'), 'desc' => __('Create projects with multiple keywords. ReplyRadar monitors them all.')],
                    ['icon' => '💾', 'title' => __('Export to CSV'), 'desc' => __('Export your opportunities to CSV for your CRM, Notion, or anywhere else.')],
                    ['icon' => '🚀', 'title' => __('No technical setup'), 'desc' => __('Register in 30 seconds. Add your first keyword and get results in 2 minutes.')],
                ] as $f)
                    <div class="glass-card !p-6">
                        <div class="text-3xl mb-4">{{ $f['icon'] }}</div>
                        <h3 class="text-sm font-bold text-white mb-2">{{ $f['title'] }}</h3>
                        <p class="text-sm text-gray-400 leading-relaxed">{{ $f['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Pricing --}}
    <section id="pricing" class="py-20 px-4 border-t border-white/[0.04]">
        <div class="max-w-4xl mx-auto">
            <h2 class="text-3xl font-bold text-white text-center mb-3">{{ __('Simple pricing') }}</h2>
            <p class="text-gray-400 text-center mb-12">{{ __('No surprises. No contracts. Cancel anytime.') }}</p>
            <div class="grid sm:grid-cols-3 gap-4">
                @php
                    $plans = [
                        ['name' => 'Free', 'price' => '$0', 'period' => '', 'features' => [__('1 project'), __('5 keywords'), __('50 opportunities/mo')], 'cta' => __('Get started free'), 'featured' => false],
                        ['name' => 'Pro', 'price' => '$29', 'period' => __('/mo'), 'features' => [__('5 projects'), __('50 keywords'), __('Unlimited opportunities'), __('Export to CSV'), __('Email alerts')], 'cta' => __('Start Pro'), 'featured' => true],
                        ['name' => 'Business', 'price' => '$99', 'period' => __('/mo'), 'features' => [__('Everything unlimited'), __('API access'), __('Multi-source'), __('Priority support')], 'cta' => __('Contact'), 'featured' => false],
                    ];
                @endphp
                @foreach($plans as $plan)
                    <div class="glass !p-8 {{ $plan['featured'] ? 'gradient-border' : '' }}" {{ $plan['featured'] ? 'style=border-color:transparent' : '' }}>
                        @if($plan['featured'])
                            <div class="text-xs font-bold text-indigo-400 bg-indigo-500/10 rounded-full px-3 py-1 inline-block mb-3">{{ __('Most popular') }}</div>
                        @endif
                        <div class="text-lg font-bold text-white">{{ $plan['name'] }}</div>
                        <div class="text-4xl font-extrabold text-white mt-2">
                            {{ $plan['price'] }}
                            @if($plan['period'])<span class="text-lg font-normal text-gray-400">{{ $plan['period'] }}</span>@endif
                        </div>
                        <ul class="mt-5 mb-6 space-y-2">
                            @foreach($plan['features'] as $f)
                                <li class="flex items-center gap-2 text-sm text-gray-300">
                                    <span class="text-green-400 font-bold">✓</span> {{ $f }}
                                </li>
                            @endforeach
                        </ul>
                        <a href="{{ route('register') }}"
                            class="block text-center py-3 rounded-xl text-sm font-bold transition-all duration-200
                            {{ $plan['featured'] ? 'glass-btn-primary' : 'glass-btn-secondary' }}">
                            {{ $plan['cta'] }}
                        </a>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-20 px-4 border-t border-white/[0.04] text-center">
        <div class="max-w-2xl mx-auto">
            <h2 class="text-3xl sm:text-4xl font-bold text-white mb-4">{{ __('Start spotting opportunities today') }}</h2>
            <p class="text-gray-400 mb-8">{{ __('Join early adopters using real intelligence to find opportunities on Reddit.') }}</p>
            <a href="{{ route('register') }}" class="glass-btn-primary !px-10 !py-4 text-base font-semibold inline-block">
                {{ __('Create free account') }}
            </a>
            <p class="text-sm text-gray-500 mt-4">{{ __('No card · 30-second setup') }}</p>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-white/[0.04] py-6 px-4">
        <div class="max-w-6xl mx-auto flex items-center justify-between flex-wrap gap-4">
            <span class="text-sm text-gray-500">{{ __('ReplyRadar 2026') }}</span>
            <div class="flex gap-6">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">{{ __('Login') }}</a>
                <a href="{{ route('register') }}" class="text-sm text-gray-500 hover:text-gray-300 transition-colors">{{ __('Register') }}</a>
            </div>
        </div>
    </footer>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
