<?php
namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Student;
use App\Models\Constant;
use App\Models\CoursePre;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Jobs\RegisterCourseJob;
use App\Models\DepartmentCourse;
use Illuminate\Support\Facades\DB;

class CourseRegistrationController extends Controller
{
    public function getCoursesStatus(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }

        $data = $this->getStudentCoursesStatus($student->id);

        return response()->json([
            'status' => 'success',
            'results' => count($data),
            'data' => [
            'courses' => $data
            ]
        ]);

    }

    public function getStudentCoursesStatus($studentId)
    {   
        $studentDepartment = Student::where('id', $studentId)->first()->department_id;
        //get all courses that the student department offers
        $departmentCourses = DepartmentCourse::where('department_id', $studentDepartment)->get();
        $courses = [];
        foreach ($departmentCourses as $departmentCourse) {
            $courses[] = Course::where('id', $departmentCourse->course_id)->first();
        }
        $studentCourses = StudentCourse::where('student_id', $studentId)->get();
        $data = [];
        $maxRetakeGrade = Constant::where('name', 'Max GPA to retake a course')->first()->value;
        foreach ($courses as $course) {
             $studentTakenCourse = $studentCourses->where('course_id', $course->id)->first();   
            if ($studentTakenCourse && $studentTakenCourse->status_id == 1 && $studentTakenCourse->grade > $maxRetakeGrade) 
                continue;
            elseif($studentTakenCourse && ($studentTakenCourse->status_id == 3 || $studentTakenCourse->status_id == 4 ))
                continue;
            elseif($studentTakenCourse && $studentTakenCourse->status_id == 1 && $studentTakenCourse->grade <= $maxRetakeGrade && !$course->isClosed)
            {
                $data[] = [
                    'courseId' => $course->id,
                    'courseName' => $course->name,
                    'courseHours' => $course->hours,
                    'level' => $course->level,
                    'elective' => $course->isElective,
                    'state' => 'retake'
                ];
                continue;
            }
            if ($course->isClosed) {
                $data[] = [
                    'courseId' => $course->id,
                    'courseName' => $course->name,
                    'courseHours' => $course->hours,
                    'level' => $course->level,
                    'elective' => $course->isElective,
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
                            'level' => $course->level,
                            'elective' => $course->isElective,
                            'state' => 'must-take'
                        ];
                    } else {
                        $data[] = [
                            'courseId' => $course->id,
                            'courseName' => $course->name,
                            'courseHours' => $course->hours,
                            'level' => $course->level,
                            'elective' => $course->isElective,
                            'state' => 'open'
                        ];
                    }
                } else {
                    $data[] = [
                        'courseId' => $course->id,
                        'courseName' => $course->name,
                        'courseHours' => $course->hours,
                        'level' => $course->level,
                        'elective' => $course->isElective,
                        'state' => 'need-pre-req'
                    ];
                }
            }
        }
        return $data;
    }
    
    public function register(Request $request)
    {
        $userId = $request->user()->id;
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

            dispatch(new RegisterCourseJob($courseId, $student->id,$retakeCourses));
            

        }
        return response()->json([
            'status' => 'success',
            'message' => 'Courses registered successfully'
        ]);
    }
    public function checkCoursesHours($courseIds,Request $request)
    {
        $data = "";
        $totalHours = 0;
        foreach ($courseIds as $courseId) {
            $course = Course::find($courseId);
            $totalHours += $course->hours;
        }
        $hoursPerTerm = $this->getHoursPerTerm($request);
        $minHoursPerTerm = $hoursPerTerm->getData()->data->minHoursPerTerm;
        $maxHoursPerTerm = $hoursPerTerm->getData()->data->maxHoursPerTerm;
        if($totalHours > $maxHoursPerTerm)
            $data = "Exceed";
        elseif($totalHours < $minHoursPerTerm) 
            $data = "Less";

        return $data;
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
                        if (!$studentPreReq || $studentPreReq->status_id == 3 || $studentPreReq->status_id == 4) {
                            $isPreReqPassed = false;
                            break;
                        }
                    }
                }
                return $isPreReqPassed;
    }
    public function isMustTake($courseId)
    {
        $minGraphLength = Constant::where('name', 'no. a course opens to be must')->first()->value;
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
        return count($visited) > $minGraphLength;
    }
    public function getHoursPerTerm(Request $request)
    {
        $userId = $request->user()->id;
        $student = Student::where('user_id', $userId)->first();
        if (!$student) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Student not found'
            ], 404);
        }

        $constants = Constant::all();
        $highGPA = $constants->where('name', 'High GPA')->first()->value;
        $lowGPA = $constants->where('name', 'Low GPA')->first()->value;
        $minHoursPerTerm = $constants->where('name', 'Min Hours Per Term')->first()->value;
        $maxHoursPerTermForHighGpa = $constants->where('name', 'Max Hours Per Term For High GPA')->first()->value;
        $maxHoursPerTermForAverageGpa = $constants->where('name', 'Max Hours Per Term For Avg GPA')->first()->value;
        $maxHoursPerTermForLowGpa = $constants->where('name', 'Max Hours Per Term For Low GPA')->first()->value;

        $studentLevel = $this->getStudentLevel($student->id);
        if ($student->grade >= $highGPA ) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'minHoursPerTerm' => $minHoursPerTerm,
                    'maxHoursPerTerm' => $maxHoursPerTermForHighGpa
                ]
            ]);
        } elseif(($student->grade >= $lowGPA && $student->grade < $highGPA) || $studentLevel == 0) {
            return response()->json([
                'status' => 'success',
                'data' => [
                    'minHoursPerTerm' => $minHoursPerTerm,
                    'maxHoursPerTerm' => $maxHoursPerTermForAverageGpa
                ]
            ]);
        }
        else
            return response()->json([
                'status' => 'success',
                'data' => [
                    'minHoursPerTerm' => $minHoursPerTerm,
                    'maxHoursPerTerm' => $maxHoursPerTermForLowGpa
                ]
            ]);
    }

    public function getStudentLevel($studentId)
    {
        $studentPassedCourses = StudentCourse::where('student_id', $studentId)
            ->where('status_id', 1)
            ->get();
        $hours = 0;
        $level = 0;
        $graduationsHours = Constant::where('name', 'Graduation Hours')->first()->value;
        foreach($studentPassedCourses as $studentPassedCourse){
            $course = Course::find($studentPassedCourse->course_id);
            $hours += $course->hours;
        }
        if(0 <= $hours && $hours <=  $graduationsHours * 0.225)
            $level = 0;
        elseif($graduationsHours * 0.225 < $hours && $hours <= $graduationsHours * 0.41875)
            $level = 1;
        elseif($graduationsHours * 0.41875 < $hours && $hours <= $graduationsHours * 0.6)
            $level = 2;
        elseif($graduationsHours * 0.6 < $hours && $hours <= $graduationsHours * 0.8)
            $level = 3;
        elseif($graduationsHours * 0.8 < $hours && $hours <= $graduationsHours)
            $level = 4;
            
        return $level;
    }



}