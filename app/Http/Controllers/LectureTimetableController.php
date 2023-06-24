<?php

namespace App\Http\Controllers;

use App\Models\LecturesTimeTable;
use App\Models\Student;
use App\Models\StudentCourse;
use App\Models\Course;
use App\Models\Hall;
use App\Models\Department;
use App\Models\HallsDepartment;
use App\Models\ProfessorDay;
use App\Models\Professor;
use Illuminate\Http\Request;
use App\Models\Constant;
use Rap2hpoutre\FastExcel\FastExcel;

class LectureTimetableController extends Controller
{
        public function index(Request $request)
    {
        return view('generateExams');
    }
    public function runScript(Request $request)
    {
        $this->createHallsFile();
        $this->createProffFile();
        $this->createSubjectsFile();
        $this->createProffTimesFile();
        
        // dispatch(new ExamTimetableProcess($examsStartDate));
        
        return response()->json([
        'status' => 'success',
        'result' => 'The script is running in the background.',
    ]);
    }
    public function createHallsFile() {
        // Retrieve all halls with their corresponding department
        $halls = Hall::with('hallsDepartments')->get();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($halls as $hall) {
            if($hall->is_active == false)
                continue;
            $hallName = $hall->name;
            $hallCapacity = $hall->capacity;
            $hallDepartment = '';
            if(count($hall->hallsDepartments) > 1)
            {
                foreach($hall->hallsDepartments as $hallsDepartment)
                {
                    if($hallDepartment == '')
                    {
                        $hallDepartment = Department::where('id', $hallsDepartment->department_id)->get()[0]->name;
                    }       
                    else
                        $hallDepartment .= ',' . Department::where('id', $hallsDepartment->department_id)->get()[0]->name;
                }
            }else {
                if(count($hall->hallsDepartments) == 1)
                {
                    $hallDepartment = Department::where('id', $hall->hallsDepartments[0]->department_id)->get()[0]->name;
                }    
                else
                    $hallDepartment = '';
            }

                // If the course code does not exist, add a new entry to the export data array
                $exportData[] = [
                    'name' => $hallName,
                    'capacity' => $hallCapacity,
                    'department' => $hallDepartment,
                ];  
        }
        (new FastExcel($exportData))->export(base_path('app\scripts\lectures\halls.xlsx'));    
    }
    public function createProffFile()
    {
        // Retrieve all courses with their corresponding professors
        $courses = Course::with('professorCourses')->get();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($courses as $course) {
            $courseCode = $course->code;
            $professorNames = '';
            if(count($course->professorCourses) > 1)
            {
                foreach($course->professorCourses as $professorCourse)
                {
                    if($professorNames == '')
                    {
                        $professorNames = Professor::where('id', $professorCourse->professor_id)->get()[0]->user->name;
                    }       
                    else
                        $professorNames .= ',' . Professor::where('id', $professorCourse->professor_id)->get()[0]->user->name;
                }
            }else {
                if(count($course->professorCourses) == 1)
                    $professorNames = Professor::where('id', $course->professorCourses[0]->professor_id)->get()[0]->user->name;
                else
                    $professorNames = '';
            }

                // If the course code does not exist, add a new entry to the export data array
                $exportData[] = [
                    'subject' => $courseCode,
                    'prof' => $professorNames,
                ];  

        }
        (new FastExcel($exportData))->export(base_path('app\scripts\lectures\Profs.xlsx'));    
    }
    public function createSubjectsFile()
    {
        // Retrieve all courses with their corresponding professors
        $courses = Course::with('professorCourses')->get();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($courses as $course) {
            $courseCode = $course->code;
            $courseSection = $course->sectionHours == 0 ? 0 : 1;
            $courseLab = $course->labHours == 0 ? 0 : 1;
            $courseLectureTime = $course->LectureHours;
            $courseSectionTime = $course->sectionHours;
            $courseLabTime = $course->labHours;

            $departments = '';
            if(count($course->departmentCourses) > 1)
            {
                foreach($course->departmentCourses as $departmentCourses)
                {
                    if($departments == '')
                    {
                        $departments = Department::where('id', $departmentCourses->department_id)->get()[0]->name;
                    }       
                    else
                        $departments .= ',' . Professor::where('id',$departmentCourses->department_id)->get()[0]->name;
                }
            }else {
                if(count($course->departmentCourses) == 1)
                {
                    $departments = Department::where('id', $course->departmentCourses[0]->department_id)->get()[0]->name;
                }
                else
                    $departments = '';
            }

                // If the course code does not exist, add a new entry to the export data array
                $exportData[] = [
                    'subject' => $courseCode,
                    'sec' => $courseSection,
                    'lab' => $courseLab,
                    'lecTime' => $courseLectureTime,
                    'secTime' => $courseSectionTime,
                    'labTime' => $courseLabTime,
                    'department' => $departments,
                ];  

        }
        (new FastExcel($exportData))->export(base_path('app\scripts\lectures\subjects.xlsx'));    
    }
        public function createProffTimesFile()
    {
        $professors = Professor::all();
        $professorsTimes = ProfessorDay::all();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($professorsTimes as $professorsTime) {
            $professorName = $professorsTime->professor->user->name;
            $professorDayPeriod = $professorsTime->day->name . '-' . $professorsTime->period->timePeriod;
            // check if the professor name exists in the array
            $professorExists = false;
            foreach($exportData as $data)
            {
                if($data['Profs'] == $professorName)
                {
                    
                    $professorExists = true;
                    $data['periods'] .= ',' . $professorDayPeriod;
                }
            }
            if(!$professorExists)
            {
                // If the course code does not exist, add a new entry to the export data array
                $exportData[] = [
                    'Profs' => $professorName,
                    'periods' => $professorDayPeriod,
                ];  
            }

        }
        dd($exportData);
        (new FastExcel($exportData))->export(base_path('app\scripts\lectures\Profs Time.xlsx'));    
    }
    
    public function getTimetable(Request $request)
    {
        if(Constant::where('name', 'Timetable Published')->first()->value == 0)
        {
            return response()->json([
                'status' => 'failed',
                'message' => 'Timetable not published yet',
            ], 422);
        }
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