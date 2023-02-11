<?php

namespace App\Http\Controllers;

use App\Models\ExamsTimeTable;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class ExamsController extends Controller
{
    public function index(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found'], 404);
        }
        
        $studentCourses = StudentCourse::where('student_id', $student->id)
        ->where('status_id', 3)
        ->get();

        $exams = [];
        foreach ($studentCourses as $studentCourse) {
            $courseExams = ExamsTimeTable::where('course_id', $studentCourse->course_id)->get();
            foreach ($courseExams as $courseExam) {
                $exams[] = [
                    'HallName' => $courseExam->hall->name,
                    'CourseName' => $studentCourse->course->name,
                    'day' => $courseExam->day,
                ];
            }
        }

        return response()->json([
            'status' => 'success',
            'results' => count($exams),
            'data' => [ 
                'exams' => $exams
                ]
        ]);

    }
}
