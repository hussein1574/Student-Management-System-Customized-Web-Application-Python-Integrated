<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Constant;
use Illuminate\Http\Request;

class StudentCoursesController extends Controller
{
    public function getCurrentCourses(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found'], 404);
        }

        $courses = StudentCourse::where('student_id', $student->id)
            ->where('status_id', 3)
            ->get();
        

        return response()->json([
            'status' => 'success',
            'results' => $courses->count(),
            'data' =>[
                'courses' => $courses->map(function ($course) {
                    return [
                        'name' => $course->course->name,
                        'hours' => $course->course->hours,
                    ];
                })
            ]

        ]);
    }
    public function getFinishedCourses(Request $request, $userId)
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
    public function getGraduationHours(Request $request, $userId)
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
    public function getHoursPerYear(Request $request, $userId)
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

        $finishedHoursByYear = $this->calculateHoursPerYear($courses);

        return response()->json([
            'status' => 'success',
            'results' => count($finishedHoursByYear),
            'data' => [
                'years' => $finishedHoursByYear
            ]
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
            $finishedHoursByYear[$year] += $course->course->hours;
        }
        return $finishedHoursByYear;
    }
}
