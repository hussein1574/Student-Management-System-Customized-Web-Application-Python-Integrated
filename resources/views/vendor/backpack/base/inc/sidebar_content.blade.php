{{-- This file is used to store sidebar items, inside the Backpack admin panel --}}
<?php
        $userId = backpack_user()->id;
        $professor =  App\Models\Professor::where('user_id', $userId)->first();
        $acadmicAdvisor =  App\Models\AcademicAdvisor::where('user_id', $userId)->first();
 ?>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>
@if($acadmicAdvisor)
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Students</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('student') }}"><i class="nav-icon la la-user"></i> Students</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('student-course') }}"><i class="nav-icon la la-book"></i> Student courses</a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-book"></i>Courses</a>
    <ul class="nav-dropdown-items">

        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('course') }}"><i class="nav-icon la la-book"></i> Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('course-pre') }}"><i class="nav-icon la la-book"></i> Course prerequisites</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('department-course') }}"><i class="nav-icon la la-question"></i> Course departments</a></li>
    </ul>
</li>
@if($professor)
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('upload-students-results') }}"><i class="nav-icon la la-newspaper-o"></i>Students results</a></li>
@endif
@elseif($professor)
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('upload-students-results') }}"><i class="nav-icon la la-newspaper-o"></i>Students results</a></li>
@else
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('admit-students-results') }}"><i class="nav-icon la la-newspaper-o"></i>Students results</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('upload-program') }}"><i class="nav-icon la la-newspaper-o"></i>Upload program</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-cog"></i>Generate</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('generate-exams') }}"><i class="nav-icon la la-newspaper-o"></i>Exams</a></li>
    </ul>
</li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> Users</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Students</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('student') }}"><i class="nav-icon la la-user"></i> Students</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('student-course') }}"><i class="nav-icon la la-book"></i> Student courses</a></li>
    </ul>
</li>
<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i>Professors</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('professor') }}"><i class="nav-icon la la-user"></i> Professors</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('professor-course') }}"><i class="nav-icon la la-book"></i> Professor courses</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('professor-day') }}"><i class="nav-icon la la-newspaper-o"></i> Professor days</a></li>
    </ul>
</li>



<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-book"></i>Courses</a>
    <ul class="nav-dropdown-items">

        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('course') }}"><i class="nav-icon la la-book"></i> Courses</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('course-pre') }}"><i class="nav-icon la la-book"></i> Course prerequisites</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('department-course') }}"><i class="nav-icon la la-question"></i> Course departments</a></li>
    </ul>
</li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('department') }}"><i class="nav-icon la la-question"></i> Departments</a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-newspaper-o"></i> Timetables</a>
    <ul class="nav-dropdown-items">
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('lectures-time-table') }}"><i class="nav-icon la la-newspaper-o"></i> Lectures time tables</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('exams-time-table') }}"><i class="nav-icon la la-newspaper-o"></i> Exams time tables</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('lectures-time') }}"><i class="nav-icon la la-question"></i> Time periods</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('hall') }}"><i class="nav-icon la la-question"></i> Halls</a></li>

        <li class="nav-item"><a class="nav-link" href="{{ backpack_url('day') }}"><i class="nav-icon la la-question"></i> Days</a></li>
    </ul>
</li>

<li class="nav-item"><a class="nav-link" href="{{ backpack_url('constant') }}"><i class="nav-icon la la-cog"></i> Settings</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('academic-advisor') }}"><i class="nav-icon la la-question"></i> Academic advisors</a></li>
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('halls-department') }}"><i class="nav-icon la la-question"></i> Halls departments</a></li>
@endif

