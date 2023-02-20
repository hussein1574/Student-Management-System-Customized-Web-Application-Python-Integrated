<?php if($crud->buttons()->where('stack', $stack)->count()): ?>
	<?php $__currentLoopData = $crud->buttons()->where('stack', $stack); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $button): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
	  <?php echo $button->getHtml($entry ?? null); ?>

	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
<?php endif; ?>
<?php /**PATH D:\Learning\Graduation-Project\Website\intelligent-college-timetable-scheduler\intelligent-college-timetable-scheduler(test)\vendor\backpack\crud\src\resources\views\crud/inc/button_stack.blade.php ENDPATH**/ ?>