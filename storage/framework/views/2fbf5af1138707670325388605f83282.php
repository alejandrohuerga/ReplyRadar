<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <title><?php echo $__env->yieldContent('title', 'ReplyRadar'); ?></title>
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>">
    <link rel="shortcut icon" href="<?php echo e(asset('favicon.ico')); ?>">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css']); ?>
    <style>
        .auth-gradient {
            min-height: 100vh;
            background: linear-gradient(135deg, #08080f 0%, #0d0d1a 50%, #08080f 100%);
            display: flex;
            flex-direction: column;
        }
        .auth-glow {
            position: fixed;
            width: 500px;
            height: 500px;
            top: -200px;
            right: -200px;
            background: radial-gradient(circle, rgba(99,102,241,0.12) 0%, transparent 70%);
            pointer-events: none;
        }
        .auth-glow-2 {
            position: fixed;
            width: 400px;
            height: 400px;
            bottom: -150px;
            left: -150px;
            background: radial-gradient(circle, rgba(168,85,247,0.08) 0%, transparent 70%);
            pointer-events: none;
        }
    </style>
</head>
<body class="font-['Figtree']">
    <div class="auth-gradient">
        <div class="auth-glow"></div>
        <div class="auth-glow-2"></div>

        
        <nav class="relative z-10 border-b border-white/[0.04]" style="background: rgba(8,8,15,0.6); backdrop-filter: blur(20px);">
            <div class="max-w-6xl mx-auto px-4 sm:px-6">
                <div class="flex items-center justify-between h-16">
                    <a href="<?php echo e(url('/')); ?>" class="flex items-center gap-2">
                        <img src="<?php echo e(asset('images/logo/logoSoloSinFondo.png')); ?>" alt="ReplyRadar" class="h-8 w-auto">
                        <span class="hidden sm:inline font-bold text-lg text-white">ReplyRadar</span>
                    </a>
                    <div class="flex items-center gap-3">
                        
                        <div class="flex items-center gap-1 text-xs">
                            <a href="<?php echo e(route('language.switch', 'en')); ?>" class="px-2 py-1 rounded-lg transition-colors <?php echo e(app()->getLocale() === 'en' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300'); ?>">EN</a>
                            <span class="text-white/[0.1]">|</span>
                            <a href="<?php echo e(route('language.switch', 'es')); ?>" class="px-2 py-1 rounded-lg transition-colors <?php echo e(app()->getLocale() === 'es' ? 'bg-indigo-500/20 text-indigo-300' : 'text-gray-500 hover:text-gray-300'); ?>">ES</a>
                        </div>
                        <?php if(Route::has('register') && !request()->routeIs('register')): ?>
                            <a href="<?php echo e(route('register')); ?>" class="glass-btn-primary !px-4 !py-1.5 text-sm"><?php echo e(__('Register')); ?></a>
                        <?php elseif(!request()->routeIs('login')): ?>
                            <a href="<?php echo e(route('login')); ?>" class="glass-btn-secondary !px-4 !py-1.5 text-sm"><?php echo e(__('Log in')); ?></a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>

        
        <?php if(session('status')): ?>
            <div class="relative z-10 max-w-md mx-auto mt-6 px-4">
                <div class="glass !rounded-xl px-4 py-3 border border-green-500/20 bg-green-500/10 text-sm text-green-400 text-center">
                    <?php echo e(session('status')); ?>

                </div>
            </div>
        <?php endif; ?>

        
        <div class="relative z-10 flex-1 flex items-center justify-center px-4 py-12">
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\laragon\www\ReplyRadar\resources\views/layouts/guest.blade.php ENDPATH**/ ?>