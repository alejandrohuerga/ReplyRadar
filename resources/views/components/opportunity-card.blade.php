@props(['post', 'blurredIds' => collect([])])

@php $isBlurred = $blurredIds->contains($post->id); @endphp

@php
    $score = round($post->final_score);
    $intentScore = round($post->intent_score);
    $matchScore = round($post->match_score);
    $redditScore = $post->reddit_score ?? 0;

    if ($score >= 40) { $badge = [__('Hot'), 'bg-red-500/20 text-red-400 border-red-500/20']; }
    elseif ($score >= 25) { $badge = [__('Warm'), 'bg-orange-500/20 text-orange-400 border-orange-500/20']; }
    elseif ($score >= 15) { $badge = [__('Cool'), 'bg-yellow-500/20 text-yellow-400 border-yellow-500/20']; }
    else { $badge = [__('Cold'), 'bg-gray-500/20 text-gray-400 border-gray-500/20']; }

    if ($matchScore >= 80) { $fire = '🔥🔥🔥'; $fireClass = 'text-red-400 drop-shadow-[0_0_12px_rgba(248,113,113,0.6)]'; $fireBar = 'from-red-500 via-orange-400 to-yellow-300'; }
    elseif ($matchScore >= 60) { $fire = '🔥🔥'; $fireClass = 'text-orange-400 drop-shadow-[0_0_8px_rgba(251,146,60,0.4)]'; $fireBar = 'from-orange-500 via-yellow-400 to-yellow-300'; }
    elseif ($matchScore >= 40) { $fire = '🔥'; $fireClass = 'text-yellow-400'; $fireBar = 'from-yellow-500 to-yellow-300'; }
    else { $fire = ''; $fireClass = 'text-gray-500'; $fireBar = 'from-gray-500 to-gray-400'; }
@endphp

<div class="glass-card !p-5">
    @if($isBlurred)
        <div class="flex items-center justify-between -mx-5 -mt-5 mb-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600/30 to-purple-600/20 border-b border-white/10 rounded-t-xl">
            <div class="flex items-center gap-2">
                <span class="text-sm">🔒</span>
                <span class="text-xs text-gray-300 font-medium">{{ __('Premium opportunity') }}</span>
            </div>
            <a href="{{ route('billing.plans') }}"
                class="text-xs text-indigo-300 hover:text-white font-medium transition-colors">
                {{ __('Upgrade') }} →
            </a>
        </div>
    @endif
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0 {{ $isBlurred ? 'blur-sm' : '' }}">
            <a href="{{ $post->url }}" target="_blank" rel="noopener noreferrer"
                class="text-sm font-medium text-gray-100 hover:text-indigo-400 line-clamp-2 transition-colors">
                {{ $post->title }}
            </a>
            <div class="flex items-center gap-3 mt-2 flex-wrap">
                <span class="text-xs text-indigo-400 font-medium">r/{{ $post->subreddit }}</span>
                <span class="text-xs text-gray-500">↑ {{ $redditScore }}</span>
                <span class="text-xs text-gray-500">💬 {{ $post->num_comments ?? 0 }}</span>
                @if($post->posted_at)
                    <span class="text-xs text-gray-500">{{ \Carbon\Carbon::parse($post->posted_at)->locale(app()->getLocale())->isoFormat('D MMM YYYY') }}</span>
                @endif
            </div>
        </div>

        <div class="shrink-0 flex flex-col items-center gap-1">
            <div class="text-lg leading-none {{ $fireClass }} transition-all duration-300">
                {{ $fire }}
            </div>
            <div class="flex items-center gap-1">
                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">{{ __('Match') }}</span>
                <span class="text-sm font-extrabold {{ $matchScore >= 60 ? 'text-white' : ($matchScore >= 40 ? 'text-yellow-300' : 'text-gray-400') }}">
                    {{ $matchScore }}
                </span>
            </div>
            <div class="w-16 h-1.5 bg-white/[0.06] rounded-full overflow-hidden {{ $isBlurred ? 'blur-sm' : '' }}">
                <div class="h-full rounded-full bg-gradient-to-r {{ $fireBar }}" style="width: {{ $matchScore }}%"></div>
            </div>
        </div>

        <div class="shrink-0 {{ $isBlurred ? 'blur-sm' : '' }}">
            <span class="inline-flex items-center gap-1 rounded-full border text-[10px] px-2 py-0.5 font-medium {{ $badge[1] }}">
                <span class="font-bold">{{ $score }}</span>
            </span>
        </div>
    </div>

    <div class="mt-3 pt-3 border-t border-white/[0.06] grid grid-cols-3 gap-2 {{ $isBlurred ? 'blur-sm' : '' }}">
        <div class="text-center">
            <div class="text-xs text-gray-500">{{ __('Intent') }}</div>
            <div class="text-sm font-semibold text-gray-200">{{ $intentScore }}</div>
            <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ $intentScore }}%; background: linear-gradient(90deg, #6366f1, #818cf8)"></div>
            </div>
        </div>
        <div class="text-center">
            <div class="text-xs text-gray-500">{{ __('Match') }}</div>
            <div class="text-sm font-semibold text-gray-200">{{ $matchScore }}</div>
            <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ $matchScore }}%; background: linear-gradient(90deg, #a855f7, #c084fc)"></div>
            </div>
        </div>
        <div class="text-center">
            <div class="text-xs text-gray-500">{{ __('Engagement') }}</div>
            <div class="text-sm font-semibold text-gray-200">{{ $redditScore }}</div>
            <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: {{ min(100, $redditScore * 2) }}%; background: linear-gradient(90deg, #f97316, #fb923c)"></div>
            </div>
        </div>
    </div>
</div>
