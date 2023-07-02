<?php

namespace App\Http\Controllers;

use DateTime;
use DateInterval;
use Carbon\Carbon;
use App\Models\Hall;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Jobs\ExamTimetableJob;
use App\Models\ExamsTimeTable;
use App\Jobs\ExamTimetableProcess;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Symfony\Component\Process\Process;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use App\Models\Constant;

class ExamsTimeTableController extends Controller
{
    public function index(Request $request)
    {
        return view("generateExams");
    }
    public function runScript(Request $request)
    {
        $examsStartDate = $request->input("examsStartDate");
        if ($examsStartDate == null) {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Validation failed",
                    "errors" => "Empty exams start date",
                ],
                422
            );
        }

        dispatch(new ExamTimetableProcess($examsStartDate));

        return response()->json([
            "status" => "success",
            "result" => "The script is running in the background.",
        ]);
    }
    public function getExams(Request $request)
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
        if (
            Constant::where("name", "ExamTimetable Published")->first()
                ->value == 0
        ) {
            return response()->json(
                [
                    "status" => "failed",
                    "message" => "Exams Timetable not published yet",
                ],
                422
            );
        }

        $studentCourses = StudentCourse::where("student_id", $student->id)
            ->where("status_id", 3)
            ->get();

        $exams = $this->getExamsForStudent($studentCourses);

        dd($exams);

        return response()->json([
            "status" => "success",
            "results" => count($exams),
            "data" => [
                "exams" => $exams,
            ],
        ]);
    }
    public function getExamsForStudent($studentCourses)
    {
        $exams = [];
        foreach ($studentCourses as $studentCourse) {
            $courseExam = ExamsTimeTable::where(
                "course_id",
                $studentCourse->course_id
            )->first();
            if ($courseExam) {
                $exams[] = [
                    "hallName" => $courseExam->hall->name,
                    "courseName" => $studentCourse->course->name,
                    "day" => $courseExam->day,
                ];
            }
        }
        return $exams;
    }
    public function clearExams(Request $request)
    {
        ExamsTimeTable::truncate();
        return response()->json([
            "status" => "success",
            "message" => "Exams cleared successfully",
        ]);
    }
    public function admitExams(Request $request)
    {
        Constant::where("name", "ExamTimetable Published")->update([
            "value" => true,
        ]);
        return response()->json([
            "status" => "success",
            "message" => "Exams admitted successfully",
        ]);
    }
}
