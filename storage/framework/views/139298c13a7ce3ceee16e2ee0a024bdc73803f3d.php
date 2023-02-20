
<?php
    $column['value'] = $column['value'] ?? data_get($entry, $column['name']);
    $column['escaped'] = $column['escaped'] ?? true;
    $column['prefix'] = $column['prefix'] ?? '';
    $column['suffix'] = $column['suffix'] ?? '';
    $column['decimals'] = $column['decimals'] ?? 0;
    $column['dec_point'] = $column['dec_point'] ?? '.';
    $column['thousands_sep'] = $column['thousands_sep'] ?? ',';
    $column['text'] = $column['default'] ?? '-';

    if($column['value'] instanceof \Closure) {
        $column['value'] = $column['value']($entry);
    }
    
    if (!is_null($column['value'])) {
        $column['value'] = number_format($column['value'], $column['decimals'], $column['dec_point'], $column['thousands_sep']);
        $column['text'] = $column['prefix'].$column['value'].$column['suffix'];
    }
?>

<span>
    <?php echo $__env->renderWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
        <?php if($column['escaped']): ?>
            <?php echo e($column['text']); ?>

        <?php else: ?>
            <?php echo $column['text']; ?>

        <?php endif; ?>
    <?php echo $__env->renderWhen(!empty($column['wrapper']), 'crud::columns.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path'])); ?>
</span>
<?php /**PATH D:\Learning\Graduation-Project\Website\intelligent-college-timetable-scheduler\intelligent-college-timetable-scheduler\vendor\backpack\crud\src\resources\views\crud/columns/number.blade.php ENDPATH**/ ?>