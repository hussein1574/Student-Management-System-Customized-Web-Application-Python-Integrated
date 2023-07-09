<?php

namespace App\Http\Controllers;

use App\Models\Course;
use League\Csv\Writer;
use App\Models\Student;
use App\Models\CoursePre;
use App\Models\Professor;
use Termwind\Components\Dd;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Jobs\ResultsCsvProcess;
use App\Models\ProfessorCourse;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Jobs\UpdateStudentsGPAProcess;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DeleteStudentResultsProcess;
use App\Http\Controllers\CourseRegistrationController;
use OpenSpout\Writer\XLSX\MergeCell;
use Illuminate\Support\Facades\View;

class StudentCoursesController extends Controller
{
    public function getStudentCoursesIndex(Request $request, $studentId)
    {
        $student = Student::where("id", $studentId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found ",
                ],
                404
            );
        }
        $studentName = $student->user->name;
        $studentCourses = StudentCourse::where("student_id", $studentId)
            ->where("status_id", 3)
            ->get();
        // get the courses name and hours from the courses table
        $courses = [];
        foreach ($studentCourses as $studentCourse) {
            $course = Course::where("id", $studentCourse->course_id)->first();
            $courses[] = [
                "name" => $course->name,
                "hours" => floor(
                    $course->LectureHours +
                        $course->labHours / 2 +
                        $course->sectionHours / 2
                ),
                "level" => $course->level,
            ];
        }
        return view("studentCurrentCourses", compact("courses", "studentName"));
    }
    public function transscriptShow(Request $request, $studentId)
    {
        $studentData = Student::where("id", $studentId)->first();
        if ($studentData->grade == 4.0) {
            $grade = "A+";
        } elseif ($studentData->grade >= 3.7) {
            $grade = "A-";
        } elseif ($studentData->grade >= 3.3) {
            $grade = "B+";
        } elseif ($studentData->grade >= 3.0) {
            $grade = "B";
        } elseif ($studentData->grade >= 2.7) {
            $grade = "B-";
        } elseif ($studentData->grade >= 2.3) {
            $grade = "C+";
        } elseif ($studentData->grade >= 2) {
            $grade = "C";
        } elseif ($studentData->grade >= 1.7) {
            $grade = "C-";
        } elseif ($studentData->grade >= 1.3) {
            $grade = "D+";
        } elseif ($studentData->grade >= 1.0) {
            $grade = "D";
        } elseif ($studentData->grade >= 0.0) {
            $grade = "F";
        }
        $courseRegController = new CourseRegistrationController();
        $level = $courseRegController->getStudentLevel($studentId);
        switch ($level) {
            case 0:
                $year = "Freshman";
                break;
            case 1:
                $year = "Sophomore";
                break;
            case 2:
                $year = "Junior";
                break;
            case 3:
                $year = "Senior-1";
                break;
            case 4:
                $year = "Senior-2";
                break;
            default:
                $year = "Unknown";
        }

        $student = [
            "name" => $studentData->user->name,
            "department" => $studentData->department->name,
            "year" => $year,
            "gpa" => $studentData->grade,
            "grade" => $grade,
        ];
        $courses = StudentCourse::where("student_id", $studentId)
            ->whereIN("status_id", [1, 2])
            ->get();
        $years = [];
        $earliestYear = null; // Initialize earliest year to null
        $latestYear = null;
        foreach ($courses as $course) {
            $year = $course->created_at->format("Y");
            if (!in_array($year, $years)) {
                $years[] = $year;
                if ($earliestYear === null || $year < $earliestYear) {
                    $earliestYear = $year; // Update earliest year if a new earliest year is found
                }
                if ($latestYear === null || $year > $latestYear) {
                    $latestYear = $year; // Update latest year if a new latest year is found
                }
            }
        }
        $years = [];
        $earliestYear--;
        if ($earliestYear !== null) {
            while ($earliestYear <= $latestYear) {
                $FirstTermDate = $earliestYear . "-09-01";
                $SecondTermDate = $earliestYear + 1 . "-02-01";
                $SummerTermDate = $earliestYear + 1 . "-06-01";
                $endDate = $earliestYear + 1 . "-09-01";
                $FirstTermcourses = StudentCourse::where(
                    "student_id",
                    $studentId
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [
                        $FirstTermDate,
                        $SecondTermDate,
                    ])
                    ->get();
                $SecondTermcourses = StudentCourse::where(
                    "student_id",
                    $studentId
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [
                        $SecondTermDate,
                        $SummerTermDate,
                    ])
                    ->get();
                $SummerTermcourses = StudentCourse::where(
                    "student_id",
                    $studentId
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [$SummerTermDate, $endDate])
                    ->get();
                $years[$earliestYear . "/" . ($earliestYear + 1)] = [
                    "First term" => $FirstTermcourses,
                    "Second term" => $SecondTermcourses,
                    "Summer term" => $SummerTermcourses,
                ];
                $earliestYear++;
            }
        }
        // if there is a year with no courses, remove it
        foreach ($years as $year => $terms) {
            if (
                count($terms["First term"]) == 0 &&
                count($terms["Second term"]) == 0 &&
                count($terms["Summer term"]) == 0
            ) {
                unset($years[$year]);
            }
        }
        return view("transcript", compact("student", "years"));
    }

    public function getCurrentCourses(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where("user_id", $userId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found ",
                ],
                404
            );
        }

        $courses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 3)
            ->get();

        return response()->json([
            "status" => "success",
            "results" => $courses->count(),
            "data" => [
                "courses" => $courses->map(function ($course) {
                    return [
                        "name" => $course->course->name,
                        "hours" => floor(
                            $course->course->LectureHours +
                                $course->course->labHours / 2 +
                                $course->course->sectionHours / 2
                        ),
                        "level" => $course->course->level,
                    ];
                }),
            ],
        ]);
    }
    public function getFinishedCourses(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where("user_id", $userId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found",
                ],
                404
            );
        }

        $courses = StudentCourse::where("student_id", $student->id)
            ->whereIn("status_id", [1, 2])
            ->get();
        // get the courses for the current year
        $years = [];
        $earliestYear = null; // Initialize earliest year to null
        $latestYear = null;
        foreach ($courses as $course) {
            $year = $course->created_at->format("Y");
            if (!in_array($year, $years)) {
                $years[] = $year;
                if ($earliestYear === null || $year < $earliestYear) {
                    $earliestYear = $year; // Update earliest year if a new earliest year is found
                }
                if ($latestYear === null || $year > $latestYear) {
                    $latestYear = $year; // Update latest year if a new latest year is found
                }
            }
        }
        $years = [];
        $earliestYear--;
        if ($earliestYear !== null) {
            while ($earliestYear <= $latestYear) {
                $FirstTermDate = $earliestYear . "-09-01";
                $SecondTermDate = $earliestYear + 1 . "-02-01";
                $SummerTermDate = $earliestYear + 1 . "-06-01";
                $endDate = $earliestYear + 1 . "-09-01";
                $FirstTermcourses = StudentCourse::where(
                    "student_id",
                    $student->id
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [
                        $FirstTermDate,
                        $SecondTermDate,
                    ])
                    ->get();
                $SecondTermcourses = StudentCourse::where(
                    "student_id",
                    $student->id
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [
                        $SecondTermDate,
                        $SummerTermDate,
                    ])
                    ->get();
                $SummerTermcourses = StudentCourse::where(
                    "student_id",
                    $student->id
                )
                    ->whereIN("status_id", [1, 2])
                    ->whereBetween("created_at", [$SummerTermDate, $endDate])
                    ->get();
                $year = $earliestYear . "/" . ($earliestYear + 1);
                $years[] = [
                    "year" => $year,
                    "First term" => $FirstTermcourses->map(function ($course) {
                        $gpa = $this->getGPA(
                            $course->grade +
                                $course->class_work_grade +
                                $course->lab_grade
                        );
                        $grade = "";
                        if ($gpa == 4.0) {
                            $grade = "A+";
                        } elseif ($gpa >= 3.7) {
                            $grade = "A-";
                        } elseif ($gpa >= 3.3) {
                            $grade = "B+";
                        } elseif ($gpa >= 3.0) {
                            $grade = "B";
                        } elseif ($gpa >= 2.7) {
                            $grade = "B-";
                        } elseif ($gpa >= 2.3) {
                            $grade = "C+";
                        } elseif ($gpa >= 2) {
                            $grade = "C";
                        } elseif ($gpa >= 1.7) {
                            $grade = "C-";
                        } elseif ($gpa >= 1.3) {
                            $grade = "D+";
                        } elseif ($gpa >= 1.0) {
                            $grade = "D";
                        } elseif ($gpa >= 0.0) {
                            $grade = "F";
                        }
                        return [
                            "name" => $course->course->name,
                            "hours" => floor(
                                $course->course->LectureHours +
                                    $course->course->labHours / 2 +
                                    $course->course->sectionHours / 2
                            ),
                            "grade" => $gpa,
                            "level" => $course->course->level,
                            "status" => $grade,
                        ];
                    }),
                    "Second term" => $SecondTermcourses->map(function (
                        $course
                    ) {
                        $gpa = $this->getGPA(
                            $course->grade +
                                $course->class_work_grade +
                                $course->lab_grade
                        );
                        $grade = "";
                        if ($gpa == 4.0) {
                            $grade = "A+";
                        } elseif ($gpa >= 3.7) {
                            $grade = "A-";
                        } elseif ($gpa >= 3.3) {
                            $grade = "B+";
                        } elseif ($gpa >= 3.0) {
                            $grade = "B";
                        } elseif ($gpa >= 2.7) {
                            $grade = "B-";
                        } elseif ($gpa >= 2.3) {
                            $grade = "C+";
                        } elseif ($gpa >= 2) {
                            $grade = "C";
                        } elseif ($gpa >= 1.7) {
                            $grade = "C-";
                        } elseif ($gpa >= 1.3) {
                            $grade = "D+";
                        } elseif ($gpa >= 1.0) {
                            $grade = "D";
                        } elseif ($gpa >= 0.0) {
                            $grade = "F";
                        }
                        return [
                            "name" => $course->course->name,
                            "hours" => floor(
                                $course->course->LectureHours +
                                    $course->course->labHours / 2 +
                                    $course->course->sectionHours / 2
                            ),
                            "grade" => $gpa,
                            "level" => $course->course->level,
                            "status" => $grade,
                        ];
                    }),
                    "Summer term" => $SummerTermcourses->map(function (
                        $course
                    ) {
                        $gpa = $this->getGPA(
                            $course->grade +
                                $course->class_work_grade +
                                $course->lab_grade
                        );
                        $grade = "";
                        if ($gpa == 4.0) {
                            $grade = "A+";
                        } elseif ($gpa >= 3.7) {
                            $grade = "A-";
                        } elseif ($gpa >= 3.3) {
                            $grade = "B+";
                        } elseif ($gpa >= 3.0) {
                            $grade = "B";
                        } elseif ($gpa >= 2.7) {
                            $grade = "B-";
                        } elseif ($gpa >= 2.3) {
                            $grade = "C+";
                        } elseif ($gpa >= 2) {
                            $grade = "C";
                        } elseif ($gpa >= 1.7) {
                            $grade = "C-";
                        } elseif ($gpa >= 1.3) {
                            $grade = "D+";
                        } elseif ($gpa >= 1.0) {
                            $grade = "D";
                        } elseif ($gpa >= 0.0) {
                            $grade = "F";
                        }
                        return [
                            "name" => $course->course->name,
                            "hours" => floor(
                                $course->course->LectureHours +
                                    $course->course->labHours / 2 +
                                    $course->course->sectionHours / 2
                            ),
                            "grade" => $gpa,
                            "level" => $course->course->level,
                            "status" => $grade,
                        ];
                    }),
                ];
                $earliestYear++;
            }
        }
        return response()->json([
            "status" => "success",
            "results" => count($years),
            "data" => [
                "courses" => $years,
            ],
        ]);
    }
    public function getGraduationHours(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where("user_id", $userId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found",
                ],
                404
            );
        }

        $courses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 1)
            ->get();

        $finishedHours = 0;
        foreach ($courses as $course) {
            $finishedHours += floor(
                $course->course->LectureHours +
                    $course->course->labHours / 2 +
                    $course->course->sectionHours / 2
            );
        }
        $department = $student->department;
        $graduationHours = $department->graduation_hours;

        return response()->json([
            "status" => "success",
            "results" => 2,
            "data" => [
                "finishedHours" => $finishedHours,
                "graduationHours" => $graduationHours,
            ],
        ]);
    }
    public function getHoursForFinishedYears(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where("user_id", $userId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found",
                ],
                404
            );
        }
        $courses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 1)
            ->get();

        $finishedHoursByYear = $this->calculateHoursPerYear($courses);
        $years = [];
        foreach ($finishedHoursByYear as $year => $hours) {
            $years[] = [
                "year" => $year,
                "hours" => $hours,
            ];
        }

        return response()->json([
            "status" => "success",
            "results" => count($finishedHoursByYear),
            "data" => $years,
        ]);
    }
    public function calculateHoursPerYear($courses)
    {
        $finishedHoursByYear = [];
        foreach ($courses as $course) {
            $year = date("Y", strtotime($course->created_at));
            if (!isset($finishedHoursByYear[$year])) {
                $finishedHoursByYear[$year] = 0;
            }
            $finishedHoursByYear[$year] += floor(
                $course->course->LectureHours +
                    $course->course->labHours / 2 +
                    $course->course->sectionHours / 2
            );
        }
        return $finishedHoursByYear;
    }

    public function getStudentCourses(Request $request, $studentId)
    {
        $student = Student::where("id", $studentId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found ",
                ],
                404
            );
        }
        $pendingCourses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 4)
            ->get();
        $finishedCoursesObject = StudentCourse::where(
            "student_id",
            $student->id
        )
            ->whereIn("status_id", [1, 2])
            ->get();

        $finishedCourses = $finishedCoursesObject->map(function ($course) {
            return [
                "name" => $course->course->name,
                "hours" => floor(
                    $course->course->LectureHours +
                        $course->course->labHours / 2 +
                        $course->course->sectionHours / 2
                ),
                "grade" =>
                    $course->grade +
                    $course->class_work_grade +
                    $course->lab_grade,
                "level" => $course->course->level,
                "status" => $course->courseStatus->id,
            ];
        });
        $courseRegiserationController = new CourseRegistrationController();
        $studentCoursesStatus = $courseRegiserationController->getStudentCoursesStatus(
            $request,
            $student->id
        );
        usort($studentCoursesStatus, function ($a, $b) {
            return $a["level"] <=> $b["level"];
        });
        return view(
            "student-courses.index",
            compact(
                "pendingCourses",
                "finishedCourses",
                "studentCoursesStatus",
                "studentId"
            )
        );
    }
    public function admitStudentCourses(Request $request, $studentId)
    {
        $student = Student::where("id", $studentId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found ",
                ],
                404
            );
        }

        $pendingCourses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 4)
            ->get();
        foreach ($pendingCourses as $course) {
            $course->status_id = 3;
            $course->save();
        }

        header("Location: " . backpack_url("student"));
        exit();
    }

    public function showStudentCourse(Request $request, $studentId)
    {
        $student = Student::where("id", $studentId)->first();
        if (!$student) {
            return response()->json(
                [
                    "status" => "fail",
                    "message" => "Student not found ",
                ],
                404
            );
        }
        $finishedCoursesObject = StudentCourse::where(
            "student_id",
            $student->id
        )
            ->whereIn("status_id", [1, 2])
            ->get();

        $finishedCourses = $finishedCoursesObject->map(function ($course) {
            return [
                "name" => $course->course->name,
                "hours" => floor(
                    $course->course->LectureHours +
                        $course->course->labHours / 2 +
                        $course->course->sectionHours / 2
                ),
                "grade" =>
                    $course->grade +
                    $course->class_work_grade +
                    $course->lab_grade,
                "level" => $course->course->level,
                "status" => $course->courseStatus->id,
            ];
        });

        $courseRegiserationController = new CourseRegistrationController();
        $studentCoursesStatus = $courseRegiserationController->getStudentCoursesStatus(
            $request,
            $student->id
        );
        $coursesPreRequists = CoursePre::all();
        usort($studentCoursesStatus, function ($a, $b) {
            return $a["level"] <=> $b["level"];
        });
        $preCourses = $coursesPreRequists->map(function ($course) {
            return [
                "course_id" => $course->course_id,
                "coursePre" => Course::where(
                    "id",
                    $course->coursePre_id
                )->first()->name,
                "passed" => $course->passed,
            ];
        });
        return view(
            "student-courses.add",
            compact(
                "finishedCourses",
                "preCourses",
                "studentCoursesStatus",
                "studentId"
            )
        );
    }

    public function registerStudentCourse(Request $request)
    {
        $studentId = $request->student_id;
        $courseId = $request->course_id;

        $studentCourse = new StudentCourse();
        $studentCourse->student_id = $studentId;
        $studentCourse->course_id = $courseId;
        $studentCourse->grade = 0;
        $studentCourse->status_id = 4;
        $studentCourse->save();

        return $this->getStudentCourses($request, $studentId);
    }

    public function deleteStudentCourse(Request $request)
    {
        $studentId = $request->student_id;
        $courseId = $request->course_id;
        StudentCourse::where("student_id", $studentId)
            ->where("course_id", $courseId)
            ->where("status_id", 4)
            ->delete();

        return $this->getStudentCourses($request, $studentId);
    }

    public function hasPendingCourses($studentId)
    {
        $pendingCourses = StudentCourse::where("student_id", $studentId)
            ->where("status_id", 4)
            ->get();
        if ($pendingCourses->count() > 0) {
            return true;
        }
        return false;
    }

    public function uploadStudentsResultsIndex(Request $request)
    {
        $userId = backpack_user()->id;
        $professor = Professor::where("user_id", $userId)->first();
        $professorCourses = ProfessorCourse::where(
            "professor_id",
            $professor->id
        )->get();

        $professorCourses = $professorCourses->map(function ($course) {
            $courseId = $course->course->id;
            $coursesStatus = StudentCourse::where("course_id", $courseId)
                ->get()
                ->map(function ($course) {
                    return $course->status_id;
                });
            $uploaded = true;
            foreach ($coursesStatus as $status) {
                if ($status == 3) {
                    $uploaded = false;
                    break;
                }
            }
            return [
                "name" => $course->course->name,
                "id" => $course->course->id,
                "hasLab" => $course->course->labHours > 0,
                "hasSection" => $course->course->sectionHours > 0,
                "uploaded" => $uploaded,
            ];
        });
        $professorCourses = $professorCourses
            ->where("uploaded", false)
            ->toArray();
        if (empty($professorCourses)) {
            session()->flash("alert", "warning");
            session()->flash(
                "message",
                "You have uploaded the results for all your courses"
            );
        }

        return view("uploadStudentsResults", compact("professorCourses"));
    }

    public function getCourseStudents(Request $request)
    {
        $courseId = $request->input("course_id");
        $students = StudentCourse::where("course_id", $courseId)
            ->with([
                "student" => function ($query) {
                    $query->with("user");
                },
            ])
            ->get();
        $studentsIds = [];
        foreach ($students as $student) {
            $studentsIds[] = $student->student_id;
        }
        $students = Student::whereIn("id", $studentsIds)->get();
        $students = $students->map(function ($student) use (
            $students,
            $courseId
        ) {
            $studentCourse = StudentCourse::where("student_id", $student->id)
                ->where("course_id", $courseId)
                ->first();
            $course = Course::where("id", $courseId)->first();
            if ($course->labHours == 0 && $course->sectionHours == 0) {
                return [
                    "id" => $student->id,
                    "name" => $student->user->name,
                    "gpa" => $studentCourse->grade,
                ];
            } elseif ($course->labHours == 0) {
                return [
                    "id" => $student->id,
                    "name" => $student->user->name,
                    "gpa" => $studentCourse->grade,
                    "class_work" => $studentCourse->class_work_grade,
                ];
            } else {
                return [
                    "id" => $student->id,
                    "name" => $student->user->name,
                    "gpa" => $studentCourse->grade,
                    "class_work" => $studentCourse->class_work_grade,
                    "lab" => $studentCourse->lab_grade,
                ];
            }
        });
        return $students;
    }

    public function uploadStudentsResults(Request $request)
    {
        // Check if the Save button was clicked
        if ($request->input("save")) {
            // Perform the Save action
            $response = $this->saveStudentsResults($request);
        }

        // Check if the Send button was clicked
        if ($request->input("send")) {
            // Perform the Send action
            $response = $this->sendStudentsResults($request);
            redirect()->back();
        }

        // Redirect back to the form
        return $response;
    }

    public function saveStudentsResults(Request $request)
    {
        $courseId = $request->input("course_id");
        foreach ($request->gpa as $id => $gpa) {
            if ($request->class_work) {
                $classWork = $request->class_work[$id];
            } else {
                $classWork = 0;
            }
            if ($request->lab) {
                $lab = $request->lab[$id];
            } else {
                $lab = 0;
            }
            $results[] = [
                "id" => $id,
                "gpa" => $gpa,
                "class_work" => $classWork,
                "lab" => $lab,
            ];
        }

        foreach ($results as $result) {
            $studentId = $result["id"];
            $gpa = $result["gpa"];
            $classWork = $result["class_work"];
            $lab = $result["lab"];

            $studentCourse = StudentCourse::where("course_id", $courseId)
                ->where("student_id", $studentId)
                ->first();
            $studentCourse->grade = $gpa;
            $studentCourse->class_work_grade = $classWork;
            $studentCourse->lab_grade = $lab;
            $studentCourse->save();
        }

        return response()->json([
            "status" => "success",
            "result" => "The grades are saved successfully.",
        ]);
    }
    public function sendStudentsResults(Request $request)
    {
        $courseId = $request->input("course_id");
        foreach ($request->gpa as $id => $gpa) {
            if ($request->class_work) {
                $classWork = $request->class_work[$id];
            } else {
                $classWork = 0;
            }
            if ($request->lab) {
                $lab = $request->lab[$id];
            } else {
                $lab = 0;
            }
            $results[] = [
                "id" => $id,
                "gpa" => $gpa,
                "class_work" => $classWork,
                "lab" => $lab,
            ];
        }

        foreach ($results as $result) {
            $studentId = $result["id"];
            $gpa = $result["gpa"];
            $classWork = $result["class_work"];
            $lab = $result["lab"];

            $studentCourse = StudentCourse::where("course_id", $courseId)
                ->where("student_id", $studentId)
                ->first();
            $studentCourse->grade = $gpa;
            $studentCourse->class_work_grade = $classWork;
            $studentCourse->lab_grade = $lab;

            $fullDegree = $gpa + $classWork + $lab;

            $gpa = $this->getGPA($fullDegree);

            if ($gpa >= 1) {
                $studentCourse->status_id = 7;
            } else {
                $studentCourse->status_id = 8;
            }
            $studentCourse->save();
        }

        return response()->json([
            "status" => "success",
            "result" => "The grades are sent successfully.",
        ]);
    }

    public static function getGPA($fullDegree)
    {
        if ($fullDegree >= 97) {
            return 4.0;
        } elseif ($fullDegree >= 93 && $fullDegree < 97) {
            return 4.0;
        } elseif ($fullDegree >= 89 && $fullDegree < 93) {
            return 3.7;
        } elseif ($fullDegree >= 84 && $fullDegree < 89) {
            return 3.3;
        } elseif ($fullDegree >= 80 && $fullDegree < 84) {
            return 3.0;
        } elseif ($fullDegree >= 76 && $fullDegree < 80) {
            return 2.7;
        } elseif ($fullDegree >= 73 && $fullDegree < 76) {
            return 2.3;
        } elseif ($fullDegree >= 70 && $fullDegree < 73) {
            return 2.0;
        } elseif ($fullDegree >= 67 && $fullDegree < 70) {
            return 1.7;
        } elseif ($fullDegree >= 64 && $fullDegree < 67) {
            return 1.3;
        } elseif ($fullDegree >= 60 && $fullDegree < 64) {
            return 1.0;
        } else {
            return 0.0;
        }
    }

    public function improveGrades(Request $request)
    {
        $studentCourses = StudentCourse::where("course_id", $request->course_id)
            ->whereIn("status_id", [7, 8])
            ->get();
        $studentCourses->each(function ($studentCourse) use ($request) {
            if ($studentCourse->grade + $request->grade_value <= 50) {
                $studentCourse->grade += $request->grade_value;
            } else {
                $studentCourse->grade = 50;
            }
            if (
                $this->getGPA(
                    $studentCourse->grade +
                        $studentCourse->class_work_grade +
                        $studentCourse->lab_grade
                ) >= 60
            ) {
                $studentCourse->status_id = 7;
            } else {
                $studentCourse->status_id = 8;
            }
            $studentCourse->save();
        });
        return $this->admitStudentsResultsIndex($request);
    }

    public function admitStudentsResultsIndex(Request $request)
    {
        $studentsCourses = StudentCourse::whereIn("status_id", [7, 8])->get();

        // Group the collection by course ID
        $groupedCourses = $studentsCourses->groupBy("course_id");

        // Create a new collection to store the required data
        $courseData = collect();

        // Loop through each group
        foreach ($groupedCourses as $courseId => $students) {
            // Count the number of students with a success status
            $successCount = $students->where("status_id", 7)->count();

            // Count the number of students with a failure status
            $failureCount = $students->where("status_id", 8)->count();

            // Calculate the sum of the grades for the course
            $gradeSum = $students->sum("grade");
            $gradeSum += $students->sum("class_work_grade");
            $gradeSum += $students->sum("lab_grade");

            $aPlusCount = 0;
            $aCount = 0;
            $aMinusCount = 0;
            $bPlusCount = 0;
            $bCount = 0;
            $bMinusCount = 0;
            $cPlusCount = 0;
            $cCount = 0;
            $cMinusCount = 0;
            $dPlusCount = 0;
            $dCount = 0;
            $fCount = 0;

            // calculate number of each grade as following (A+: 4, A:3.9, A- : 3.7, B+:3.3, B:3, B-:2.7, C+:2.3, C:2, C-:1.7, D+:1.3, D:1, F:0)
            $students->sum(function ($student) use (
                &$aPlusCount,
                &$aCount,
                &$aMinusCount,
                &$bPlusCount,
                &$bCount,
                &$bMinusCount,
                &$cPlusCount,
                &$cCount,
                &$cMinusCount,
                &$dPlusCount,
                &$dCount,
                &$fCount
            ) {
                $gpa = $this->getGPA(
                    $student->grade +
                        $student->class_work_grade +
                        $student->lab_grade
                );
                if ($gpa == 4) {
                    $aPlusCount++;
                } elseif ($gpa >= 3.9) {
                    $aCount++;
                } elseif ($gpa >= 3.7) {
                    $aMinusCount++;
                } elseif ($gpa >= 3.3) {
                    $bPlusCount++;
                } elseif ($gpa >= 3) {
                    $bCount++;
                } elseif ($gpa >= 2.7) {
                    $bMinusCount++;
                } elseif ($gpa >= 2.3) {
                    $cPlusCount++;
                } elseif ($gpa >= 2) {
                    $cCount++;
                } elseif ($gpa >= 1.7) {
                    $cMinusCount++;
                } elseif ($gpa >= 1.3) {
                    $dPlusCount++;
                } elseif ($gpa >= 1) {
                    $dCount++;
                } else {
                    $fCount++;
                }
            });

            // Create a new entry for the course
            $courseData->push([
                "course_id" => $courseId,
                "course_name" => Course::where("id", $courseId)->first()->name,
                "success_count" => $successCount,
                "failure_count" => $failureCount,
                "total_count" => $students->count(),
                "gpa_sum" => $gradeSum,
                "a_plus_count" => $aPlusCount,
                "a_count" => $aCount,
                "a_minus_count" => $aMinusCount,
                "b_plus_count" => $bPlusCount,
                "b_count" => $bCount,
                "b_minus_count" => $bMinusCount,
                "c_plus_count" => $cPlusCount,
                "c_count" => $cCount,
                "c_minus_count" => $cMinusCount,
                "d_plus_count" => $dPlusCount,
                "d_count" => $dCount,
                "f_count" => $fCount,
            ]);
        }

        // calculate the required values for each course
        $courseData->transform(function ($item) {
            $item["success_rate"] = round(
                ($item["success_count"] / $item["total_count"]) * 100.0,
                2
            );
            $item["failure_rate"] = round(
                ($item["failure_count"] / $item["total_count"]) * 100.0,
                2
            );
            $item["avg_gpa"] = round(
                $item["gpa_sum"] / $item["total_count"],
                2
            );
            return $item;
        });

        // convert the collection to an array
        $pendingCourses = $courseData->toArray();
        if (empty($pendingCourses)) {
            session()->flash("alert", "warning");
            session()->flash("message", "You have admited all the results");
        }
        return view("admitStudentsResults", compact("pendingCourses"));
    }
    public function admitStudentsResults(Request $request)
    {
        $courseId = $request->course_id;
        $passedCourses = StudentCourse::where("course_id", $courseId)->where(
            "status_id",
            7
        );
        $failedCourses = StudentCourse::where("course_id", $courseId)->where(
            "status_id",
            8
        );

        $passedCourses->update(["status_id" => 1]);
        $failedCourses->update(["status_id" => 2]);

        dispatch(new UpdateStudentsGPAProcess());

        return $this->admitStudentsResultsIndex($request);
    }
    public function deleteStudentResults(Request $request)
    {
        $courseId = $request->course_id;

        $courses = StudentCourse::where("course_id", $courseId)->whereIn(
            "status_id",
            [7, 8]
        );
        $courses->update(["status_id" => 3]);
        return $this->admitStudentsResultsIndex($request);
    }
}
