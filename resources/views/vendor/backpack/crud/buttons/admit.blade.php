<?php
    $studentCoursesController = new \App\Http\Controllers\StudentCoursesController();
    $appear = $studentCoursesController->hasPendingCourses($entry->getKey());
    $route = $crud->route;
    $route = str_replace('/student', '', $route);

?>

@if ($appear)
<a href="{{ url($route.'/admit-student-courses/'.$entry->getKey()) }} " class="btn btn-sm btn-link"><i class="la la-edit"></i> Admit Courses</a>


@else
<a href="{{ url($route.'/admit-student-courses/'.$entry->getKey()) }} " class="btn btn-sm btn-link"><i class="la la-edit"></i> Add courses</a>
@endif
