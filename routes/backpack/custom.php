<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ExamsTimeTableController;
use App\Http\Controllers\Admin\DashboardController;

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
    Route::get('/generate-exams', [ExamsTimeTableController::class, 'index'])->name('generate-exams');
    Route::post('/run-script', [ExamsTimeTableController::class, 'runScript'])->name('run-script');
    Route::get('/dashboard/failed-students-chart-data', [DashboardController::class, 'failedStudentChartData'])->name('dashboard.failedStudentsChartData');
    Route::get('/dashboard/registered-students-chart-data', [DashboardController::class, 'registeredStudentChartData'])->name('dashboard.registeredStudentsChartData');
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
    Route::crud('constant', 'ConstantCrudController');
}); // this should be the absolute last line of this file