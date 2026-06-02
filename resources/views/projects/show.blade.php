@extends('layouts.app')

@section('title', $project->name)

@section('content')
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="{{ route('projects.index') }}" class="hover:text-indigo-400 transition-colors">{{ __('Projects') }}</a>
        <span>/</span>
        <span class="text-gray-200 font-medium">{{ $project->name }}</span>
    </div>

    <div class="grid lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="glass !p-4">
                <h3 class="text-sm font-semibold text-white mb-3">{{ __('Keywords') }}</h3>

                <form method="POST" action="{{ route('keywords.store', $project) }}" class="mb-4">
                    @csrf
                    <div class="flex gap-2">
                        <input type="text" name="term" placeholder="{{ __('New keyword...') }}"
                            value="{{ old('term') }}"
                            {{ !$canAddKeyword ? 'disabled' : '' }}
                            class="glass-input flex-1 text-sm {{ !$canAddKeyword ? 'opacity-50' : '' }}">
                        <button type="submit" {{ !$canAddKeyword ? 'disabled' : '' }}
                            class="glass-btn-primary !px-3 !py-2 {{ !$canAddKeyword ? 'opacity-50 cursor-not-allowed' : '' }}">
                            +
                        </button>
                    </div>
                    @error('term') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    @if(!$canAddKeyword)
                        <p class="text-xs text-amber-400 mt-1">
                            {{ __('Limit reached.') }} <a href="{{ route('billing.plans') }}" class="underline">{{ __('Upgrade') }}</a>
                        </p>
                    @endif
                </form>

                <div class="space-y-2">
                    @forelse($project->keywords as $kw)
                        <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white/[0.03]">
                            <span class="text-sm {{ $kw->is_active ? 'text-gray-200' : 'text-gray-500 line-through' }}">
                                {{ $kw->term }}
                            </span>
                            <div class="flex gap-1">
                                <form method="POST" action="{{ route('keywords.toggle', $kw) }}" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs text-gray-500 hover:text-indigo-400 transition-colors" title="{{ $kw->is_active ? __('Pause') : __('Activate') }}">
                                        {{ $kw->is_active ? '⏸' : '▶' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('keywords.destroy', $kw) }}" class="inline"
                                    onsubmit="return confirm('{{ __('Delete keyword «{term}»?') }}'.replace('{term}', '{{ $kw->term }}'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition-colors">✕</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500 text-center py-4">{{ __('No keywords yet') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">
                    {{ $posts->count() }} {{ __('opportunities detected') }}
                </h2>
                <div class="flex items-center gap-3">
                    <form method="GET" action="{{ route('projects.show', $project) }}" id="sort-form">
                        <select name="sort" onchange="this.form.submit()"
                            class="glass-input text-sm !py-1.5 !px-3">
                            <option value="final_score" {{ request('sort') === 'final_score' ? 'selected' : '' }}>{{ __('Sort by score') }}</option>
                            <option value="posted_at" {{ request('sort') === 'posted_at' ? 'selected' : '' }}>{{ __('Sort by date') }}</option>
                        </select>
                    </form>
                    @if($canExport)
                        <a href="{{ route('export.posts', ['project_id' => $project->id]) }}"
                            class="glass-btn-primary !px-4 !py-1.5 text-sm">
                            {{ __('Export CSV') }}
                        </a>
                    @else
                        <span class="glass-btn-secondary !px-4 !py-1.5 text-sm opacity-50 cursor-not-allowed"
                            title="{{ __('Available on Pro plan') }}">
                            {{ __('Export CSV') }}
                        </span>
                    @endif
                </div>
            </div>

            @if($posts->isEmpty())
                <div class="glass !p-16 text-center">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-gray-500 text-sm">{{ __('Add a keyword to start detecting opportunities') }}</p>
                </div>
            @else
                <div class="grid gap-3">
                    @foreach($posts as $post)
                        <x-opportunity-card :post="$post" :blurred-ids="$blurredIds" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
