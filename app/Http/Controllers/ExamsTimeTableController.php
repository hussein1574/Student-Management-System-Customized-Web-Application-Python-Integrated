<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Jobs\ExamTimetableJob;
use App\Models\ExamsTimeTable;
use App\Jobs\ExamTimetableProcess;
use Symfony\Component\Process\Process;
use Illuminate\Validation\ValidationException;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ExamsTimeTableController extends Controller
{
    public function index(Request $request)
    {
        return view('generateExams');
    }
    public function runScript(Request $request)
    {
        try {
            $request->validate([
                'maxStds' => ['required', 'int'],
                'maxRooms' => ['required', 'int'],
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
        
        dispatch(new ExamTimetableProcess($request->maxStds,$request->maxRooms));
        
        return response()->json([
        'status' => 'success',
        'result' => 'The script is running in the background.',
    ]);
    }
    public function getExams(Request $request)
    {
        $userId = $request->user()->id;
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