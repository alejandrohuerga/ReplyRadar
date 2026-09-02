<?php $__env->startSection('title', __('Reset password')); ?>

<?php $__env->startSection('content'); ?>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
                <span class="text-2xl">🔄</span>
            </div>
            <h1 class="text-2xl font-bold text-white"><?php echo e(__('Reset password')); ?></h1>
            <p class="text-sm text-gray-400 mt-1"><?php echo e(__('Choose a secure new password')); ?></p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="<?php echo e(route('password.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="token" value="<?php echo e($token); ?>">

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Email')); ?></label>
                    <input type="email" name="email" value="<?php echo e(old('email', $email)); ?>" required autofocus autocomplete="username"
                        class="glass-input" placeholder="<?php echo e(__('you@email.com')); ?>">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1.5"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('New password')); ?></label>
                    <input type="password" name="password" required autocomplete="new-password"
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

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Confirm password')); ?></label>
                    <input type="password" name="password_confirmation" required autocomplete="new-password"
                        class="glass-input" placeholder="••••••••">
                </div>

                <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                    <?php echo e(__('Reset Password')); ?>

                </button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views\auth\reset-password.blade.php ENDPATH**/ ?>