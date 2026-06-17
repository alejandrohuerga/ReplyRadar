<?php $__env->startSection('title', __('Create account')); ?>

<?php $__env->startSection('content'); ?>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mx-auto mb-4">
                <img src="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>" alt="ReplyRadar" class="h-12 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-white"><?php echo e(__('Get started free')); ?></h1>
            <p class="text-sm text-gray-400 mt-1"><?php echo e(__('Monitor Reddit and find opportunities')); ?></p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="<?php echo e(route('register')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Name')); ?></label>
                    <input type="text" name="name" value="<?php echo e(old('name')); ?>" required autofocus autocomplete="name"
                        class="glass-input" placeholder="<?php echo e(__('Your name')); ?>">
                    <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-xs text-red-400 mt-1.5"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Email')); ?></label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autocomplete="username"
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
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Password')); ?></label>
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
                    <?php echo e(__('Create account')); ?>

                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            <?php echo e(__('Already have an account?')); ?>

            <a href="<?php echo e(route('login')); ?>" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors"><?php echo e(__('Log in')); ?></a>
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views/auth/register.blade.php ENDPATH**/ ?>