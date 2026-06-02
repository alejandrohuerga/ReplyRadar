<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title>ReplyRadar</title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
</head>
<body>

    
    <nav class="fixed top-0 left-0 right-0 z-50 border-b border-white/[0.04]" style="background: rgba(8,8,15,0.8); backdrop-filter: blur(20px); -webkit-backdrop-filter: blur(20px);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                
                <a href="<?php echo e(route('dashboard')); ?>" class="flex items-center gap-2">
                    <img src="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>" alt="ReplyRadar" class="h-8 w-auto">
                    <span class="hidden sm:inline font-bold text-lg text-white">ReplyRadar</span>
                </a>

                
                <div class="flex items-center gap-4">
                    
                    <div class="flex items-center gap-1 text-xs">
                        <a href="<?php echo e(route('language.switch', 'en')); ?>" class="px-2 py-1 rounded-lg transition-colors <?php echo e(app()->getLocale() === 'en' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300'); ?>">EN</a>
                        <span class="text-white/[0.1]">|</span>
                        <a href="<?php echo e(route('language.switch', 'es')); ?>" class="px-2 py-1 rounded-lg transition-colors <?php echo e(app()->getLocale() === 'es' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300'); ?>">ES</a>
                    </div>
                    <a href="<?php echo e(route('projects.index')); ?>" class="text-sm text-gray-400 hover:text-white transition-colors <?php echo e(request()->routeIs('projects.*') ? 'text-white' : ''); ?>"><?php echo e(__('Projects')); ?></a>
                    <a href="<?php echo e(route('billing.plans')); ?>" class="text-sm text-gray-400 hover:text-white transition-colors <?php echo e(request()->routeIs('billing.*') ? 'text-white' : ''); ?>"><?php echo e(__('Plans')); ?></a>

                    
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 glass-btn-secondary text-sm !px-3 !py-1.5">
                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-xs font-bold text-white">
                                <?php echo e(substr(auth()->user()->name, 0, 1)); ?>

                            </div>
                            <span class="hidden sm:inline"><?php echo e(auth()->user()->name); ?></span>
                            <svg class="w-3 h-3 text-gray-400" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95" class="absolute right-0 mt-2 w-56 origin-top-right" style="display: none">
                            <div class="glass !p-1.5 !rounded-xl shadow-xl shadow-black/50">
                                <a href="<?php echo e(route('profile.edit')); ?>" class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/[0.06] rounded-lg transition-colors"><?php echo e(__('Profile')); ?></a>
                                <a href="<?php echo e(route('billing.plans')); ?>" class="block px-3 py-2 text-sm text-gray-300 hover:text-white hover:bg-white/[0.06] rounded-lg transition-colors"><?php echo e(__('Subscription')); ?></a>
                                <hr class="border-white/[0.06] my-1">
                                <form method="POST" action="<?php echo e(route('logout')); ?>">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="w-full text-left px-3 py-2 text-sm text-red-400 hover:text-red-300 hover:bg-red-500/10 rounded-lg transition-colors"><?php echo e(__('Logout')); ?></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    
    <?php if(session('success') || session('error') || $errors->any()): ?>
        <div class="fixed top-20 left-1/2 -translate-x-1/2 z-40 w-full max-w-md px-4">
            <?php if(session('success')): ?>
                <div class="glass !rounded-xl px-4 py-3 border border-green-500/20 bg-green-500/10 text-sm text-green-400 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>
            <?php if(session('error')): ?>
                <div class="glass !rounded-xl px-4 py-3 border border-red-500/20 bg-red-500/10 text-sm text-red-400 flex items-center gap-2">
                    <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            <?php if($errors->any()): ?>
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="glass !rounded-xl px-4 py-3 border border-red-500/20 bg-red-500/10 text-sm text-red-400 flex items-center gap-2">
                        <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?php echo e($error); ?>

                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    
    <main class="pt-24 pb-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</body>
</html>
<?php /**PATH C:\laragon\www\ReplyRadar\resources\views/layouts/app.blade.php ENDPATH**/ ?>