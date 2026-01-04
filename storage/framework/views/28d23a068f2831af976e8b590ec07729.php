<?php $__env->startSection('body'); ?>

    <body class="login">
        <?php echo $__env->yieldContent('content'); ?>

        <!-- BEGIN: JS Assets-->
        <?php echo app('Illuminate\Foundation\Vite')('resources/js/app.js'); ?>
        <!-- END: JS Assets-->

        <?php echo $__env->yieldContent('script'); ?>
    </body>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('../layout/base', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH G:\xampp\htdocs\astroway\Website\resources\views////layout/login.blade.php ENDPATH**/ ?>