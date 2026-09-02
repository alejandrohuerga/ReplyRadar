<?php $__env->startSection('title', __('Confirm password')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-md mx-auto">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">🔒</span>
        </div>
        <h1 class="text-2xl font-bold text-white"><?php echo e(__('Confirm your password')); ?></h1>
        <p class="text-sm text-gray-400 mt-1"><?php echo e(__('This is a secure area. Please confirm your password before continuing.')); ?></p>
    </div>

    <div class="glass !p-8">
        <form method="POST" action="<?php echo e(route('password.confirm')); ?>">
            <?php echo csrf_field(); ?>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Password')); ?></label>
                <input type="password" name="password" required autocomplete="current-password"
                    class="glass-input" placeholder="••••••••">
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1.5"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>

            <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                <?php echo e(__('Confirm')); ?>

            </button>
        </form>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views\auth\confirm-password.blade.php ENDPATH**/ ?>