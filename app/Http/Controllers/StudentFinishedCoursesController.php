<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class StudentFinishedCoursesController extends Controller
{
    public function index(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found'], 404);
        }

        $courses = StudentCourse::where('student_id', $student->id)
        ->whereIn('status_id', [1, 2])
        ->get();
        

        return response()->json([
            'status' => 'success',
            'results' => $courses->count(),
            'data' =>[
                'courses' => $courses->map(function ($course) {
                    return [
                        'name' => $course->course->name,
                        'hours' => $course->course->hours,
                        'grade' => $course->grade,
                        'status' => $course->courseStatus->status,
                    ];
                })
            ]

        ]);
    }
}
