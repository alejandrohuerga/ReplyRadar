<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['post', 'blurredIds' => collect([])]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['post', 'blurredIds' => collect([])]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $isBlurred = $blurredIds->contains($post->id);
    $isTwitter = ($post->source ?? 'reddit') === 'twitter';
    $isMastodon = ($post->source ?? 'reddit') === 'mastodon';

    $score = round($post->final_score);
    $intentScore = round($post->intent_score);
    $matchScore = round($post->match_score);
    $redditScore = $post->reddit_score ?? 0;

    if ($score >= 60) { $badge = [__('Hot'), 'bg-red-500/20 text-red-400 border-red-500/20']; }
    elseif ($score >= 40) { $badge = [__('Warm'), 'bg-orange-500/20 text-orange-400 border-orange-500/20']; }
    elseif ($score >= 20) { $badge = [__('Cool'), 'bg-yellow-500/20 text-yellow-400 border-yellow-500/20']; }
    else { $badge = [__('Cold'), 'bg-gray-500/20 text-gray-400 border-gray-500/20']; }

    if ($matchScore >= 80) { $fire = '🔥🔥🔥'; $fireClass = 'text-red-400 drop-shadow-[0_0_12px_rgba(248,113,113,0.6)]'; $fireBar = 'from-red-500 via-orange-400 to-yellow-300'; }
    elseif ($matchScore >= 60) { $fire = '🔥🔥'; $fireClass = 'text-orange-400 drop-shadow-[0_0_8px_rgba(251,146,60,0.4)]'; $fireBar = 'from-orange-500 via-yellow-400 to-yellow-300'; }
    elseif ($matchScore >= 40) { $fire = '🔥'; $fireClass = 'text-yellow-400'; $fireBar = 'from-yellow-500 to-yellow-300'; }
    else { $fire = ''; $fireClass = 'text-gray-500'; $fireBar = 'from-gray-500 to-gray-400'; }
?>

<?php if($isBlurred): ?>
    <a href="<?php echo e(route('billing.plans')); ?>"
        class="glass-card !p-5 block group hover:bg-white/[0.07] transition-all cursor-pointer">
        <div class="flex flex-col items-center justify-center py-5 text-center">
            <span class="text-3xl mb-1 block">⭐</span>
            <span class="text-sm font-bold text-white block"><?php echo e(__('Premium')); ?></span>
            <span class="text-xs text-gray-400 block"><?php echo e(__('Members only')); ?></span>
            <div class="flex items-center gap-4 mt-3">
                <div class="text-lg leading-none <?php echo e($fireClass); ?>"><?php echo e($fire); ?></div>
                <span class="inline-flex items-center gap-1 rounded-full border text-[10px] px-2 py-0.5 font-medium <?php echo e($badge[1]); ?>">
                    <span class="font-bold"><?php echo e($score); ?></span>
                </span>
                <div class="flex items-center gap-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?php echo e(__('Match')); ?></span>
                    <span class="text-sm font-extrabold <?php echo e($matchScore >= 60 ? 'text-white' : ($matchScore >= 40 ? 'text-yellow-300' : 'text-gray-400')); ?>">
                        <?php echo e($matchScore); ?>

                    </span>
                </div>
                <div class="w-16 h-1.5 bg-white/[0.06] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r <?php echo e($fireBar); ?>" style="width: <?php echo e($matchScore); ?>%"></div>
                </div>
            </div>
        </div>
    </a>
