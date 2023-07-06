<?php

namespace App\Http\Controllers;

use App\Jobs\TimetableProcess;
use App\Models\Day;
use App\Models\Hall;
use App\Models\Course;
use App\Models\Student;
use App\Models\Constant;
use App\Models\Professor;
use App\Models\Department;
use App\Models\LecturesTime;
use App\Models\ProfessorDay;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use Illuminate\Support\Carbon;
use App\Models\HallsDepartment;
use App\Models\LecturesTimeTable;
use Rap2hpoutre\FastExcel\FastExcel;

class LectureTimetableController extends Controller
{
    public function index(Request $request)
    {
        return view("generateTimetable");
    }
    public function runScript(Request $request)
    {
        dispatch(new TimetableProcess());

        return response()->json([
            "status" => "success",
            "result" => "The script is running in the background.",
        ]);
    }

    public function getTimetable(Request $request)
    {
        if (
            Constant::where("name", "Timetable Published")->first()->value == 0
        ) {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Timetable not published yet",
                ],
                422
            );
        }
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

        $studentCourses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 3)
            ->get();

        $timetable = $this->getTimetableForStudent($studentCourses);

        return response()->json([
            "status" => "success",
            "results" => count($timetable),
            "data" => [
                "days" => $timetable,
            ],
        ]);
    }
    public function getTimetableForStudent($studentCourses)
    {
        $timetable = [];
        foreach ($studentCourses as $studentCourse) {
            $courseLectures = LecturesTimeTable::where(
                "course_id",
                $studentCourse->course_id
            )->get();
            foreach ($courseLectures as $courseLecture) {
                $timetable[$courseLecture->day->name][
                    $courseLecture->lecturesTime->timePeriod
                ] = [
                    "courseName" => $studentCourse->course->name,
                    "professorName" => $courseLecture->professor
                        ? $courseLecture->professor->user->name
                        : ($courseLecture->isLab == 1
                            ? "Lab"
                            : "Section"),
                    "hallName" => $courseLecture->hall->name,
                ];
            }
        }
        return $timetable;
    }
    public function clearTimetable(Request $request)
    {
        LecturesTimeTable::truncate();
        return response()->json([
            "status" => "success",
            "message" => "Exams cleared successfully",
        ]);
    }
    public function admitTimetable(Request $request)
    {
        Constant::where("name", "Timetable Published")->update([
            "value" => true,
        ]);
        return response()->json([
            "status" => "success",
            "message" => "Timetable published successfully",
        ]);
    }
}