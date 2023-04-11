<?php

namespace App\Http\Controllers\Admin;

use App\Models\Constant;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function failedStudentChartData()
    {
        $courses = \App\Models\Course::all();
        $year = date('Y'); // get current year
        $StudentCourses = \App\Models\StudentCourse::whereYear('updated_at', '=', $year)->get();

        $courseFailureCount = [];
        foreach ($courses as $course) {
            $courseFailureCount[$course->name] = 0;
        }
        foreach ($StudentCourses as $StudentCourse) {
            if ($StudentCourse->status_id == 2) {
                $courseFailureCount[$StudentCourse->course->name] += 1;
            }
        }
        $data = [
            'labels' => array_keys($courseFailureCount),
            'datasets' => [
                [
                    'label' => 'Failed Students by Course',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    // 'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => array_values($courseFailureCount),
                ]
            ]
        ];

        return response()->json($data);
    }
    public function registeredStudentChartData()
    {
        $courses = \App\Models\Course::all();
        $StudentCourses = \App\Models\StudentCourse::all();
        $courseRegisterCount = [];
        foreach ($courses as $course) {
            $courseRegisterCount[$course->name] = 0;
        }
        foreach ($StudentCourses as $StudentCourse) {
            if ($StudentCourse->status_id == 4) {
                $courseRegisterCount[$StudentCourse->course->name] += 1;
            }
        }
        $data = [
            'labels' => array_keys($courseRegisterCount),
            'datasets' => [
                [
                    'label' => 'Registered Students by Course',
                    'backgroundColor' => 'rgba(34, 223, 71)',
                    // 'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => array_values($courseRegisterCount),
                ]
            ]
        ];

        return response()->json($data);
    }
    public function clearStudentsRegistration()
    {
            dispatch(new \App\Jobs\ClearStudentRegistrationProcess());
            sleep(2);
            return redirect()->back()->with('success', 'Registration Process Cleared Successfully');
    }
    public function changeRegistrationState(){
        $registrationState = Constant::where('name', 'Regestration Opened')->first();
        if($registrationState->value == 0){
            $registrationState->value = 1;
        }else{
            $registrationState->value = 0;
        }
        $registrationState->save();
        return redirect()->back();
    }
}