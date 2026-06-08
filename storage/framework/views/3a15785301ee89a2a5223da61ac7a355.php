<?php $__env->startSection('title', __('Dashboard')); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold text-white"><?php echo e(__('Opportunities')); ?></h1>
            <p class="text-sm text-gray-400 mt-1"><?php echo e(__('Conversations ranked by real buyer intent')); ?></p>
        </div>
        <a href="<?php echo e(route('projects.index')); ?>" class="glass-btn-primary">
            <?php echo e(__('+ New project')); ?>

        </a>
    </div>

    
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1"><?php echo e(__('Total detected')); ?></div>
            <div class="text-xl font-bold text-white"><?php echo e($stats['total_posts']); ?></div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1"><?php echo e(__('Hot (score 80+)')); ?></div>
            <div class="text-xl font-bold text-red-400"><?php echo e($stats['hot_count']); ?></div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1"><?php echo e(__('Avg score')); ?></div>
            <div class="text-xl font-bold text-indigo-400"><?php echo e($stats['avg_score']); ?></div>
        </div>
        <div class="glass !p-4">
            <div class="text-xs text-gray-500 mb-1"><?php echo e(__('Top subreddit')); ?></div>
            <div class="text-xl font-bold text-purple-400"><?php echo e($stats['top_subreddit'] ? 'r/'.$stats['top_subreddit'] : '—'); ?></div>
        </div>
    </div>

    
    <?php if(count($projects) === 0): ?>
        <div class="glass !p-16 text-center">
            <div class="text-4xl mb-3">🎯</div>
            <h3 class="text-lg font-medium text-white mb-2"><?php echo e(__('Create your first project')); ?></h3>
            <p class="text-sm text-gray-400 mb-6"><?php echo e(__('Add keywords and ReplyRadar will automatically detect opportunities on Reddit.')); ?></p>
            <a href="<?php echo e(route('projects.index')); ?>" class="glass-btn-primary inline-block">
                <?php echo e(__('Create project')); ?>

            </a>
        </div>
    <?php else: ?>
        
        <div class="flex items-center gap-3 mb-4 flex-wrap" x-data="{ filter: 'all', search: '' }">
            <input type="text" placeholder="<?php echo e(__('Search opportunities...')); ?>" x-model="search"
                class="glass-input flex-1 min-w-48 text-sm">
            <button @click="filter = 'all'" :class="filter === 'all' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm"><?php echo e(__('All')); ?></button>
            <button @click="filter = 'hot'" :class="filter === 'hot' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">🔥 <?php echo e(__('Hot')); ?></button>
            <button @click="filter = 'warm'" :class="filter === 'warm' ? 'glass-btn-primary' : 'glass-btn-secondary'" class="text-sm">⚡ <?php echo e(__('Warm')); ?></button>
            <span class="text-sm text-gray-500" x-text="'<?php echo e($opportunities->count()); ?> <?php echo e(__('results')); ?>'"></span>
        </div>

        <?php if($blurredIds->isNotEmpty()): ?>
            <div class="mb-4 p-4 rounded-xl bg-gradient-to-r from-amber-500/10 via-yellow-500/10 to-orange-500/10 border border-amber-500/20 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex items-center gap-3">
                    <span class="text-xl">🚀</span>
                    <div>
                        <p class="text-sm text-amber-100 font-medium"><?php echo e(__('Unlock big opportunities for only')); ?> <span class="text-amber-300 font-bold">€14,99</span></p>
                        <p class="text-xs text-amber-400/70"><?php echo e(__('Offer for the first 100 users this week')); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-center">
                        <div class="text-lg font-bold text-amber-300">22</div>
                        <div class="text-[10px] text-amber-400/60 uppercase tracking-wider"><?php echo e(__('spots left')); ?></div>
                    </div>
                    <form method="POST" action="<?php echo e(route('billing.promo14')); ?>" class="inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit"
                            class="whitespace-nowrap text-sm glass-btn-primary !px-4 !py-2">
                            <?php echo e(__('Upgrade now')); ?> →
                        </button>
                    </form>
                </div>
            </div>
        <?php endif; ?>

        
        <div class="grid gap-3">
            <?php $__empty_1 = true; $__currentLoopData = $opportunities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <?php
                    $isHot = $post->final_score >= 80;
                    $isWarm = $post->final_score >= 60 && $post->final_score < 80;
                ?>
                <div class="dashboard-post"
                    x-data="{ show: true }"
                    x-show="show"
                    x-init="
                        $watch('filter', val => {
                            show = (val === 'all' || (val === 'hot' && <?php echo e($isHot ? 'true' : 'false'); ?>) || (val === 'warm' && <?php echo e($isWarm ? 'true' : 'false'); ?>));
                        });
                        $watch('search', val => {
                            const t = '<?php echo e(addslashes($post->localized_title)); ?>'.toLowerCase();
                            const s = '<?php echo e(addslashes($post->subreddit)); ?>'.toLowerCase();
                            show = (t.includes(val.toLowerCase()) || s.includes(val.toLowerCase())) &&
                                (filter === 'all' || (filter === 'hot' && <?php echo e($isHot ? 'true' : 'false'); ?>) || (filter === 'warm' && <?php echo e($isWarm ? 'true' : 'false'); ?>));
                        });
                    "
                >
                    <?php if (isset($component)) { $__componentOriginal24b019fd63fae09ba9fb2a5e4cd3e3be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24b019fd63fae09ba9fb2a5e4cd3e3be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.opportunity-card','data' => ['post' => $post,'blurredIds' => $blurredIds]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('opportunity-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['post' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post),'blurred-ids' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($blurredIds)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal24b019fd63fae09ba9fb2a5e4cd3e3be)): ?>
<?php $attributes = $__attributesOriginal24b019fd63fae09ba9fb2a5e4cd3e3be; ?>
<?php unset($__attributesOriginal24b019fd63fae09ba9fb2a5e4cd3e3be); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal24b019fd63fae09ba9fb2a5e4cd3e3be)): ?>
<?php $component = $__componentOriginal24b019fd63fae09ba9fb2a5e4cd3e3be; ?>
<?php unset($__componentOriginal24b019fd63fae09ba9fb2a5e4cd3e3be); ?>
<?php endif; ?>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="glass !p-12 text-center">
                    <p class="text-gray-500 text-sm"><?php echo e(__('No opportunities match these filters')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views/dashboard/index.blade.php ENDPATH**/ ?>