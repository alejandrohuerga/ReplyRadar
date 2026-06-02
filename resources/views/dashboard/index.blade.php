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
            <div class="text-xs text-gray-500 mb-1">{{ __('Hot (score 80+)') }}</div>
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
        {{-- Filtros + búsqueda --}}
        <div class="flex items-center gap-3 mb-4 flex-wrap" x-data="{ filter: 'all', search: '' }">
            <input type="text" placeholder="{{ __('Search opportunities...') }}" x-model="search"
                class="glass-input flex-1 min-w-48 text-sm">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">{{ __('All') }}</button>
            <button @click="filter = 'hot'" :class="filter === 'hot' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">🔥 {{ __('Hot') }}</button>
            <button @click="filter = 'warm'" :class="filter === 'warm' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">⚡ {{ __('Warm') }}</button>
            <span class="text-sm text-gray-500" x-text="'{{ $opportunities->count() }} {{ __('results') }}'"></span>
        </div>

        {{-- Lista --}}
        <div class="grid gap-3">
            @forelse($opportunities as $post)
                @php
                    $isHot = $post->final_score >= 80;
                    $isWarm = $post->final_score >= 60 && $post->final_score < 80;
                @endphp
                <div class="dashboard-post"
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="
                        $watch('filter', val => {
                            show = (val === 'all' || (val === 'hot' && {{ $isHot ? 'true' : 'false' }}) || (val === 'warm' && {{ $isWarm ? 'true' : 'false' }}));
                        });
                        $watch('search', val => {
                            const t = '{{ addslashes($post->title) }}'.toLowerCase();
                            const s = '{{ addslashes($post->subreddit) }}'.toLowerCase();
                            show = (t.includes(val.toLowerCase()) || s.includes(val.toLowerCase())) &&
                                (filter === 'all' || (filter === 'hot' && {{ $isHot ? 'true' : 'false' }}) || (filter === 'warm' && {{ $isWarm ? 'true' : 'false' }}));
                        });
                    "
                >
                    <x-opportunity-card :post="$post" />
                </div>
            @empty
                <div class="glass !p-12 text-center">
                    <p class="text-gray-500 text-sm">{{ __('No opportunities match these filters') }}</p>
                </div>
            @endforelse
        </div>
    @endif
@endsection
