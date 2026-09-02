<?php $__env->startSection('title', __('Verify email')); ?>

<?php $__env->startSection('content'); ?>
<div class="max-w-2xl mx-auto">
    <div class="text-center mb-8">
        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-indigo-500/20 to-purple-600/20 border border-white/[0.06] flex items-center justify-center mx-auto mb-4">
            <span class="text-2xl">📧</span>
        </div>
        <h1 class="text-2xl font-bold text-white"><?php echo e(__('Verify your email')); ?></h1>
        <p class="text-sm text-gray-400 mt-1"><?php echo e(__("We've sent you a verification link")); ?></p>
    </div>

    <div class="glass !p-8 text-center">
        <?php if(session('status') == 'verification-link-sent'): ?>
            <div class="mb-6 px-4 py-3 rounded-lg bg-green-500/10 border border-green-500/20 text-sm text-green-400">
                <?php echo e(__('A new verification link has been sent to your email.')); ?>

            </div>
        <?php endif; ?>

        <p class="text-gray-300 mb-6">
            <?php echo e(__('Before continuing, check your email for the verification link. If you didn\'t receive it, request another.')); ?>

        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
            <form method="POST" action="<?php echo e(route('verification.send')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="glass-btn-primary !px-6 !py-2.5">
                    <?php echo e(__('Resend verification')); ?>

                </button>
            </form>

            <form method="POST" action="<?php echo e(route('logout')); ?>">
                <?php echo csrf_field(); ?>
                <button type="submit" class="glass-btn-secondary !px-6 !py-2.5">
                    <?php echo e(__('Log out')); ?>

                </button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views\auth\verify-email.blade.php ENDPATH**/ ?>