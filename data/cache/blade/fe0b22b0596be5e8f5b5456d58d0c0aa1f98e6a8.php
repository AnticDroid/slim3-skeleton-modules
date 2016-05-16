<?php $__env->startSection('content'); ?>
    Hello modules!
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.portal', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>