<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CoursePre;
use App\Models\Department;
use Illuminate\Http\Request;
use App\Jobs\ProgramCsvProcess;
use App\Models\DepartmentCourse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Bus;

class UploadProgram extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::all();
        $regulations = $departments->map(function ($department) {
            return [
                "id" => $department->id,
                "name" => $department->name,
            ];
        });
        return view("uploadProgram", compact("regulations"));
    }
    public function upload(Request $request)
    {
        // dd(request()->has('mycsv'));
        if (request()->has("mycsv")) {
            $data = file(request()->mycsv);

            $header = [];

            $data = array_map("str_getcsv", $data);

            $header = $data[0];
            unset($data[0]);

            dispatch(new ProgramCsvProcess($data, $header));

            return response()->json([
                "status" => "success",
                "result" => "The file is being processed in the background.",
            ]);
        }
        return response()->json(
            [
                "status" => "failed",
                "result" => "Please upload a CSV file",
            ],
            400
        );
    }
    public function delete(Request $request)
    {
        $departmentCourses = DepartmentCourse::where(
            "department_id",
            $request->regulation_id
        )->get();
        $departmentCourses->each(function ($departmentCourse) {
            $departmentCourse->delete();
        });
        return response()->json([
            "status" => "success",
            "result" => "The program is deleted successfully.",
        ]);
    }
}
