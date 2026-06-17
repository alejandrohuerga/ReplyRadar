<?php $__env->startSection('title', __('Plans')); ?>

<?php $__env->startSection('content'); ?>
    <div class="text-center mb-10">
        <h1 class="text-3xl font-bold text-white"><?php echo e(__('Choose your plan')); ?></h1>
        <p class="text-gray-400 mt-2"><?php echo e(__('No contracts. Cancel anytime.')); ?></p>
    </div>

    <?php if($subscribed): ?>
        <div class="text-center mb-8">
            <a href="<?php echo e(route('billing.portal')); ?>"
                class="glass-btn-secondary inline-block !px-6 !py-2 text-sm">
                <?php echo e(__('Manage subscription in Stripe')); ?>

            </a>
        </div>
    <?php endif; ?>

    <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
        <?php
            $plans = [
                (object)[
                    'id' => 'free', 'name' => 'Free', 'price' => '$0', 'desc' => __('For exploring ReplyRadar'),
                    'features' => [__('1 project'), __('5 keywords'), __('50 opportunities/mo'), __('7-day history')],
                    'featured' => false,
                ],
                (object)[
                    'id' => 'pro', 'name' => 'Pro', 'price' => '24€', 'desc' => __('For creators and solopreneurs'),
                    'features' => [__('5 projects'), __('50 keywords'), __('Unlimited opportunities'), __('90-day history'), __('Export to CSV'), __('Opportunities with +80 match')],
                    'featured' => true,
                ],
                (object)[
                    'id' => 'business', 'name' => 'Business', 'price' => '99€', 'desc' => __('For agencies and teams'),
                    'features' => [__('Everything in Pro'), __('Unlimited projects'), __('Unlimited keywords'), __('Multi-source'), __('API access'), __('Priority support')],
                    'featured' => false,
                ],
            ];
        ?>

        <?php $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="glass !p-8 <?php echo e($plan->featured ? 'gradient-border' : ''); ?>" <?php echo e($plan->featured ? "style=border-color:transparent" : ""); ?>>
                <?php if($plan->featured): ?>
                    <div class="text-xs font-bold text-indigo-400 bg-indigo-500/10 rounded-full px-3 py-1 inline-block mb-3"><?php echo e(__('Most popular')); ?></div>
                <?php endif; ?>
                <div class="text-lg font-bold text-white"><?php echo e($plan->name); ?></div>
                <div class="text-4xl font-extrabold text-white mt-2">
                    <?php echo e($plan->price); ?>

                    <span class="text-base font-normal text-gray-400"><?php echo e(__('/mo')); ?></span>
                </div>
                <p class="text-sm text-gray-400 mt-1 mb-5"><?php echo e($plan->desc); ?></p>

                <ul class="space-y-2 mb-6">
                    <?php $__currentLoopData = $plan->features; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="flex items-center gap-2 text-sm text-gray-300">
                            <span class="text-green-400 font-bold">✓</span> <?php echo e($f); ?>

                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                <?php if($plan->id === 'free' || $plan->id === $currentPlan): ?>
                    <form method="POST" action="<?php echo e(route('billing.checkout')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan" value="<?php echo e($plan->id); ?>">
                        <button type="submit" disabled
                            class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                <?php echo e($plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary'); ?>

                                opacity-60 cursor-default">
                            <?php if($plan->id === $currentPlan): ?>
                                <?php echo e(__('Current plan')); ?>

                            <?php else: ?>
                                <?php echo e(__('Free plan')); ?>

                            <?php endif; ?>
                        </button>
                    </form>
                <?php elseif(!$stripeReady): ?>
                    <button type="button" disabled
                        class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                            <?php echo e($plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary'); ?>

                            opacity-60 cursor-default">
                        <?php echo e(__('Coming soon')); ?>

                    </button>
                <?php else: ?>
                    <form method="POST" action="<?php echo e(route('billing.checkout')); ?>">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="plan" value="<?php echo e($plan->id); ?>">
                        <button type="submit"
                            class="w-full py-2.5 rounded-xl text-sm font-medium transition-all duration-200
                                <?php echo e($plan->featured ? 'glass-btn-primary' : 'glass-btn-secondary'); ?>">
                            <?php echo e(__('Switch to')); ?> <?php echo e($plan->name); ?>

                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\ReplyRadar\resources\views/billing/plans.blade.php ENDPATH**/ ?>