<?php

namespace App\Http\Controllers;

use App\Models\Constant;
use App\Models\Student;
use App\Models\StudentCourse;
use Illuminate\Http\Request;

class GraduationController extends Controller
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

        $finishedHours = 0;
        foreach ($courses as $course) {
            $finishedHours += $course->course->hours;
        }

        $graduationHours = Constant::where('name', 'graduationHours')->first()->value;

        return response()->json([
            'status' => 'success',
            'results' => 2,
            'data' =>[
                'finishedHours' => $finishedHours,
                'graduationHours' => $graduationHours
            ]
        ]);

    }
}
