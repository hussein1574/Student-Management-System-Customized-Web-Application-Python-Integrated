
<?php echo $__env->renderWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
	
	<?php echo $__env->make($widget['view'], ['widget' => $widget], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php echo $__env->renderWhen(!empty($widget['wrapper']), 'backpack::widgets.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?><?php /**PATH D:\Programming\Graduation-project\2022-2023\Laravel\intelligent-college-timetable-scheduler\vendor\backpack\crud\src\resources\views\base/widgets/view.blade.php ENDPATH**/ ?>