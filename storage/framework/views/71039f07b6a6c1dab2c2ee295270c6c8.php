<?php $__env->startSection('title', $project->name); ?>

<?php $__env->startSection('content'); ?>
    <div class="flex items-center gap-2 text-sm text-gray-500 mb-6">
        <a href="<?php echo e(route('projects.index')); ?>" class="hover:text-indigo-400 transition-colors"><?php echo e(__('Projects')); ?></a>
        <span>/</span>
        <span class="text-gray-200 font-medium"><?php echo e($project->name); ?></span>
    </div>

    <div class="grid lg:grid-cols-4 gap-6">
        <div class="lg:col-span-1 space-y-4">
            <div class="glass !p-4">
                <h3 class="text-sm font-semibold text-white mb-3"><?php echo e(__('Keywords')); ?></h3>

                <form method="POST" action="<?php echo e(route('keywords.store', $project)); ?>" class="mb-4">
                    <?php echo csrf_field(); ?>
                    <div class="flex gap-2">
                        <input type="text" name="term" placeholder="<?php echo e(__('New keyword...')); ?>"
                            value="<?php echo e(old('term')); ?>"
                            <?php echo e(!$canAddKeyword ? 'disabled' : ''); ?>

                            class="glass-input flex-1 text-sm <?php echo e(!$canAddKeyword ? 'opacity-50' : ''); ?>">
                        <button type="submit" <?php echo e(!$canAddKeyword ? 'disabled' : ''); ?>

                            class="glass-btn-primary !px-3 !py-2 <?php echo e(!$canAddKeyword ? 'opacity-50 cursor-not-allowed' : ''); ?>">
                            +
                        </button>
                    </div>
                    <?php $__errorArgs = ['term'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php if(!$canAddKeyword): ?>
                        <p class="text-xs text-amber-400 mt-1">
                            <?php echo e(__('Limit reached.')); ?> <a href="<?php echo e(route('billing.plans')); ?>" class="underline"><?php echo e(__('Upgrade')); ?></a>
                        </p>
                    <?php endif; ?>
                </form>

                <div class="space-y-2">
                    <?php $__empty_1 = true; $__currentLoopData = $project->keywords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kw): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="flex items-center justify-between gap-2 p-2 rounded-lg bg-white/[0.03]">
                            <span class="text-sm <?php echo e($kw->is_active ? 'text-gray-200' : 'text-gray-500 line-through'); ?>">
                                <?php echo e($kw->term); ?>

                            </span>
                            <div class="flex gap-1">
                                <form method="POST" action="<?php echo e(route('keywords.toggle', $kw)); ?>" class="inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('PATCH'); ?>
                                    <button type="submit" class="text-xs text-gray-500 hover:text-indigo-400 transition-colors" title="<?php echo e($kw->is_active ? __('Pause') : __('Activate')); ?>">
                                        <?php echo e($kw->is_active ? '⏸' : '▶'); ?>

                                    </button>
                                </form>
                                <form method="POST" action="<?php echo e(route('keywords.destroy', $kw)); ?>" class="inline"
                                    onsubmit="return confirm('<?php echo e(__('Delete keyword «{term}»?')); ?>'.replace('{term}', '<?php echo e($kw->term); ?>'))">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="text-xs text-gray-500 hover:text-red-400 transition-colors">✕</button>
                                </form>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p class="text-xs text-gray-500 text-center py-4"><?php echo e(__('No keywords yet')); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="lg:col-span-3">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-white">
                    <?php echo e($posts->count()); ?> <?php echo e(__('opportunities detected')); ?>

                </h2>
                <div class="flex items-center gap-3">
                    <form method="GET" action="<?php echo e(route('projects.show', $project)); ?>" id="sort-form">
                        <select name="sort" onchange="this.form.submit()"
                            class="glass-input text-sm !py-1.5 !px-3">
                            <option value="final_score" <?php echo e(request('sort') === 'final_score' ? 'selected' : ''); ?>><?php echo e(__('Sort by score')); ?></option>
                            <option value="posted_at" <?php echo e(request('sort') === 'posted_at' ? 'selected' : ''); ?>><?php echo e(__('Sort by date')); ?></option>
                        </select>
                    </form>
                    <?php if($canExport): ?>
                        <a href="<?php echo e(route('export.posts', ['project_id' => $project->id])); ?>"
                            class="glass-btn-primary !px-4 !py-1.5 text-sm">
                            <?php echo e(__('Export CSV')); ?>

                        </a>
                    <?php else: ?>
                        <span class="glass-btn-secondary !px-4 !py-1.5 text-sm opacity-50 cursor-not-allowed"
                            title="<?php echo e(__('Available on Pro plan')); ?>">
                            <?php echo e(__('Export CSV')); ?>

                        </span>
                    <?php endif; ?>
                </div>
            </div>

            <?php if($posts->isEmpty()): ?>
                <div class="glass !p-16 text-center">
                    <div class="text-4xl mb-3">🔍</div>
                    <p class="text-gray-500 text-sm"><?php echo e(__('Add a keyword to start detecting opportunities')); ?></p>
                </div>
            <?php else: ?>
                <div class="grid gap-3">
                    <?php $__currentLoopData = $posts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $post): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <?php if (isset($component)) { $__componentOriginal24b019fd63fae09ba9fb2a5e4cd3e3be = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal24b019fd63fae09ba9fb2a5e4cd3e3be = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.opportunity-card','data' => ['post' => $post]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('opportunity-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['post' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($post)]); ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views/projects/show.blade.php ENDPATH**/ ?>