<?php

namespace App\Http\Controllers;


use App\Models\Course;
use League\Csv\Writer;
use App\Models\Student;
use App\Models\CoursePre;
use App\Models\Professor;
use Termwind\Components\Dd;
use Illuminate\Http\Request;
use App\Models\StudentCourse;
use App\Jobs\ResultsCsvProcess;
use App\Models\ProfessorCourse;
use Rap2hpoutre\FastExcel\FastExcel;
use App\Jobs\UpdateStudentsGPAProcess;
use Illuminate\Support\Facades\Storage;
use App\Jobs\DeleteStudentResultsProcess;
use App\Http\Controllers\CourseRegistrationController;
use OpenSpout\Writer\XLSX\MergeCell;

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
                        'level' => $course->course->level,
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
                        'level' => $course->course->level,
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
        ->where('status_id', 1)
        ->get();

        $finishedHours = 0;
        foreach ($courses as $course) {
            $finishedHours += $course->course->hours;
        }
        $department = $student->department;
        $graduationHours = $department->graduation_hours;

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
        ->where('status_id', 1)
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
                'level' => $course->course->level,
                'status' => $course->courseStatus->id,
            ];
        });
        $courseRegiserationController = new CourseRegistrationController();
        $studentCoursesStatus = $courseRegiserationController->getStudentCoursesStatus($request,$student->id);
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
                'level' => $course->course->level,
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
    #region uploadResults
    // public function uploadStudentsResultsIndex(Request $request)
    // { 
    //     $userId = backpack_user()->id;
    //     $professor = Professor::where('user_id', $userId)->first();
    //     $professorCourses = ProfessorCourse::where('professor_id', $professor->id)->get();
    //     $professorCourses = $professorCourses->map(function ($course) {
    //         $courseId = $course['id'];
    //         $coursesStatus = StudentCourse::where('course_id', $courseId)->get()->map(function ($course) {
    //             return $course->status_id;
    //         });
    //         $uploaded = true;
    //         foreach ($coursesStatus as $status) {
    //             if ($status == 3) {
    //                 $uploaded = false;
    //                 break;
    //             }
    //         }
    //         return [
    //             'name' => $course->course->name,
    //             'id' => $course->course->id,
    //             'uploaded' => $uploaded,
    //         ];
    //     });
    //     $professorCourses = $professorCourses->where('uploaded', false)->toArray();
    //     if(empty($professorCourses))
    //     {
    //         session()->flash('alert', 'warning');
    //         session()->flash('message', 'You have uploaded the results for all your courses');
    //     }

    //     return view('uploadStudentsResults', compact('professorCourses'));
    // }

    // public function uploadStudentsResults(Request $request)
    // {
    //     $courseId = $request->course_id;
    //     if (request()->has('mycsv')) {
    //         $data   =   file(request()->mycsv);

    //         $header = [];

    //         $data = array_map('str_getcsv', $data);

    //         $header = $data[0];
    //         unset($data[0]);  

          
    //         dispatch(new ResultsCsvProcess($data, $header, $courseId));

    //         return response()->json([
    //             'status' => 'success',
    //             'result' => 'The file is being processed in the background.',
    //         ]);
    //     }
    //     return response()->json([
    //         'status' => 'failed',
    //         'result' => 'Please upload a CSV file',
    //     ], 400);
    // }
    //     public function exportStudentsSheet(Request $request)
    // {
    //     $courseId = $request->course_id;
    //     $students = StudentCourse::where('course_id', $courseId)->get();
    //     $studentsIds = [];
    //     foreach ($students as $student) {
    //         $studentsIds[] = $student->student_id;
    //     }
    //     $students = Student::whereIn('id', $studentsIds)->get();
    //     $students = $students->map(function ($student) {
    //         return [
    //             'Student ID' => $student->id,
    //             'Student Name' => $student->user->name,
    //             'GPA' => null,
    //             'Status' => null
    //         ];});
        
    //      $path = storage_path('app/StudentsResults.csv');
    //     // Export the data to a CSV file
    //     (new FastExcel($students))->export($path);

    //     // Read the CSV file contents
    //     $contents = Storage::get('StudentsResults.csv');

    //     // Delete the CSV file
    //     Storage::delete('StudentsResults.csv');

    //     // Return the file as a downloadable response
    //     return response()->streamDownload(function () use ($contents) {
    //         echo $contents;
    //     }, 'StudentsResults.csv');
      
    // }
    #endregion
    
 
