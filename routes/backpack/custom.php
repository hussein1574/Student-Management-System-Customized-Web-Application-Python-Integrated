<?php

use App\Models\StudentCourse;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadProgram;
use App\Http\Controllers\ExamsTimeTableController;
use App\Http\Controllers\StudentCoursesController;
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
    Route::get('/admit-student-courses/{studentId}',[StudentCoursesController::class,'getStudentCourses'])->name('get-student-courses');
    Route::post('/admit-student-courses/{studentId}',[StudentCoursesController::class,'admitStudentCourses'])->name('admit-student-courses');

    Route::get('/register-student-course/{studentId}',[StudentCoursesController::class,'showStudentCourse'])->name('show-student-course');
    Route::post('/register-student-course',[StudentCoursesController::class,'registerStudentCourse'])->name('register-student-course');

    Route::delete('/delete-student-course',[StudentCoursesController::class,'deleteStudentCourse'])->name('delete-student-course');

    Route::get('/upload-program', [UploadProgram::class, 'index'])->name('upload-program');
    Route::post('/upload-program',[UploadProgram::class,'upload'])->name('upload-program');
    Route::delete('/clear-regulation-courses',[UploadProgram::class,'delete'])->name('clear-regulation-courses');

    Route::get('/upload-students-results', [StudentCoursesController::class, 'uploadStudentsResultsIndex'])->name('upload-students-results');
    Route::post('/upload-students-results',[StudentCoursesController::class,'uploadStudentsResults'])->name('upload-students-results');

    Route::get('/admit-students-results', [StudentCoursesController::class, 'admitStudentsResultsIndex'])->name('admit-students-results');
    Route::post('/admit-students-results',[StudentCoursesController::class,'admitStudentsResults'])->name('admit-students-results');
    Route::delete('/delete-student-results',[StudentCoursesController::class,'deleteStudentResults'])->name('delete-student-results');


    Route::get('/generate-exams', [ExamsTimeTableController::class, 'index'])->name('generate-exams');
    Route::delete('/clear-exam-timetable', [ExamsTimeTableController::class, 'clearExams'])->name('clear-exam-timetable');
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
    Route::crud('academic-advisor', 'AcademicAdvisorCrudController');
}); // this should be the absolute last line of this file