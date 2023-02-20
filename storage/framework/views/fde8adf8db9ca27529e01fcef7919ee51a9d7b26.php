<?php if(isset($saveAction['active']) && !is_null($saveAction['active']['value'])): ?>
    <div id="saveActions" class="form-group">

        <input type="hidden" name="_save_action" value="<?php echo e($saveAction['active']['value']); ?>">
        <?php if(!empty($saveAction['options'])): ?>
            <div class="btn-group" role="group">
        <?php endif; ?>

        <button type="submit" class="btn btn-success">
            <span class="la la-save" role="presentation" aria-hidden="true"></span> &nbsp;
            <span data-value="<?php echo e($saveAction['active']['value']); ?>"><?php echo e($saveAction['active']['label']); ?></span>
        </button>

        <div class="btn-group" role="group">
            <?php if(!empty($saveAction['options'])): ?>
                <button id="bpSaveButtonsGroup" type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><span class="caret"></span><span class="sr-only">&#x25BC;</span></button>
                <div class="dropdown-menu" aria-labelledby="bpSaveButtonsGroup">
                <?php $__currentLoopData = $saveAction['options']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <button type="button" class="dropdown-item" data-value="<?php echo e($value); ?>"><?php echo e($label); ?></button>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            <?php endif; ?>
        </div>

        <?php if(!empty($saveAction['options'])): ?>
            </div>
        <?php endif; ?>

        <?php if(!$crud->hasOperationSetting('showCancelButton') || $crud->getOperationSetting('showCancelButton') == true): ?>
            <a href="<?php echo e($crud->hasAccess('list') ? url($crud->route) : url()->previous()); ?>" class="btn btn-default"><span class="la la-ban"></span> &nbsp;<?php echo e(trans('backpack::crud.cancel')); ?></a>
        <?php endif; ?>

    </div>
<?php endif; ?>

<?php $__env->startPush('after_scripts'); ?>
<script>

    // this function checks if form is valid. 
    function checkFormValidity(form) {
        // the condition checks if `checkValidity` is defined in the form (browser compatibility)
        if (form[0].checkValidity) {
            return form[0].checkValidity();
        }
        return false;
    }

    // this function checks if any of the inputs has errors and report them on page.
    // we use it to report the errors after form validation fails and making the error fields visible
    function reportValidity(form) {
        // the condition checks if `reportValidity` is defined in the form (browser compatibility)
        if (form[0].reportValidity) {
            // hide the save actions drop down if open
            $('#saveActions').find('.dropdown-menu').removeClass('show');
            // validate and display form errors
            form[0].reportValidity();
        }
    }

    function changeTabIfNeededAndDisplayErrors(form) {
        // we get the first erroed field
        var $firstErrorField = form.find(":invalid").first();
        // we find the closest tab
        var $closestTab = $($firstErrorField).closest('.tab-pane');
        // if we found the tab we will change to that tab before reporting validity of form
        if($closestTab.length) {
            var id = $closestTab.attr('id');
                // switch tabs
                $('.nav a[href="#' + id + '"]').tab('show');
        }
        reportValidity(form);
    }

    // make all submit buttons trigger HTML5 validation
    jQuery(document).ready(function($) {

        var selector = $('#bpSaveButtonsGroup').next();
        var form = $(selector).closest('form');
        var saveActionField = $('[name="_save_action"]');
        var $defaultSubmitButton = $(form).find(':submit');
        // this is the main submit button, the default save action.
        $($defaultSubmitButton).on('click', function(e) {
            e.preventDefault();
            $saveAction = $(this).children('span').eq(1);
            // if form is valid just submit it
            if(checkFormValidity(form)) {
                saveActionField.val( $saveAction.attr('data-value') );
                form[0].requestSubmit();
            }else{
                // navigate to the tab where the first error happens 
                changeTabIfNeededAndDisplayErrors(form);
            }
        });

        //this is for the anchors AKA other non-default save actions.
        $(selector).find('button').each(function() {
            $(this).click(function(e) {
                //we check if form is valid
                if (checkFormValidity(form)) {
                    //if everything is validated we proceed with the submission
                    var saveAction = $(this).data('value');
                    saveActionField.val( saveAction );
                    form[0].requestSubmit();
                }else{
                    // navigate to the tab where the first error happens 
                    changeTabIfNeededAndDisplayErrors(form);
                }
                e.stopPropagation();
            });
        });
    });

    </script>
<?php $__env->stopPush(); ?>
<?php /**PATH D:\Learning\Graduation-Project\Website\intelligent-college-timetable-scheduler\intelligent-college-timetable-scheduler(test)\vendor\backpack\crud\src\resources\views\crud/inc/form_save_buttons.blade.php ENDPATH**/ ?>