
<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('dashboard')); ?>"><i class="la la-home nav-icon"></i> <?php echo e(trans('backpack::base.dashboard')); ?></a></li>


<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Authentication</a>
       <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('user')); ?>"><i class="nav-icon la la-user"></i> Users</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('role')); ?>"><i class="nav-icon la la-group"></i> Roles</a></li>
    </ul>
  </li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-book"></i>Courses</a>
       <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('course')); ?>"><i class="nav-icon la la-book"></i> Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('course-pre')); ?>"><i class="nav-icon la la-book"></i> Course prerequisites</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('course-status')); ?>"><i class="nav-icon la la-info-circle"></i> Course statuses</a></li> 
        <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('department-course')); ?>"><i class="nav-icon la la-question"></i> Course departments</a></li>   
    </ul>
  </li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-user"></i>Students</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('student')); ?>"><i class="nav-icon la la-user"></i> Students</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('student-course')); ?>"><i class="nav-icon la la-book"></i> Student courses</a></li>  
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-user"></i>Proffesors</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('professor')); ?>"><i class="nav-icon la la-user"></i> Professors</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('professor-course')); ?>"><i class="nav-icon la la-book"></i> Professor courses</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('professor-day')); ?>"><i class="nav-icon la la-newspaper-o"></i> Professor days</a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-newspaper-o"></i> Timetables</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('lectures-time-table')); ?>"><i class="nav-icon la la-newspaper-o"></i> Lectures time tables</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('exams-time-table')); ?>"><i class="nav-icon la la-newspaper-o"></i> Exams time tables</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('lectures-time')); ?>"><i class="nav-icon la la-question"></i> Lectures times</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('hall')); ?>"><i class="nav-icon la la-question"></i> Halls</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('department')); ?>"><i class="nav-icon la la-question"></i> Departments</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('day')); ?>"><i class="nav-icon la la-question"></i> Days</a></li>
        </ul>
  </li>

<li class="nav-item"><a class="nav-link" href="<?php echo e(backpack_url('constant')); ?>"><i class="nav-icon la la-cog"></i> Settings</a></li>


<?php /**PATH D:\Learning\Graduation-Project\Website\intelligent-college-timetable-scheduler\intelligent-college-timetable-scheduler(test)\resources\views/vendor/backpack/base/inc/sidebar_content.blade.php ENDPATH**/ ?>