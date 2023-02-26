<?php

namespace App\Http\Controllers;

use App\Models\LecturesTimeTable;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class LectureTimetableController extends Controller
{
    public function getTimetable(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }

        $studentCourses = StudentCourse::where('student_id', $student->id)
            ->where('status_id', 3)
            ->get();

        $timetable = $this->getTimetableForStudent($studentCourses);

        return response()->json([
            'status' => 'success',
            'results' => count($timetable),
            'data' => [
                'days' => $timetable
            ]
        ]);
    }
    public function getTimetableForStudent($studentCourses)
    {
        $timetable = [];
        foreach ($studentCourses as $studentCourse) {
            $courseLectures = LecturesTimeTable::where('course_id', $studentCourse->course_id)->get();
            foreach ($courseLectures as $courseLecture) {
                $timetable[$courseLecture->day->name][$courseLecture->lecturesTime->timePeriod] = [
                    'courseName' => $studentCourse->course->name,
                    'hallName' => $courseLecture->hall->name
                ];
            }
        }
        return $timetable;
    }
}