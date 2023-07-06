<?php

use App\Models\Day;
use App\Models\Hall;
use App\Models\User;
use App\Models\Course;
use App\Models\Constant;
use App\Models\Professor;
use App\Models\LecturesTime;
use App\Models\StudentCourse;
use App\Models\ExamsTimeTable;
use App\Models\LecturesTimeTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UploadProgram;
use App\Http\Controllers\ExamsTimeTableController;
use App\Http\Controllers\StudentCoursesController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\LectureTimetableController;

// --------------------------
// Custom Backpack Routes
// --------------------------
// This route file is loaded automatically by Backpack\Base.
// Routes you generate using Backpack\Generators will be placed here.

Route::group(
    [
        "prefix" => config("backpack.base.route_prefix", "admin"),
        "middleware" => array_merge(
            (array) config("backpack.base.web_middleware", "web"),
            (array) config("backpack.base.middleware_key", "admin")
        ),
        "namespace" => "App\Http\Controllers\Admin",
    ],
    function () {
        // custom admin routes
        Route::get("/admit-student-courses/{studentId}", [
            StudentCoursesController::class,
            "getStudentCourses",
        ])->name("get-student-courses");
        Route::get("/transcript/{studentId}", [
            StudentCoursesController::class,
            "transscriptShow",
        ])->name("transcript.show");
        Route::post("/admit-student-courses/{studentId}", [
            StudentCoursesController::class,
            "admitStudentCourses",
        ])->name("admit-student-courses");

        Route::get("/register-student-course/{studentId}", [
            StudentCoursesController::class,
            "showStudentCourse",
        ])->name("show-student-course");
        Route::post("/register-student-course", [
            StudentCoursesController::class,
            "registerStudentCourse",
        ])->name("register-student-course");

        Route::delete("/delete-student-course", [
            StudentCoursesController::class,
            "deleteStudentCourse",
        ])->name("delete-student-course");

        Route::get("/upload-program", [UploadProgram::class, "index"])->name(
            "upload-program"
        );
        Route::post("/upload-program", [UploadProgram::class, "upload"])->name(
            "upload-program"
        );
        Route::delete("/clear-regulation-courses", [
            UploadProgram::class,
            "delete",
        ])->name("clear-regulation-courses");

        Route::get("/upload-students-results", [
            StudentCoursesController::class,
            "uploadStudentsResultsIndex",
        ])->name("upload-students-results");
        Route::post("/get-students-for-course", [
            StudentCoursesController::class,
            "getCourseStudents",
        ])->name("get-students-for-course");
        Route::post("/save-students-results", [
            StudentCoursesController::class,
            "saveStudentsResults",
        ])->name("save-students-results");
        Route::post("/upload-students-results", [
            StudentCoursesController::class,
            "uploadStudentsResults",
        ])->name("upload-students-results");
        Route::post("/improve-grades", [
            StudentCoursesController::class,
            "improveGrades",
        ])->name("improve-grades");

        Route::get("/admit-students-results", [
            StudentCoursesController::class,
            "admitStudentsResultsIndex",
        ])->name("admit-students-results");
        Route::post("/admit-students-results", [
            StudentCoursesController::class,
            "admitStudentsResults",
        ])->name("admit-students-results");
        Route::delete("/delete-student-results", [
            StudentCoursesController::class,
            "deleteStudentResults",
        ])->name("delete-student-results");

        Route::get("/generate-exams", [
            ExamsTimeTableController::class,
            "index",
        ])->name("generate-exams");
        Route::delete("/clear-exam-timetable", [
            ExamsTimeTableController::class,
            "clearExams",
        ])->name("clear-exam-timetable");
        Route::post("/run-script", [
            ExamsTimeTableController::class,
            "runScript",
        ])->name("run-script");
        Route::post("/admit-exams", [
            ExamsTimeTableController::class,
            "admitExams",
        ])->name("admit-exams");

        Route::get("/generate-timetable", [
            LectureTimetableController::class,
            "index",
        ])->name("generate-timetable");
        Route::delete("/clear-timetable", [
            LectureTimetableController::class,
            "clearTimetable",
        ])->name("clear-timetable");
        Route::post("/run-timetable-script", [
            LectureTimetableController::class,
            "runScript",
        ])->name("run-timetable-script");
        Route::post("/admit-timetable", [
            LectureTimetableController::class,
            "admitTimetable",
        ])->name("admit-timetable");

        Route::get("/dashboard/failed-students-chart-data", [
            DashboardController::class,
            "failedStudentChartData",
        ])->name("dashboard.failedStudentsChartData");
        Route::get("/dashboard/registered-students-chart-data", [
            DashboardController::class,
            "registeredStudentChartData",
        ])->name("dashboard.registeredStudentsChartData");

        Route::delete("/clear-students-registration", [
            DashboardController::class,
            "clearStudentsRegistration",
        ])->name("clear-students-registration");
        Route::post("/change-registration-state", [
            DashboardController::class,
            "changeRegistrationState",
        ])->name("change-registration-state");

        Route::get("/time-table-admition", function () {
            $lecturesTableAdmited = Constant::where(
                "name",
                "Timetable Published"
            )->first()->value;
            $examsTableAdmited = Constant::where(
                "name",
                "ExamTimetable Published"
            )->first()->value;
            $lectures = LecturesTimeTable::all();
            $exams = ExamsTimeTable::all();
            $lecturesSheet = base_path(
                "app\scripts\lectures\Lecture_Table.xlsx"
            );
            $examsSheet = base_path("app/scripts/Exams_Table.xlsx");
            $lectureFitnessFile = base_path("app/scripts/lectures/fitness.txt");
            $examFitnessFile = base_path("app/scripts/fitness.txt");
            // read the fitness file text
            $timeTableProblems = file_get_contents($lectureFitnessFile);
            $examProblems = file_get_contents($examFitnessFile);

            $hallsData = Hall::all();
            $halls = [];
            foreach ($hallsData as $hall) {
                if ($hall->is_active) {
                    $halls[] = $hall->name;
                }
            }
            $daysData = Day::all();
            $days = [];
            foreach ($daysData as $day) {
                $days[] = $day->name;
            }
            $timeperiodsData = LecturesTime::all();
            $timeperiods = [];
            foreach ($timeperiodsData as $timeperiod) {
                $timeperiods[] = $timeperiod->timePeriod;
            }
            //check if there job table is empty
            if (DB::table("jobs")->count() == 0) {
                if (!file_exists($lecturesSheet)) {
                    session()->flash("alert-lectures", "error");
                    session()->flash(
                        "message-lectures",
                        "Failed to generate lectures timetable"
                    );
                }
                if (!file_exists($examsSheet)) {
                    session()->flash("alert-exams", "error");
                    session()->flash(
                        "message-exams",
                        "Failed to generate exams timetable"
                    );
                }
                if (!file_exists($lecturesSheet) && !file_exists($examsSheet)) {
                    return view("timetablesPage");
                } elseif (
                    file_exists($lecturesSheet) &&
                    !file_exists($examsSheet)
                ) {
                    $lecturesData = [];
                    //map lectures to hall name and day name and time name and professor name and course name
                    foreach ($lectures as $lecture) {
                        $profName = "";
                        if ($lecture->professor_id != null) {
                            $profName = User::find(
                                Professor::find($lecture->professor_id)->user_id
                            )->name;
                        } elseif ($lecture->isLab == 1) {
                            $profName = "Lab";
                        } else {
                            $profName = "Section";
                        }
                        $lecturesData[] = [
                            "hall_name" => Hall::find($lecture->hall_id)->name,
                            "day_name" => Day::find($lecture->day_id)->name,
                            "time_name" => LecturesTime::find(
                                $lecture->lectureTime_id
                            )->timePeriod,
                            "professor_name" => $profName,
                            "course_name" => Course::find($lecture->course_id)
                                ->name,
                        ];
                    }
                    // Restructure the data to organize exams by time period
                    $lectureTableData = [];
                    foreach ($timeperiods as $period) {
                        $lectureTableData[$period] = [];
                        foreach ($days as $day) {
                            $lectureTableData[$period][$day] = [];
                        }
                    }

                    foreach ($lecturesData as $lecture) {
                        $timePeriod = $lecture["time_name"];
                        $day = $lecture["day_name"];
                        $lectureTableData[$timePeriod][$day][] = $lecture;
                    }
                    if (empty($lecturesData)) {
                        $timeTableProblems = "";
                    }
                    return view(
                        "timetablesPage",
                        compact(
                            "lectureTableData",
                            "halls",
                            "days",
                            "timeperiods",
                            "lecturesTableAdmited",
                            "timeTableProblems"
                        )
                    );
                } elseif (
                    file_exists($examsSheet) &&
                    !file_exists($lecturesSheet)
                ) {
                    $examsData = [];
                    foreach ($exams as $exam) {
                        $examsData[] = [
                            "hall_name" => Hall::find($exam->hall_id)->name,
                            "day_name" => $exam->day,
                            "course_name" => Course::find($exam->course_id)
                                ->name,
                        ];
                    }

                    $examDays = array_unique(
                        array_column($examsData, "day_name")
                    );

                    // Restructure the data to organize exams by hall and day
                    $examTableData = [];
                    foreach ($halls as $hall) {
                        $examTableData[$hall] = [];
                        foreach ($examDays as $day) {
                            $examTableData[$hall][$day] = [];
                        }
                    }

                    foreach ($examsData as $exam) {
                        $hall = $exam["hall_name"];
                        $day = $exam["day_name"];
                        $examTableData[$hall][$day][] = $exam;
                    }
                    if (empty($examsData)) {
                        $examProblems = "";
                    }
                    return view(
                        "timetablesPage",
                        compact(
                            "examTableData",
                            "halls",
                            "examDays",
                            "examsTableAdmited",
                            "examProblems"
                        )
                    );
                } else {
                    $lecturesData = [];
                    $examsData = [];
                    foreach ($lectures as $lecture) {
                        $profName = "";
                        if ($lecture->professor_id != null) {
                            $profName = User::find(
                                Professor::find($lecture->professor_id)->user_id
                            )->name;
                        } elseif ($lecture->isLab == 1) {
                            $profName = "Lab";
                        } else {
                            $profName = "Section";
                        }
                        $lecturesData[] = [
                            "hall_name" => Hall::find($lecture->hall_id)->name,
                            "day_name" => Day::find($lecture->day_id)->name,
                            "time_name" => LecturesTime::find(
                                $lecture->lectureTime_id
                            )->timePeriod,
                            "professor_name" => $profName,
                            "course_name" => Course::find($lecture->course_id)
                                ->name,
                        ];
                    }

                    foreach ($exams as $exam) {
                        $examsData[] = [
                            "hall_name" => Hall::find($exam->hall_id)->name,
                            "day_name" => $exam->day,
                            "course_name" => Course::find($exam->course_id)
                                ->name,
                        ];
                    }
                    // Restructure the data to organize exams by time period
                    $lectureTableData = [];
                    foreach ($timeperiods as $period) {
                        $lectureTableData[$period] = [];
                        foreach ($days as $day) {
                            $lectureTableData[$period][$day] = [];
                        }
                    }

                    foreach ($lecturesData as $lecture) {
                        $timePeriod = $lecture["time_name"];
                        $day = $lecture["day_name"];
                        $lectureTableData[$timePeriod][$day][] = $lecture;
                    }

                    $examDays = array_unique(
                        array_column($examsData, "day_name")
                    );

                    // Restructure the data to organize exams by hall and day
                    $examTableData = [];
                    foreach ($halls as $hall) {
                        $examTableData[$hall] = [];
                        foreach ($examDays as $day) {
                            $examTableData[$hall][$day] = [];
                        }
                    }

                    foreach ($examsData as $exam) {
                        $hall = $exam["hall_name"];
                        $day = $exam["day_name"];
                        $examTableData[$hall][$day][] = $exam;
                    }
                    if (empty($lecturesData)) {
                        $timeTableProblems = "";
                    }
                    if (empty($examsData)) {
                        $examProblems = "";
                    }
                    return view(
                        "timetablesPage",
                        compact(
                            "lectureTableData",
                            "examTableData",
                            "halls",
                            "days",
                            "timeperiods",
                            "examDays",
                            "lecturesTableAdmited",
                            "examsTableAdmited",
                            "timeTableProblems",
                            "examProblems"
                        )
                    );
                }
            } else {
                session()->flash("alert-generate", "info");
                session()->flash(
                    "message-generate",
                    "Time table is still being generated"
                );
                return view("timetablesPage");
            }
        })->name("time-table-admition");

        Route::crud("lectures-time", "LecturesTimeCrudController");
        Route::crud("user", "UserCrudController");
        Route::crud("student-course", "StudentCourseCrudController");
        Route::crud("course", "CourseCrudController");
        Route::crud("lectures-time-table", "LecturesTimeTableCrudController");
        Route::crud("course-pre", "CoursePreCrudController");
        Route::crud("hall", "HallCrudController");
        Route::crud("exams-time-table", "ExamsTimeTableCrudController");
        Route::crud("student", "StudentCrudController");
        Route::crud("department-course", "DepartmentCourseCrudController");
        Route::crud("department", "DepartmentCrudController");
        Route::crud("professor-day", "ProfessorDayCrudController");
        Route::crud("day", "DayCrudController");
        Route::crud("professor-course", "ProfessorCourseCrudController");
        Route::crud("course-status", "CourseStatusCrudController");
        Route::crud("professor", "ProfessorCrudController");
        Route::crud("constant", "ConstantCrudController");
        Route::crud("academic-advisor", "AcademicAdvisorCrudController");
        Route::crud("halls-department", "HallsDepartmentCrudController");
    }
); // this should be the absolute last line of this file