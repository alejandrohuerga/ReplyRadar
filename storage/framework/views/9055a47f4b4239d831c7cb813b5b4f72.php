<?php $__env->startSection('title', __('Profile')); ?>

<?php $__env->startSection('content'); ?>
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-white"><?php echo e(__('Profile')); ?></h1>
        <p class="text-gray-400 mt-1 text-sm"><?php echo e(__('Manage your personal info and security')); ?></p>
    </div>

    <div class="max-w-2xl space-y-6">
        <div class="glass !p-6">
            <h2 class="text-lg font-semibold text-white mb-1"><?php echo e(__('Profile information')); ?></h2>
            <p class="text-sm text-gray-400 mb-6"><?php echo e(__('Update your name and email')); ?></p>

            <form method="POST" action="<?php echo e(route('profile.update')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PATCH'); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Name')); ?></label>
                    <input type="text" name="name" value="<?php echo e(old('name', auth()->user()->name)); ?>"
                        class="glass-input">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Email address')); ?></label>
                    <input type="email" name="email" value="<?php echo e(old('email', auth()->user()->email)); ?>"
                        class="glass-input">
                    <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <?php if(auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !auth()->user()->hasVerifiedEmail()): ?>
                    <div class="p-3 rounded-xl bg-amber-500/10 border border-amber-500/20 text-sm text-amber-400">
                        <?php echo e(__('Your email is unverified.')); ?>

                        <form method="POST" action="<?php echo e(route('verification.send')); ?>" class="inline">
                            <?php echo csrf_field(); ?>
                            <button type="submit" class="underline ml-1"><?php echo e(__('Resend verification')); ?></button>
                        </form>
                    </div>
                <?php endif; ?>

                <?php if(session('status') === 'verification-link-sent'): ?>
                    <div class="p-3 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-400">
                        <?php echo e(__('A new verification link has been sent to your email address.')); ?>

                    </div>
                <?php endif; ?>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="glass-btn-primary"><?php echo e(__('Save')); ?></button>
                    <?php if(session('success')): ?>
                        <span class="text-sm text-green-400"><?php echo e(__('✓ Saved')); ?></span>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="glass !p-6">
            <h2 class="text-lg font-semibold text-white mb-1"><?php echo e(__('Update password')); ?></h2>
            <p class="text-sm text-gray-400 mb-6"><?php echo e(__('Ensure your account is using a long, random password to stay secure.')); ?></p>

            <form method="POST" action="<?php echo e(route('password.update')); ?>" class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Current password')); ?></label>
                    <input type="password" name="current_password" class="glass-input" autocomplete="current-password">
                    <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('New password')); ?></label>
                    <input type="password" name="password" class="glass-input" autocomplete="new-password">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Confirm password')); ?></label>
                    <input type="password" name="password_confirmation" class="glass-input" autocomplete="new-password">
                    <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="glass-btn-primary"><?php echo e(__('Save')); ?></button>
                    <?php if(session('status') === 'password-updated'): ?>
                        <span class="text-sm text-green-400"><?php echo e(__('✓ Saved')); ?></span>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="glass !p-6 border-red-500/10">
            <h2 class="text-lg font-semibold text-red-400 mb-1"><?php echo e(__('Delete account')); ?></h2>
            <p class="text-sm text-gray-400 mb-6">
                <?php echo e(__('Once your account is deleted, all data will be permanently deleted.')); ?>

            </p>

            <form method="POST" action="<?php echo e(route('profile.destroy')); ?>"
                onsubmit="return confirm('<?php echo e(__('Are you sure you want to delete your account? This action cannot be undone.')); ?>')"
                class="space-y-4">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>

                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Confirm with your password')); ?></label>
                    <input type="password" name="password" class="glass-input" placeholder="<?php echo e(__('Your current password')); ?>" autocomplete="current-password">
                    <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <button type="submit" class="glass-btn-danger !px-5 !py-2.5 text-sm">
                    <?php echo e(__('Delete account')); ?>

                </button>
            </form>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views\profile\edit.blade.php ENDPATH**/ ?>