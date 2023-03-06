<?php

namespace App\Http\Controllers;


use App\Models\Course;
use App\Models\Student;
use App\Models\Constant;
use App\Models\CoursePre;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Http\Controllers\CourseRegistrationController;
use Termwind\Components\Dd;

class StudentCoursesController extends Controller
{
    public function getCurrentCourses(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found '], 404);
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
                        'level' => $course->level,
                    ];
                })
            ]

        ]);
    }
    public function getFinishedCourses(Request $request)
    {
        $userId = $request->user()->id;
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
                        'level' => $course->level,
                        'status' => $course->courseStatus->status,
                    ];
                })
            ]

        ]);
    }
    public function getGraduationHours(Request $request)
    {
        $userId = $request->user()->id;
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

        $graduationHours = Constant::where('name', 'Graduation Hours')->first()->value;

        return response()->json([
            'status' => 'success',
            'results' => 2,
            'data' =>[
                'finishedHours' => $finishedHours,
                'graduationHours' => $graduationHours
            ]
        ]);

    }
    public function getHoursForFinishedYears(Request $request)
    {
        $userId = $request->user()->id;
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

    public function getStudentCourses(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found '], 404);
        }
        $pendingCourses = StudentCourse::where('student_id', $student->id)
        ->where('status_id', 4)
        ->get();
        $finishedCoursesObject = StudentCourse::where('student_id', $student->id)
        ->whereIn('status_id', [1, 2])
        ->get();
         
        $finishedCourses = $finishedCoursesObject->map(function ($course) {
            return [
                'name' => $course->course->name,
                'hours' => $course->course->hours,
                'grade' => $course->grade,
                'level' => $course->level,
                'status' => $course->courseStatus->id,
            ];
        });
        $courseRegiserationController = new CourseRegistrationController();
        $studentCoursesStatus = $courseRegiserationController->getStudentCoursesStatus($student->id);
        usort($studentCoursesStatus, function($a, $b) {
            return $a['level'] <=> $b['level'];
        });
        return view('student-courses.index', compact('pendingCourses','finishedCourses','studentCoursesStatus','studentId'));
    }
    public function admitStudentCourses(Request $request,$studentId)
    {
        $student = Student::where('id', $studentId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found '], 404);
        }

        $pendingCourses = StudentCourse::where('student_id', $student->id)
        ->where('status_id', 4)
        ->get();
        foreach ($pendingCourses as $course) {
            $course->status_id = 3;
            $course->save();
        }
        
        header('Location: ' . backpack_url('student'));
        exit();
    }

    public function showStudentCourse(Request $request, $studentId)
    {
        $student = Student::where('id', $studentId)->first();
        if (!$student) {
            return response()->json([
                'status'=> 'fail',
                'message' => 'Student not found '], 404);
        }
        $finishedCoursesObject = StudentCourse::where('student_id', $student->id)
        ->whereIn('status_id', [1, 2])
        ->get();
         
        $finishedCourses = $finishedCoursesObject->map(function ($course) {
            return [
                'name' => $course->course->name,
                'hours' => $course->course->hours,
                'grade' => $course->grade,
                'level' => $course->level,
                'status' => $course->courseStatus->id,
            ];
        });
        $courseRegiserationController = new CourseRegistrationController();
        $studentCoursesStatus = $courseRegiserationController->getStudentCoursesStatus($student->id);
        $coursesPreRequists = CoursePre::all();
        usort($studentCoursesStatus, function($a, $b) {
            return $a['level'] <=> $b['level'];
        });
        $preCourses = $coursesPreRequists->map(function ($course) {
            return [
                'course_id' => $course->course_id,
                'coursePre' => Course::where('id', $course->coursePre_id)->first()->name,
                'passed' => $course->passed,
            ];
        });
       // dd($preCourses);
        return view('student-courses.add', compact('finishedCourses','preCourses', 'studentCoursesStatus','studentId'));
    }

    public function registerStudentCourse(Request $request)
    {
        $studentId = $request->student_id;
        $courseId = $request->course_id;

        if(StudentCourse::where('student_id', $studentId)->where('course_id', $courseId)->exists()){
            $studentCourse = StudentCourse::where('student_id', $studentId)->where('course_id', $courseId)->first();
            $studentCourse->status_id = 4;
            $studentCourse->save();
        }
        else{
            $studentCourse = new StudentCourse();
            $studentCourse->student_id = $studentId;
            $studentCourse->course_id = $courseId;
            $studentCourse->grade = 0;
            $studentCourse->status_id = 4;
            $studentCourse->save();
        }

        return $this->getStudentCourses($request, $studentId);
    }

    public function deleteStudentCourse(Request $request)
    {
        $studentId = $request->student_id;
        $courseId = $request->course_id;
        StudentCourse::where('student_id', $studentId)->where('course_id', $courseId)->delete();

        return $this->getStudentCourses($request, $studentId);
        
    }

    public function hasPendingCourses($studentId)
    {
        $pendingCourses = StudentCourse::where('student_id', $studentId)
        ->where('status_id', 4)
        ->get();
        if ($pendingCourses->count() > 0) {
            return true;
        }
        return false;
    }
}