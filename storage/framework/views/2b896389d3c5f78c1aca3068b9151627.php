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

<?php $isBlurred = $blurredIds->contains($post->id); ?>

<?php
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
?>

<div class="glass-card !p-5">
    <?php if($isBlurred): ?>
        <div class="flex items-center justify-between -mx-5 -mt-5 mb-4 px-5 py-2.5 bg-gradient-to-r from-indigo-600/30 to-purple-600/20 border-b border-white/10 rounded-t-xl">
            <div class="flex items-center gap-2">
                <span class="text-sm">🔒</span>
                <span class="text-xs text-gray-300 font-medium"><?php echo e(__('Premium opportunity')); ?></span>
            </div>
            <a href="<?php echo e(route('billing.plans')); ?>"
                class="text-xs text-indigo-300 hover:text-white font-medium transition-colors">
                <?php echo e(__('Upgrade')); ?> →
            </a>
        </div>
    <?php endif; ?>
    <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0 <?php echo e($isBlurred ? 'blur-sm' : ''); ?>">
            <a href="<?php echo e($post->url); ?>" target="_blank" rel="noopener noreferrer"
                class="text-sm font-medium text-gray-100 hover:text-indigo-400 line-clamp-2 transition-colors">
                <?php echo e($post->title); ?>

            </a>
            <div class="flex items-center gap-3 mt-2 flex-wrap">
                <span class="text-xs text-indigo-400 font-medium">r/<?php echo e($post->subreddit); ?></span>
                <span class="text-xs text-gray-500">↑ <?php echo e($redditScore); ?></span>
                <span class="text-xs text-gray-500">💬 <?php echo e($post->num_comments ?? 0); ?></span>
                <?php if($post->posted_at): ?>
                    <span class="text-xs text-gray-500"><?php echo e(\Carbon\Carbon::parse($post->posted_at)->locale(app()->getLocale())->isoFormat('D MMM YYYY')); ?></span>
                <?php endif; ?>
            </div>
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
            <div class="w-16 h-1.5 bg-white/[0.06] rounded-full overflow-hidden <?php echo e($isBlurred ? 'blur-sm' : ''); ?>">
                <div class="h-full rounded-full bg-gradient-to-r <?php echo e($fireBar); ?>" style="width: <?php echo e($matchScore); ?>%"></div>
            </div>
        </div>

        <div class="shrink-0 <?php echo e($isBlurred ? 'blur-sm' : ''); ?>">
            <span class="inline-flex items-center gap-1 rounded-full border text-[10px] px-2 py-0.5 font-medium <?php echo e($badge[1]); ?>">
                <span class="font-bold"><?php echo e($score); ?></span>
            </span>
        </div>
    </div>

    <div class="mt-3 pt-3 border-t border-white/[0.06] grid grid-cols-3 gap-2 <?php echo e($isBlurred ? 'blur-sm' : ''); ?>">
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
            <div class="text-xs text-gray-500"><?php echo e(__('Engagement')); ?></div>
            <div class="text-sm font-semibold text-gray-200"><?php echo e($redditScore); ?></div>
            <div class="mt-1 h-1 bg-white/[0.06] rounded-full overflow-hidden">
                <div class="h-full rounded-full" style="width: <?php echo e(min(100, $redditScore * 2)); ?>%; background: linear-gradient(90deg, #f97316, #fb923c)"></div>
            </div>
        </div>
    </div>
</div>
<?php /**PATH C:\laragon\www\ReplyRadar\resources\views/components/opportunity-card.blade.php ENDPATH**/ ?>