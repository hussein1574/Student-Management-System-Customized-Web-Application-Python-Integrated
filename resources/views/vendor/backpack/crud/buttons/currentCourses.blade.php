<?php
$route = $crud->route;
$route = str_replace("/student", "", $route);
?>

<a href="{{ url($route.'/curr-student-courses/'.$entry->getKey()) }} " class="btn btn-sm btn-link"><i class="la la-edit"></i> Current Courses</a>


