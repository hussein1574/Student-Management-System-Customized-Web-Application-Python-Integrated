<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ExamsTimeTableController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LectureTimetableController;
use App\Http\Controllers\CourseRegistrationController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get("/", function () {
    return view("welcome");
});

Route::get("/dashboard", function () {
    return view("dashboard");
})
    ->middleware(["auth", "guest"])
    ->name("dashboard");

Route::get("/info", function () {
    $halls = DB::table("halls")
        ->select("id", "name")
        ->get()
        ->toArray();
    $days = DB::table("days")
        ->select("id", "name")
        ->get()
        ->toArray();
    $periods = DB::table("lectures_times")
        ->select("id", "timePeriod")
        ->get()
        ->toArray();
    $departments = DB::table("departments")
        ->select("id", "name")
        ->get()
        ->toArray();

    return response()->json([
        "halls" => $halls,
        "days" => $days,
        "periods" => $periods,
        "departments" => $departments,
    ]);
});

Route::middleware("auth:api")->group(function () {
    Route::post("/change-pass", [UserController::class, "changePassword"]);
    Route::get("/profile", [UserController::class, "profile"])->name(
        "profile.edit"
    );
    Route::patch("/profile", [ProfileController::class, "update"])->name(
        "profile.update"
    );
    Route::delete("/profile", [ProfileController::class, "destroy"])->name(
        "profile.destroy"
    );
    Route::get("/register-courses", [
        CourseRegistrationController::class,
        "getCoursesStatus",
    ]);
    Route::post("/register-courses", [
        CourseRegistrationController::class,
        "register",
    ]);
    Route::get("/current-courses", [
        StudentCoursesController::class,
        "getCurrentCourses",
    ]);
    Route::get("/finished-courses", [
        StudentCoursesController::class,
        "getFinishedCourses",
    ]);
    Route::get("/recent-finished-courses", [
        StudentCoursesController::class,
        "getLastTermFinishedCourses",
    ]);
    Route::get("/student-hours", [
        StudentCoursesController::class,
        "getGraduationHours",
    ]);
    Route::get("/finished-hours-by-year", [
        StudentCoursesController::class,
        "getHoursForFinishedYears",
    ]);
    Route::get("/exams", [ExamsTimeTableController::class, "getExams"]);
    Route::get("/time-table", [
        LectureTimetableController::class,
        "getTimetable",
    ]);
    Route::get("/hours-per-term", [
        CourseRegistrationController::class,
        "getHoursPerTerm",
    ]);
});

require __DIR__ . "/auth.php";
