<?php

namespace App\Jobs;

use Throwable;
use App\Models\Course;
use App\Models\Constant;
use App\Models\CoursePre;
use App\Models\Department;
use Illuminate\Bus\Queueable;
use App\Models\DepartmentCourse;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ProgramCsvProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $header;
    public $timeout = 0;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header)
    {
        $this->data = $data;
        $this->header = $header;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //added the course to the database
        $courseHeader = array_slice($this->header, 0, -2);
        foreach ($this->data as $course) {
            $courseData = array_slice($course, 0, -2);
            $row = array_combine($courseHeader, $courseData);
            $courseExist = Course::where("code", trim($courseData[0]))->first();
            if ($courseExist) {
                continue;
            }
            Course::create([
                "code" => trim($courseData[0]),
                "name" => $row["name"],
                "LectureHours" => $row["LectureHours"],
                "isElective" => $row["isElective"],
                "level" => $row["level"],
                "labHours" => $row["labHours"],
                "sectionHours" => $row["sectionHours"],
            ]);
        }

        //added the course department to the database
        foreach ($this->data as $course) {
            $courseCode = $course[0];
            $departmentsNames = $course[count($course) - 2];
            if ($departmentsNames == null || $departmentsNames == "") {
                continue;
            }
            $departmentsArray = explode(",", $departmentsNames);
            $courseCode = trim($courseCode);
            $course = Course::where("code", $courseCode)->first();
            foreach ($departmentsArray as $departmentName) {
                $departmentName = trim($departmentName);
                $department = Department::where(
                    "name",
                    $departmentName
                )->first();
                if ($course[1] == "Graduation Project") {
                    $department->graduation_project_needed_hours =
                        $course[count($course) - 1];
                    $department->save();
                }
                DepartmentCourse::create([
                    "course_id" => $course->id,
                    "department_id" => $department->id,
                ]);
            }
        }

        //added the course pre to the database
        foreach ($this->data as $course) {
            $courseCode = $course[0];
            $coursePreNames = $course[count($course) - 1];
            if ($course[1] == "Graduation Project") {
                continue;
            }
            if ($coursePreNames == null || $coursePreNames == "") {
                continue;
            }
            $coursePreArray = explode(",", $coursePreNames);
            $courseCode = trim($courseCode);
            $course = Course::where("code", $courseCode)->first();
            foreach ($coursePreArray as $coursePreName) {
                // check if "(A)" is present in the string
                $passed = strpos($coursePreName, "(A)") !== false;
                if ($passed) {
                    // remove "(A)" from the string
                    $coursePreName = str_replace("(A)", "", $coursePreName);
                }
                $coursePreName = trim($coursePreName);
                $coursePre = Course::where("code", $coursePreName)->first();
                CoursePre::create([
                    "course_id" => $course->id,
                    "coursePre_id" => $coursePre->id,
                    "passed" => $passed ? 0 : 1,
                ]);
            }
        }
    }
    public function failed(Throwable $exception)
    {
        CoursePre::truncate();
        DepartmentCourse::truncate();
        Course::truncate();
    }
}
