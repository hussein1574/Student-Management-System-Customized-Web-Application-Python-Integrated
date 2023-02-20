<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function failedStudentChartData()
    {
        $courses = \App\Models\Course::all();
        $StudentCourses = \App\Models\StudentCourse::all();
        $courseFailureCount = [];
        foreach($courses as $course){
            $courseFailureCount[$course->name] = 0;
        }
        foreach($StudentCourses as $StudentCourse){
            if($StudentCourse->status_id == 2){
                $courseFailureCount[$StudentCourse->course->name] += 1;
            }
        }
        $data = [
            'labels' => array_keys($courseFailureCount),
            'datasets' => [
                [
                    'label' => 'Failed Students by Course',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
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
        foreach($courses as $course){
            $courseRegisterCount[$course->name] = 0;
        }
        foreach($StudentCourses as $StudentCourse){
            if($StudentCourse->status_id == 3){
                $courseRegisterCount[$StudentCourse->course->name] += 1;
            }
        }
        $data = [
            'labels' => array_keys($courseRegisterCount),
            'datasets' => [
                [
                    'label' => 'Registered Students by Course',
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                    'data' => array_values($courseRegisterCount),
                ]
            ]
        ];
    
        return response()->json($data);
    }
    


}