<?php

namespace App\Http\Controllers;

use App\Models\ExamsTimeTable;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class ExamsTimeTableController extends Controller
{
    public function getExams(Request $request, $userId)
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

        $exams = $this->getExamsForStudent($studentCourses);

        return response()->json([
            'status' => 'success',
            'results' => count($exams),
            'data' => [ 
                'exams' => $exams
                ]
        ]);

    }
    public function getExamsForStudent($studentCourses){
        $exams = [];
        foreach ($studentCourses as $studentCourse) {
            $courseExam = ExamsTimeTable::where('course_id', $studentCourse->course_id)->first();
            if ($courseExam)
            {
                $exams[] = [
                    'hallName' => $courseExam->hall->name,
                    'courseName' => $studentCourse->course->name,
                    'day' => $courseExam->day,
                ];
            }            
        }
        return $exams;
    }
}
