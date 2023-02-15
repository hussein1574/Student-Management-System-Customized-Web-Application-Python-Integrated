<?php

use App\Http\Controllers\CourseRegistrationController;
use App\Http\Controllers\ExamsTimeTableController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\LectureTimetableController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('current-courses/{user_id}/', [StudentCoursesController::class, 'getCurrentCourses']);
Route::get('finished-courses/{user_id}/', [StudentCoursesController::class, 'getFinishedCourses']);
Route::get('/student-hours/{user_id}/', [StudentCoursesController::class, 'getGraduationHours']);
Route::get('/finished-hours-by-year/{user_id}/', [StudentCoursesController::class, 'getHoursPerYear']);
Route::get('/exams/{user_id}/', [ExamsTimeTableController::class, 'getExams']);
Route::get('/time-table/{user_id}/', [LectureTimetableController::class, 'getTimetable']);
Route::get('/register-courses/{user_id}/', [CourseRegistrationController::class, 'getCoursesStatus']);
Route::get('/hours-per-term/{user_id}/', [CourseRegistrationController::class, 'getHoursPerTerm']);
Route::post('/register-courses/{userId}', [CourseRegistrationController::class, 'register']);
