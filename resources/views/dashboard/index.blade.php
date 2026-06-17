@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ __('Opportunities') }}</h1>
            <p class="text-sm text-gray-400 mt-1">{{ __('Conversations ranked by real buyer intent') }}</p>
        </div>
        <a href="{{ route('projects.index') }}" class="glass-btn-primary">
            {{ __('+ New project') }}
        </a>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1">{{ __('Total detected') }}</div>
            <div class="text-xl font-bold text-white">{{ $stats['total_posts'] }}</div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1">{{ __('Hot') }}</div>
            <div class="text-xl font-bold text-red-400">{{ $stats['hot_count'] }}</div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1">{{ __('Avg score') }}</div>
            <div class="text-xl font-bold text-indigo-400">{{ $stats['avg_score'] }}</div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1">{{ __('Top subreddit') }}</div>
            <div class="text-xl font-bold text-purple-400">{{ $stats['top_subreddit'] ? 'r/'.$stats['top_subreddit'] : '—' }}</div>
        </div>
    </div>

    {{-- Sin proyectos --}}
    @if(count($projects) === 0)
        <div class="glass !p-16 text-center">
            <div class="text-4xl mb-3">🎯</div>
            <h3 class="text-lg font-medium text-white mb-2">{{ __('Create your first project') }}</h3>
            <p class="text-sm text-gray-400 mb-6">{{ __('Add keywords and ReplyRadar will automatically detect opportunities on Reddit.') }}</p>
            <a href="{{ route('projects.index') }}" class="glass-btn-primary inline-block">
                {{ __('Create project') }}
            </a>
        </div>
    @else
        <div x-data="{ filter: 'all', source: 'all', search: '' }">
        {{-- Filtros + búsqueda --}}
        <div class="flex items-center gap-3 mb-4 flex-wrap">
            <input type="text" placeholder="{{ __('Search opportunities...') }}" x-model="search"
                class="glass-input flex-1 min-w-48 text-sm">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">{{ __('All') }}</button>
            <button @click="filter = 'hot'" :class="filter === 'hot' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">🔥 {{ __('Hot') }}</button>
            <button @click="filter = 'warm'" :class="filter === 'warm' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">⚡ {{ __('Warm') }}</button>
            <span class="w-px h-6 bg-white/10"></span>
            <button @click="source = 'all'" :class="source === 'all' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">{{ __('All sources') }}</button>
            <button @click="source = 'reddit'" :class="source === 'reddit' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">
                <span class="text-indigo-400 font-bold">Reddit</span>
            </button>
            <button @click="source = 'mastodon'" :class="source === 'mastodon' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">
                <span class="text-purple-400 font-bold">Mastodon</span>
            </button>
            <div x-data="{ open: false, selected: '{{ $sort }}' }" class="relative ml-auto">
                <button @click="open = !open" type="button"
                    class="glass-input text-sm !py-1.5 !px-3 flex items-center gap-2 min-w-[130px] cursor-pointer whitespace-nowrap">
                    <span x-text="selected === 'final_score' ? '{{ __('By score') }}' : '{{ __('By match') }}'"></span>
                    <span class="ml-auto text-gray-500">▾</span>
                </button>
                <div x-show="open" @click.outside="open = false"
                    class="absolute right-0 mt-1 w-full min-w-[140px] rounded-xl bg-black border border-white/10 shadow-xl z-50 overflow-hidden"
                    style="display: none;">
                    <button @click="selected='final_score'; open=false; window.location='{{ route('dashboard') }}?sort=final_score'"
                        class="block w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 transition-colors"
                        :class="selected === 'final_score' ? 'bg-white/10' : ''">
                        {{ __('By score') }}
                    </button>
                    <button @click="selected='match_score'; open=false; window.location='{{ route('dashboard') }}?sort=match_score'"
                        class="block w-full text-left px-3 py-2 text-sm text-white hover:bg-white/10 transition-colors"
                        :class="selected === 'match_score' ? 'bg-white/10' : ''">
                        {{ __('By match') }}
                    </button>
                </div>
            </div>
            <span class="text-sm text-gray-500" x-text="'{{ $opportunities->count() }} {{ __('results') }}'"></span>
            <form method="POST" action="{{ route('dashboard.refresh') }}" class="inline">
                @csrf
                <button type="submit" class="glass-btn-primary !px-3 !py-1.5 text-sm whitespace-nowrap">
                    🔄 {{ __('Refresh') }}
                </button>
            </form>
        </div>

        @if($blurredIds->isNotEmpty())
            <div class="mb-4 p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-yellow-500/10 to-orange-500/10 border border-amber-500/20 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🚀</span>
                    <div>
                        <p class="text-sm text-amber-100 font-medium">{{ __('Unlock big opportunities for only') }} <span class="text-amber-300 font-bold">€14,99</span></p>
                        <p class="text-xs text-amber-400/70">{{ __('Offer for the first 100 users this week') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-center">
                        <div class="text-lg font-bold text-amber-300">22</div>
                        <div class="text-[10px] text-amber-400/60 uppercase tracking-wider">{{ __('spots left') }}</div>
                    </div>
                    <form method="POST" action="{{ route('billing.promo14') }}" class="inline">
                        @csrf
                        <button type="submit"
                            class="whitespace-nowrap text-sm glass-btn-primary !px-4 !py-2">
                            {{ __('Upgrade now') }} →
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Lista --}}
        <div class="grid gap-3">
            @forelse($opportunities as $post)
                @php
                    $isHot = $post->final_score >= 60;
                    $isWarm = $post->final_score >= 40 && $post->final_score < 60;
                @endphp
                <div class="dashboard-post"
                    x-show='
                        (search === "" ||
                         @json($post->localized_title).toLowerCase().includes(search.toLowerCase()) ||
                         @json($post->subreddit).toLowerCase().includes(search.toLowerCase()) ||
                         @json($post->source ?? 'reddit').toLowerCase().includes(search.toLowerCase())
                        ) &&
                        (filter === "all" ||
                         (filter === "hot" && {{ $isHot ? 'true' : 'false' }}) ||
                         (filter === "warm" && {{ $isWarm ? 'true' : 'false' }})
                        ) &&
                        (source === "all" ||
                         source === @json($post->source ?? 'reddit')
                        )
                    '
                >
                    <x-opportunity-card :post="$post" :blurred-ids="$blurredIds" />
                </div>
            @empty
                <div class="glass !p-12 text-center">
                    <p class="text-gray-500 text-sm">{{ __('No opportunities match these filters') }}</p>
                </div>
            @endforelse
        </div>
    </div>
    @endif
@endsection
