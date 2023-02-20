<?php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.preview') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
?>

<?php $__env->startSection('header'); ?>
	<section class="container-fluid d-print-none">
    	<a href="javascript: window.print();" class="btn float-right"><i class="la la-print"></i></a>
		<h2>
	        <span class="text-capitalize"><?php echo $crud->getHeading() ?? $crud->entity_name_plural; ?></span>
	        <small><?php echo $crud->getSubheading() ?? mb_ucfirst(trans('backpack::crud.preview')).' '.$crud->entity_name; ?>.</small>
	        <?php if($crud->hasAccess('list')): ?>
	          <small class=""><a href="<?php echo e(url($crud->route)); ?>" class="font-sm"><i class="la la-angle-double-left"></i> <?php echo e(trans('backpack::crud.back_to_all')); ?> <span><?php echo e($crud->entity_name_plural); ?></span></a></small>
	        <?php endif; ?>
	    </h2>
    </section>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<div class="row">
	<div class="<?php echo e($crud->getShowContentClass()); ?>">

	
	  <div class="">
	  	<?php if($crud->model->translationEnabled()): ?>
			<div class="row">
				<div class="col-md-12 mb-2">
					
					<div class="btn-group float-right">
					<button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
						<?php echo e(trans('backpack::crud.language')); ?>: <?php echo e($crud->model->getAvailableLocales()[request()->input('_locale')?request()->input('_locale'):App::getLocale()]); ?> &nbsp; <span class="caret"></span>
					</button>
					<ul class="dropdown-menu">
						<?php $__currentLoopData = $crud->model->getAvailableLocales(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $locale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<a class="dropdown-item" href="<?php echo e(url($crud->route.'/'.$entry->getKey().'/show')); ?>?_locale=<?php echo e($key); ?>"><?php echo e($locale); ?></a>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</ul>
					</div>
				</div>
			</div>
	    <?php endif; ?>
	    <div class="card no-padding no-border">
			<table class="table table-striped mb-0">
		        <tbody>
		        <?php $__currentLoopData = $crud->columns(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $column): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		            <tr>
		                <td>
		                    <strong><?php echo $column['label']; ?>:</strong>
		                </td>
                        <td>
                        	<?php
                        		// create a list of paths to column blade views
                        		// including the configured view_namespaces
                        		$columnPaths = array_map(function($item) use ($column) {
                        			return $item.'.'.$column['type'];
                        		}, \Backpack\CRUD\ViewNamespaces::getFor('columns'));

                        		// but always fall back to the stock 'text' column
                        		// if a view doesn't exist
                        		if (!in_array('crud::columns.text', $columnPaths)) {
                        			$columnPaths[] = 'crud::columns.text';
                        		}
                        	?>
													<?php echo $__env->first($columnPaths, \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
                        </td>
		            </tr>
		        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				<?php if($crud->buttons()->where('stack', 'line')->count()): ?>
					<tr>
						<td><strong><?php echo e(trans('backpack::crud.actions')); ?></strong></td>
						<td>
							<?php echo $__env->make('crud::inc.button_stack', ['stack' => 'line'], \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
						</td>
					</tr>
				<?php endif; ?>
		        </tbody>
			</table>
	    </div>
	  </div>

	</div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make(backpack_view('blank'), \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Programming\Graduation-project\2022-2023\Laravel\intelligent-college-timetable-scheduler\vendor\backpack\crud\src\resources\views\crud/show.blade.php ENDPATH**/ ?>