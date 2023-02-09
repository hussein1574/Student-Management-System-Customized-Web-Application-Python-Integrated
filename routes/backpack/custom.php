<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    Route::crud('role', 'RoleCrudController');
    Route::crud('lectures-time', 'LecturesTimeCrudController');
    Route::crud('user', 'UserCrudController');
    Route::crud('student-course', 'StudentCourseCrudController');
    Route::crud('course', 'CourseCrudController');
    Route::crud('lectures-time-table', 'LecturesTimeTableCrudController');
    Route::crud('course-pre', 'CoursePreCrudController');
    Route::crud('hall', 'HallCrudController');
    Route::crud('exams-time-table', 'ExamsTimeTableCrudController');
    Route::crud('student', 'StudentCrudController');
    Route::crud('department-course', 'DepartmentCourseCrudController');
    Route::crud('department', 'DepartmentCrudController');
    Route::crud('professor-day', 'ProfessorDayCrudController');
    Route::crud('day', 'DayCrudController');
    Route::crud('professor-course', 'ProfessorCourseCrudController');
    Route::crud('course-status', 'CourseStatusCrudController');
    Route::crud('professor', 'ProfessorCrudController');
    Route::crud('course-statu', 'CourseStatuCrudController');
}); // this should be the absolute last line of this file