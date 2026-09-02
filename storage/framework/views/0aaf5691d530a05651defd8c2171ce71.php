<?php $__env->startSection('title', __('Projects')); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white"><?php echo e(__('Projects')); ?></h1>
        <p class="text-gray-400 mt-1 text-sm"><?php echo e(__('Each project groups related keywords to detect opportunities')); ?></p>
    </div>

    <div class="grid lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1">
            <div class="glass !p-6">
                <h2 class="text-base font-semibold text-white mb-4"><?php echo e(__('New project')); ?></h2>
                <?php if(!$canCreate): ?>
                    <div class="mb-4 p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-sm text-amber-400">
                        <?php echo e(__("You've reached your plan limit.")); ?>

                        <a href="<?php echo e(route('billing.plans')); ?>" class="underline font-medium ml-1"><?php echo e(__('Upgrade')); ?></a>
                    </div>
                <?php endif; ?>
                <form method="POST" action="<?php echo e(route('projects.store')); ?>" class="space-y-3">
                    <?php echo csrf_field(); ?>
                    <input type="text" name="name" placeholder="<?php echo e(__('Project name')); ?>" value="<?php echo e(old('name')); ?>"
                        <?php echo e(!$canCreate ? 'disabled' : ''); ?>

                        class="glass-input <?php echo e(!$canCreate ? 'opacity-50' : ''); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <textarea name="description" placeholder="<?php echo e(__('Description (optional)')); ?>" rows="3"
                        <?php echo e(!$canCreate ? 'disabled' : ''); ?>

                        class="glass-input resize-none <?php echo e(!$canCreate ? 'opacity-50' : ''); ?>"><?php echo e(old('description')); ?></textarea>
                    <button type="submit" <?php echo e(!$canCreate ? 'disabled' : ''); ?>

                        class="glass-btn-primary w-full <?php echo e(!$canCreate ? 'opacity-50 cursor-not-allowed' : ''); ?>">
                        <?php echo e(__('Create project')); ?>

                    </button>
                </form>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-3">
            <?php $__empty_1 = true; $__currentLoopData = $projects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $project): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="glass-card !p-5">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0">
                            <a href="<?php echo e(route('projects.show', $project)); ?>" class="font-semibold text-white hover:text-indigo-400 transition-colors">
                                <?php echo e($project->name); ?>

                            </a>
                            <?php if($project->description): ?>
                                <p class="text-sm text-gray-400 mt-1"><?php echo e($project->description); ?></p>
                            <?php endif; ?>
                            <div class="flex gap-4 mt-3">
                                <span class="text-xs text-gray-500">🔑 <?php echo e($project->keywords_count); ?> <?php echo e(__('keywords')); ?></span>
                                <span class="text-xs text-gray-500">📊 <?php echo e($project->posts_count); ?> <?php echo e(__('posts')); ?></span>
                            </div>
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <a href="<?php echo e(route('projects.show', $project)); ?>"
                                class="glass-btn-primary !px-3 !py-1.5 text-xs">
                                <?php echo e(__('View →')); ?>

                            </a>
                            <form method="POST" action="<?php echo e(route('projects.destroy', $project)); ?>" class="inline"
                                onsubmit="return confirm('<?php echo e(__('Delete project «{name}»?')); ?>'.replace('{name}', '<?php echo e($project->name); ?>'))">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="glass-btn-danger !px-3 !py-1.5 text-xs">✕</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="glass !p-12 text-center">
                    <div class="text-4xl mb-3">📁</div>
                    <p class="text-gray-500 text-sm"><?php echo e(__('Create your first project to get started')); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views\projects\index.blade.php ENDPATH**/ ?>