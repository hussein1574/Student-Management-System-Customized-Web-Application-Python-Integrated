<?php

use App\Http\Controllers\ExamsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GraduationController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\StudentHoursPerYearController;
use App\Http\Controllers\StudentFinishedCoursesController;

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
Route::get('current-courses/{user_id}/', [StudentCoursesController::class, 'index']);
Route::get('finished-courses/{user_id}/', [StudentFinishedCoursesController::class, 'index']);
Route::get('/student-hours/{user_id}/', [GraduationController::class, 'index']);
Route::get('/finished-hours-by-year/{user_id}/', [StudentHoursPerYearController::class, 'index']);
Route::get('/exams/{user_id}/', [ExamsController::class, 'index']);
