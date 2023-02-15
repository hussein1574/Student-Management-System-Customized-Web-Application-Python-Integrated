<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Course;
use App\Models\Constant;
use App\Models\StudentCourse;
use App\Models\CoursePre;
use Illuminate\Http\Request;

class CourseRegistrationController extends Controller
{
    public function register(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }
        
        $courseIds = $request->input('coursesIds');
        // Make sure that courseIds is an array
        if (!is_array($courseIds)) {
            $courseIds = [$courseIds];
        }

        $data = $this->checkCoursesHours($courseIds,$request, $userId);
        if($data == "Exceed"){
            return response()->json([
                'status' => 'fail',
                'message' => "You can't exceed the maximum hours per term",
            ],400);
        }elseif($data == "Less"){
            return response()->json([
                'status' => 'fail',
                'message' => "You must exceed the minimum hours per term ",
            ],400);}
        
        $coursesData = $this->getCoursesStatus($request, $userId);
        $coursesData = $coursesData->getData()->data->courses;
        $mustTakeCourses = [];
        $openCourses = [];
        $retakeCourses = [];
        $allMustTake = true;
        foreach ($coursesData as $course) {
            if ($course->state == 'must-take') {
                $mustTakeCourses[] = $course->courseId;
            } elseif ($course->state == 'open') {
                $openCourses[] = $course->courseId;
            }
            elseif($course->state == 'retake'){
                $retakeCourses[] = $course->courseId;
            }
        }
        foreach ($mustTakeCourses as $mustTakeCourse) {
            if (!in_array($mustTakeCourse, $courseIds)) {
                $allMustTake = false;
                break;
            }
        }
        if (!$allMustTake) {
            return response()->json([
                'status' => 'fail',
                'message' => 'One or more of the must-take courses are not in the provided courses',
            ], 400);
        }
        foreach ($courseIds as $courseId) {
            if (!in_array($courseId, $mustTakeCourses) && !in_array($courseId, $openCourses) && !in_array($courseId, $retakeCourses)) {
                return response()->json([
                    'status' => 'fail',
                    'message' => 'One or more of the provided courses are not available for the student',
                ], 400);
            }

            if(in_array($courseId, $retakeCourses)){
                $studentCourse = StudentCourse::where('student_id', $student->id)->where('course_id', $courseId)->first();
                $studentCourse->status_id = 3;
                $studentCourse->save();
            }
            else
            {
                $studentCourse = new StudentCourse();
                $studentCourse->student_id = $student->id;
                $studentCourse->course_id = $courseId;
                $studentCourse->grade = 0;
                $studentCourse->status_id = 3;
                $studentCourse->save();
            }

        }
        return response()->json([
            'status' => 'success',
            'message' => 'Courses registered successfully'
        ]);
    }
    public function checkCoursesHours($courseIds,Request $request, $userId)
    {
        $data = "OK";
        $totalHours = 0;
        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);
            $totalHours += $course->hours;
        }
        $hoursPerTerm = $this->getHoursPerTerm($request, $userId);
        $minHoursPerTerm = $hoursPerTerm->getData()->data->minHoursPerTerm;
        $maxHoursPerTerm = $hoursPerTerm->getData()->data->maxHoursPerTerm;
        if($totalHours > $maxHoursPerTerm)
        {
            
            $data = "Exceed";
        }
        elseif($totalHours < $minHoursPerTerm)
        {
           
            $data = "Less";
        }
        return $data;
    }
    public function getCoursesStatus(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }

        $courses = Course::all();
        $studentCourses = StudentCourse::where('student_id', $student->id)->get();
        $data = [];
        $maxRetakeGrade = Constant::where('name', 'maxRetakeGPA')->first()->value;
        foreach ($courses as $course) {
             $studentTakenCourse = $studentCourses->where('course_id', $course->id)->first();   
            if ($studentTakenCourse && $studentTakenCourse->status_id == 1 && $studentTakenCourse->grade >= $maxRetakeGrade) 
                continue;
            elseif($studentTakenCourse && $studentTakenCourse->grade <= $maxRetakeGrade && !$course->isClosed)
            {
                $data[] = [
                    'courseId' => $course->id,
                    'courseName' => $course->name,
                    'courseHours' => $course->hours,
                    'state' => 'retake'
                ];
                continue;
            }
            if ($course->isClosed) {
                $data[] = [
                    'courseId' => $course->id,
                    'courseName' => $course->name,
                    'courseHours' => $course->hours,
                    'state' => 'closed'
                ];
            } else {
                $isPreReqPassed = $this->checkPreReqs($course, $studentCourses);

                if ($isPreReqPassed) {
                    if ($this->isMustTake($course->id)) {
                        $data[] = [
                            'courseId' => $course->id,
                            'courseName' => $course->name,
                            'courseHours' => $course->hours,
                            'state' => 'must-take'
                        ];
                    } else {
                        $data[] = [
                            'courseId' => $course->id,
                            'courseName' => $course->name,
                            'courseHours' => $course->hours,
                            'state' => 'open'
                        ];
                    }
                } else {
                    $data[] = [
                        'courseId' => $course->id,
                        'courseName' => $course->name,
                        'courseHours' => $course->hours,
                        'state' => 'need-pre-req'
                    ];
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'results' => count($data),
            'data' => [
                'courses' => $data
            ]
        ]);

    }
    public function checkPreReqs($course, $studentCourses)
    {
        $preReqs = CoursePre::where('course_id', $course->id)->get();
                $isPreReqPassed = true;

                foreach ($preReqs as $preReq) {
                    $studentPreReq = $studentCourses->where('course_id', $preReq->coursePre_id)->first();

                    if ($preReq->passed) {
                        if (!$studentPreReq || $studentPreReq->status_id != 1) {
                            $isPreReqPassed = false;
                            break;
                        }
                    } else {
                        if (!$studentPreReq || $studentPreReq->status_id == 3) {
                            $isPreReqPassed = false;
                            break;
                        }
                    }
                }
                return $isPreReqPassed;
    }
    public function isMustTake($courseId)
    {
        $visited = [];
        $queue = [];
        array_push($queue, $courseId);

        while (!empty($queue)) {
            $vertex = array_shift($queue);
            $visited[] = $vertex;
            $openOtherCourses = DB::table('course_pres')
                ->where('coursePre_id', $vertex)
                ->get();

            foreach ($openOtherCourses as $openCourse) {
                if (!in_array($openCourse->course_id, $visited)) {
                    array_push($queue, $openCourse->course_id);
                }
            }
        }
        $openOtherCourses = DB::table('course_pres')
                        ->where('coursePre_id', $courseId)
                        ->get();
        
        return (count($visited) > $openOtherCourses->count() ? count($visited) : $openOtherCourses->count()) > 2;
    }
    public function getHoursPerTerm(Request $request, $userId)
    {
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }

        $constants = Constant::all();
        $minGPA = $constants->where('name', 'minGPA')->first()->value;
        $minHoursPerTerm = $constants->where('name', 'minHoursPerTerm')->first()->value;
        $maxHoursPerTerm = $constants->where('name', 'maxHoursPerTerm')->first()->value;
        $minHoursPerTermForMinGPA = $constants->where('name', 'minHoursPerTermForMinGPA')->first()->value;
        $maxHoursPerTermForMinGPA = $constants->where('name', 'maxHoursPerTermForMinGPA')->first()->value;

        if ($student->grade >= $minGPA) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'minHoursPerTerm' => $minHoursPerTerm,
                    'maxHoursPerTerm' => $maxHoursPerTerm
                ]
            ]);
        } else {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'minHoursPerTerm' => $minHoursPerTermForMinGPA,
                    'maxHoursPerTerm' => $maxHoursPerTermForMinGPA
                ]
            ]);
        }
    }



}