public function uploadStudentsResultsIndex(Request $request)
{ 
    $userId = backpack_user()->id;
    $professor = Professor::where('user_id', $userId)->first();
    $professorCourses = ProfessorCourse::where('professor_id', $professor->id)->get();
    $professorCourses = $professorCourses->map(function ($course) {
        $courseId = $course['id'];
        $coursesStatus = StudentCourse::where('course_id', $courseId)->get()->map(function ($course) {
            return $course->status_id;
        });
        $uploaded = true;
        foreach ($coursesStatus as $status) {
            if ($status == 3) {
                $uploaded = false;
                break;
            }
        }
        return [
            'name' => $course->course->name,
            'id' => $course->course->id,
            'uploaded' => $uploaded,
        ];
    });
    $professorCourses = $professorCourses->where('uploaded', false)->toArray();
    if(empty($professorCourses))
    {
        session()->flash('alert', 'warning');
        session()->flash('message', 'You have uploaded the results for all your courses');
    }

    return view('uploadStudentsResults', compact('professorCourses'));
}

public function getCourseStudents(Request $request)
{
    $courseId = $request->input('course_id');
    $students = StudentCourse::where('course_id', $courseId)->with(['student' => function($query){
        $query->with('user');
    }])->get();
    $studentsIds = [];
    foreach ($students as $student) {
        $studentsIds[] = $student->student_id;
    }
    $students = Student::whereIn('id', $studentsIds)->get();
    $students = $students->map(function ($student) use ($students) {
        $studentCourse = StudentCourse::where('student_id', $student->id)->where('course_id', $students[0]->id)->first();
        return [
            'id' => $student->id,
            'name' => $student->user->name,
            'gpa' => $studentCourse->grade
        ];
    });
    return $students;
}

    public function uploadStudentsResults(Request $request)
    {
        // Check if the Save button was clicked
        if ($request->input('save')) {
            // Perform the Save action
            $response = $this->saveStudentsResults($request);
        }
        
        // Check if the Send button was clicked
        if ($request->input('send')) {
            // Perform the Send action
            $response = $this->sendStudentsResults($request);
            redirect()->back();
        }
        
        // Redirect back to the form
        return $response;
    }

    public function saveStudentsResults(Request $request)
    {
        $courseId = $request->input('course_id');
        foreach ($request->gpa as $id => $gpa) {
            $results[] = [
                'id' => $id,
                'gpa' => $gpa,
            ];
        
        }

        foreach ($results as $result) {
            $studentId = $result['id'];
            $gpa = $result['gpa'];
            
            $studentCourse = StudentCourse::where('course_id', $courseId)
                                        ->where('student_id', $studentId)
                                        ->first();
            $studentCourse->grade = $gpa;
            $studentCourse->save();
        }
        
        return response()->json(['status' => 'success',
        'result' => 'The grades are saved successfully.']);
    }
    public function sendStudentsResults(Request $request)
    {
        $courseId = $request->input('course_id');
        foreach ($request->gpa as $id => $gpa) {
            $results[] = [
                'id' => $id,
                'gpa' => $gpa,
            ];
        
        }

        foreach ($results as $result) {
            $studentId = $result['id'];
            $gpa = $result['gpa'];
            
            $studentCourse = StudentCourse::where('course_id', $courseId)
                                        ->where('student_id', $studentId)
                                        ->first();
            $studentCourse->grade = $gpa;
            if($gpa > 2)
                $studentCourse->status_id = 7;
            else
                $studentCourse->status_id = 8;
            $studentCourse->save();
        }
        
        return response()->json(['status' => 'success',
        'result' => 'The grades are sent successfully.']);
    }

    public function improveGrades(Request $request)
    {
        $studentCourses = StudentCourse::where('course_id', $request->course_id)->whereIn('status_id', [7, 8])->get();
        $studentCourses->each(function ($studentCourse) use ($request) {
            if($studentCourse->grade + $request->grade_value <= 4)
                $studentCourse->grade += $request->grade_value;
            if($studentCourse->grade >= 2)
                $studentCourse->status_id = 7;
            else
                $studentCourse->status_id = 8;
            $studentCourse->save();
        });
        return $this->admitStudentsResultsIndex($request);
    }

    public function admitStudentsResultsIndex(Request $request)
    {
        
        $studentsCourses = StudentCourse::whereIn('status_id', [7, 8])->get();

        // Group the collection by course ID
        $groupedCourses = $studentsCourses->groupBy('course_id');

        // Create a new collection to store the required data
        $courseData = collect();

        // Loop through each group
        foreach ($groupedCourses as $courseId => $students) {

            // Count the number of students with a success status
            $successCount = $students->where('status_id', 7)->count();

            // Count the number of students with a failure status
            $failureCount = $students->where('status_id', 8)->count();

            // Calculate the sum of the grades for the course
            $gradeSum = $students->sum('grade');

            $aPlusCount = 0;
            $aCount = 0;
            $aMinusCount = 0;
            $bPlusCount = 0;
            $bCount = 0;
            $bMinusCount = 0;
            $cPlusCount = 0;
            $cCount = 0;
            $cMinusCount = 0;
            $dPlusCount = 0;
            $dCount = 0;
            $fCount = 0;

            // calculate number of each grade as following (A+: 4, A:3.9, A- : 3.7, B+:3.3, B:3, B-:2.7, C+:2.3, C:2, C-:1.7, D+:1.3, D:1, F:0)
            $students->sum(function ($student) use(&$aPlusCount, &$aCount, &$aMinusCount, &$bPlusCount, &$bCount, &$bMinusCount, &$cPlusCount, &$cCount, &$cMinusCount, &$dPlusCount, &$dCount, &$fCount) {
                if($student->grade == 4)
                    $aPlusCount++;
                else if($student->grade >= 3.9)
                    $aCount++;
                else if($student->grade >= 3.7)
                    $aMinusCount++;
                else if($student->grade >= 3.3)
                    $bPlusCount++;
                else if($student->grade >= 3)
                    $bCount++;
                else if($student->grade >= 2.7)
                    $bMinusCount++;
                else if($student->grade >= 2.3)
                    $cPlusCount++;
                else if($student->grade >= 2)
                    $cCount++;
                else if($student->grade >= 1.7)
                    $cMinusCount++;
                else if($student->grade >= 1.3)
                    $dPlusCount++;
                else if($student->grade >= 1)
                    $dCount++;
                else
                    $fCount++;
             });


            // Create a new entry for the course
            $courseData->push([
                'course_id' => $courseId,
                'course_name' => Course::where('id', $courseId)->first()->name,
                'success_count' => $successCount,
                'failure_count' => $failureCount,
                'total_count' => $students->count(),
                'gpa_sum' => $gradeSum,
                'a_plus_count' => $aPlusCount,
                'a_count' => $aCount,
                'a_minus_count' => $aMinusCount,
                'b_plus_count' => $bPlusCount,
                'b_count' => $bCount,
                'b_minus_count' => $bMinusCount,
                'c_plus_count' => $cPlusCount,
                'c_count' => $cCount,
                'c_minus_count' => $cMinusCount,
                'd_plus_count' => $dPlusCount,
                'd_count' => $dCount,
                'f_count' => $fCount,
            ]);
        }

        

        // calculate the required values for each course
        $courseData->transform(function ($item) {
            $item['success_rate'] = round(($item['success_count'] / $item['total_count']) * 100.0,2);
            $item['failure_rate'] = round(($item['failure_count'] / $item['total_count']) * 100.0,2);
            $item['avg_gpa'] = round($item['gpa_sum'] / $item['total_count'],2);
            return $item;
        });

        // convert the collection to an array
        $pendingCourses = $courseData->toArray();
        if(empty($pendingCourses))
        {
            session()->flash('alert', 'warning');
            session()->flash('message', 'You have admited all the results');
        }
        return view('admitStudentsResults', compact('pendingCourses'));
    }
    public function admitStudentsResults(Request $request)
    {
        $courseId = $request->course_id;
        $passedCourses = StudentCourse::where('course_id', $courseId)->where('status_id', 7);
        $failedCourses = StudentCourse::where('course_id', $courseId)->where('status_id', 8);
             
        $passedCourses->update(['status_id' => 1]);
        $failedCourses->update(['status_id' => 2]);

        dispatch(new UpdateStudentsGPAProcess());
        
        return $this->admitStudentsResultsIndex($request);
    }
    public function deleteStudentResults(Request $request)
    {
        $courseId = $request->course_id;

        $courses = StudentCourse::where('course_id', $courseId)->whereIn('status_id', [7, 8]);
        $courses->update(['status_id' => 3]);
        return $this->admitStudentsResultsIndex($request);
    }
}