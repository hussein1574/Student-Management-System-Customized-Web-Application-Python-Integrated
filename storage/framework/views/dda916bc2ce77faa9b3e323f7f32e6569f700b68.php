

<?php
  $field['value'] = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';
?>
<?php echo $__env->make('crud::fields.inc.wrapper_start', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
    <?php echo $__env->make('crud::fields.inc.translatable_icon', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <input type="hidden" name="<?php echo e($field['name']); ?>" value="<?php echo e($field['value']); ?>">
    	  <input type="checkbox"
          data-init-function="bpFieldInitCheckbox"

          <?php if((bool)$field['value']): ?>
                 checked="checked"
          <?php endif; ?>

          <?php if(isset($field['attributes'])): ?>
              <?php $__currentLoopData = $field['attributes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $attribute => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    			<?php echo e($attribute); ?>="<?php echo e($value); ?>"
        	  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
          >
    	<label class="font-weight-normal mb-0"><?php echo $field['label']; ?></label>

        
        <?php if(isset($field['hint'])): ?>
            <p class="help-block"><?php echo $field['hint']; ?></p>
        <?php endif; ?>
<?php echo $__env->make('crud::fields.inc.wrapper_end', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>





    
    <?php $__env->startPush('crud_fields_scripts'); ?>
        <?php if(! Assets::isLoaded('bpFieldInitCheckbox')) { Assets::markAsLoaded('bpFieldInitCheckbox');  ?>
        <script>
            function bpFieldInitCheckbox(element) {
                var hidden_element = element.siblings('input[type=hidden]');
                var id = 'checkbox_'+Math.floor(Math.random() * 1000000);

                // make sure the value is a boolean (so it will pass validation)
                if (hidden_element.val() === '') hidden_element.val(0).trigger('change');

                // set unique IDs so that labels are correlated with inputs
                element.attr('id', id);
                element.siblings('label').attr('for', id);

                // set the default checked/unchecked state
                // if the field has been loaded with javascript
                if (hidden_element.val() != 0) {
                  element.prop('checked', 'checked');
                } else {
                  element.prop('checked', false);
                }

                hidden_element.on('CrudField:disable', function(e) {
                  element.prop('disabled', true);
                });
                hidden_element.on('CrudField:enable', function(e) {
                  element.removeAttr('disabled');
                });

                // when the checkbox is clicked
                // set the correct value on the hidden input
                element.change(function() {
                  if (element.is(":checked")) {
                    hidden_element.val(1).trigger('change');
                  } else {
                    hidden_element.val(0).trigger('change');
                  }
                })
            }
        </script>
        <?php } ?>
    <?php $__env->stopPush(); ?>



<?php /**PATH D:\Learning\Graduation-Project\Website\intelligent-college-timetable-scheduler\intelligent-college-timetable-scheduler\vendor\backpack\crud\src\resources\views\crud/fields/checkbox.blade.php ENDPATH**/ ?>