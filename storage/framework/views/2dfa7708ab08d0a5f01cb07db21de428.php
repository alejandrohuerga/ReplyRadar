<?php $__env->startSection('title', __('Log in')); ?>

<?php $__env->startSection('content'); ?>
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <div class="flex items-center justify-center mx-auto mb-4">
                <img src="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>" alt="ReplyRadar" class="h-12 w-auto">
            </div>
            <h1 class="text-2xl font-bold text-white"><?php echo e(__('Welcome back')); ?></h1>
            <p class="text-sm text-gray-400 mt-1"><?php echo e(__('Access your business opportunities')); ?></p>
        </div>

        <div class="glass !p-8">
            <form method="POST" action="<?php echo e(route('login')); ?>">
                <?php echo csrf_field(); ?>

                <div class="mb-5">
                    <label class="block text-sm font-medium text-gray-300 mb-1.5"><?php echo e(__('Email')); ?></label>
                    <input type="email" name="email" value="<?php echo e(old('email')); ?>" required autofocus autocomplete="username"
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
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="text-sm font-medium text-gray-300"><?php echo e(__('Password')); ?></label>
                        <?php if(Route::has('password.request')): ?>
                            <a href="<?php echo e(route('password.request')); ?>" class="text-xs text-indigo-400 hover:text-indigo-300 transition-colors"><?php echo e(__('Forgot your password?')); ?></a>
                        <?php endif; ?>
                    </div>
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

                <div class="flex items-center gap-2.5 mb-6">
                    <input type="checkbox" name="remember" id="remember"
                        class="w-4 h-4 rounded border-white/[0.1] bg-white/[0.05] text-indigo-500 focus:ring-indigo-500/40 focus:ring-offset-0"
                        style="accent-color: #6366f1">
                    <label for="remember" class="text-sm text-gray-400 cursor-pointer select-none"><?php echo e(__('Remember me')); ?></label>
                </div>

                <button type="submit" class="glass-btn-primary w-full !py-3 text-base font-semibold">
                    <?php echo e(__('Enter ReplyRadar')); ?>

                </button>
            </form>
        </div>

        <p class="text-center text-sm text-gray-500 mt-6">
            <?php echo e(__("Don't have an account?")); ?>

            <a href="<?php echo e(route('register')); ?>" class="text-indigo-400 font-medium hover:text-indigo-300 transition-colors"><?php echo e(__('Start free')); ?></a>
        </p>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.guest', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views/auth/login.blade.php ENDPATH**/ ?>