<?php else: ?>
    <div class="glass-card !p-5">
        <div class="flex items-start justify-between gap-4">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                    <?php if($isTwitter): ?>
                        <span class="shrink-0 text-[10px] font-bold text-sky-400 bg-sky-500/10 rounded px-1.5 py-0.5"><?php echo e(__('X')); ?></span>
                    <?php elseif($isMastodon): ?>
                        <span class="shrink-0 text-[10px] font-bold text-purple-400 bg-purple-500/10 rounded px-1.5 py-0.5">Mastodon</span>
                    <?php else: ?>
                        <span class="shrink-0 text-[10px] font-bold text-indigo-400 bg-indigo-500/10 rounded px-1.5 py-0.5">Reddit</span>
                    <?php endif; ?>
                    <a href="<?php echo e($post->url); ?>" target="_blank" rel="noopener noreferrer"
                        class="text-sm font-medium text-gray-100 hover:text-indigo-400 line-clamp-2 transition-colors">
                        <?php echo e($post->localized_title); ?>

                    </a>
                </div>
                <div class="flex items-center gap-3 mt-2 flex-wrap">
                    <?php if($isTwitter): ?>
                        <span class="text-xs text-sky-400 font-medium"><?php echo e($post->author_handle ?? __('Twitter')); ?></span>
                        <span class="text-xs text-gray-500">♥ <?php echo e($post->like_count ?? 0); ?></span>
                        <span class="text-xs text-gray-500">🔁 <?php echo e($post->retweet_count ?? 0); ?></span>
                        <span class="text-xs text-gray-500">💬 <?php echo e($post->reply_count ?? 0); ?></span>
                    <?php elseif($isMastodon): ?>
                        <span class="text-xs text-purple-400 font-medium"><?php echo e($post->author_handle ?? __('Mastodon')); ?></span>
                        <span class="text-xs text-gray-500">⭐ <?php echo e($post->like_count ?? 0); ?></span>
                        <span class="text-xs text-gray-500">🔁 <?php echo e($post->retweet_count ?? 0); ?></span>
                        <span class="text-xs text-gray-500">💬 <?php echo e($post->reply_count ?? 0); ?></span>
                    <?php else: ?>
                        <span class="text-xs text-indigo-400 font-medium">r/<?php echo e($post->subreddit); ?></span>
                        <span class="text-xs text-gray-500">↑ <?php echo e($redditScore); ?></span>
                        <span class="text-xs text-gray-500">💬 <?php echo e($post->num_comments ?? 0); ?></span>
                    <?php endif; ?>
                    <?php if($post->posted_at): ?>
                        <span class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($post->posted_at)->locale(app()->getLocale())->isoFormat('D MMM YYYY')); ?></span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="shrink-0">
                <span class="inline-flex items-center gap-1 rounded-full border text-[10px] px-2 py-0.5 font-medium <?php echo e($badge[1]); ?>">
                    <span class="font-bold"><?php echo e($score); ?></span>
                </span>
            </div>

            <div class="shrink-0 flex flex-col items-center gap-1">
                <div class="text-lg leading-none <?php echo e($fireClass); ?> transition-all duration-300">
                    <?php echo e($fire); ?>

                </div>
                <div class="flex items-center gap-1">
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider"><?php echo e(__('Match')); ?></span>
                    <span class="text-sm font-extrabold <?php echo e($matchScore >= 60 ? 'text-white' : ($matchScore >= 40 ? 'text-yellow-300' : 'text-gray-400')); ?>">
                        <?php echo e($matchScore); ?>

                    </span>
                </div>
                <div class="w-16 h-1.5 bg-white/[0.06] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r <?php echo e($fireBar); ?>" style="width: <?php echo e($matchScore); ?>%"></div>
                </div>
            </div>
        </div>

            <div class="mt-3 pt-3 border-t border-white/[0.06] grid grid-cols-3 gap-2">
                <div class="text-center">
                    <div class="text-xs text-gray-500"><?php echo e(__('Intent')); ?></div>
                    <div class="text-sm font-semibold text-gray-200"><?php echo e($intentScore); ?></div>
                    <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: <?php echo e($intentScore); ?>%; background: linear-gradient(90deg, #6366f1, #818cf8)"></div>
                    </div>
                </div>
                <div class="text-center">
                    <div class="text-xs text-gray-500"><?php echo e(__('Match')); ?></div>
                    <div class="text-sm font-semibold text-gray-200"><?php echo e($matchScore); ?></div>
                    <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: <?php echo e($matchScore); ?>%; background: linear-gradient(90deg, #a855f7, #c084fc)"></div>
                    </div>
                </div>
                <div class="text-center">
                    <?php
                        $isSocial = $isTwitter || $isMastodon;
                        $likes = $isSocial ? ($post->like_count ?? 0) : $redditScore;
                    ?>
                    <div class="text-xs text-gray-500"><?php echo e($isSocial ? __('Likes') : __('Engagement')); ?></div>
                    <div class="text-sm font-semibold text-gray-200"><?php echo e($likes); ?></div>
                    <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: <?php echo e(min(100, $likes * 2)); ?>%; background: linear-gradient(90deg, #f97316, #fb923c)"></div>
                    </div>
                </div>
            </div>
    </div>
<?php endif; ?>
<?php /**PATH C:\laragon\www\ReplyRadar\resources\views\components\opportunity-card.blade.php ENDPATH**/ ?>