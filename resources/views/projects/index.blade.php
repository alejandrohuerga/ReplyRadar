@extends('layouts.app')

@section('title', __('Projects'))

@section('content')
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white">{{ __('Projects') }}</h1>
        <p class="text-gray-400 mt-1 text-sm">{{ __('Each project groups related keywords to detect opportunities') }}</p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="glass !p-6">
                <h2 class="text-base font-semibold text-white mb-4">{{ __('New project') }}</h2>
                @if(!$canCreate)
                    <div class="mb-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-sm text-amber-400">
                        {{ __("You've reached your plan limit.") }}
                        <a href="{{ route('billing.plans') }}" class="underline font-medium ml-1">{{ __('Upgrade') }}</a>
                    </div>
                @endif
                <form method="POST" action="{{ route('projects.store') }}" class="space-y-3">
                    @csrf
                    <input type="text" name="name" placeholder="{{ __('Project name') }}" value="{{ old('name') }}"
                        {{ !$canCreate ? 'disabled' : '' }}
                        class="glass-input {{ !$canCreate ? 'opacity-50' : '' }}">
                    @error('name') <p class="text-xs text-red-400 mt-1">{{ $message }}</p> @enderror
                    <textarea name="description" placeholder="{{ __('Description (optional)') }}" rows="3"
                        {{ !$canCreate ? 'disabled' : '' }}
                        class="glass-input resize-none {{ !$canCreate ? 'opacity-50' : '' }}">{{ old('description') }}</textarea>
                    <button type="submit" {{ !$canCreate ? 'disabled' : '' }}
                        class="glass-btn-primary w-full {{ !$canCreate ? 'opacity-50 cursor-not-allowed' : '' }}">
                        {{ __('Create project') }}
                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-3">
            @forelse($projects as $project)
                <div class="glass-card !p-5">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <a href="{{ route('projects.show', $project) }}" class="font-semibold text-white hover:text-indigo-400 transition-colors">
                                {{ $project->name }}
                            </a>
                            @if($project->description)
                                <p class="text-sm text-gray-400 mt-1">{{ $project->description }}</p>
                            @endif
                            <div class="flex gap-4 mt-3">
                                <span class="text-xs text-gray-500">🔑 {{ $project->keywords_count }} {{ __('keywords') }}</span>
                                <span class="text-xs text-gray-500">📊 {{ $project->posts_count }} {{ __('posts') }}</span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="{{ route('projects.show', $project) }}"
                                class="glass-btn-primary !px-3 !py-1.5 text-xs">
                                {{ __('View →') }}
                            </a>
                            <form method="POST" action="{{ route('projects.destroy', $project) }}" class="inline"
                                onsubmit="return confirm('{{ __('Delete project «{name}»?') }}'.replace('{name}', '{{ $project->name }}'))">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="glass-btn-danger !px-3 !py-1.5 text-xs">✕</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="glass !p-12 text-center">
                    <div class="text-4xl mb-3">📁</div>
                    <p class="text-gray-500 text-sm">{{ __('Create your first project to get started') }}</p>
                </div>
            @endforelse
        </div>
    </div>
@endsection
