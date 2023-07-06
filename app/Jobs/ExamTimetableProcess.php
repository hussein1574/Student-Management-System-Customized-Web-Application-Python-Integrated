<?php

namespace App\Jobs;

use Alert;
use Throwable;
use Carbon\Carbon;
use App\Models\Hall;
use App\Models\Course;
use App\Models\Constant;
use App\Models\StudentCourse;
use Illuminate\Bus\Queueable;
use App\Models\ExamsTimeTable;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ExamTimetableProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 0;
    private $examsStartDate;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($examsStartDate)
    {
        $this->examsStartDate = $examsStartDate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $examsSheet = base_path("app/scripts/Exams_Table.xlsx");
        // if the file exists delete it
        if (file_exists($examsSheet)) {
            unlink($examsSheet);
        }
        Constant::where("name", "ExamTimetable Published")->update([
            "value" => false,
        ]);
        ExamsTimeTable::truncate();
        $this->createConflictTable();
        $this->createHallsFile();
        $command = "python " . base_path("app/scripts/main.py");
        exec($command);
        $this->uploadExamTimeTable();
    }
    public function failed(Throwable $exception)
    {
        ExamsTimeTable::truncate();
    }
    public function createConflictTable()
    {
        $conflictData = [];
        $studentCourses = StudentCourse::where("status_id", 3)->get();
        $courses = Course::whereIn(
            "id",
            $studentCourses->pluck("course_id")
        )->get();
        $coursesCodes = $courses->pluck("code")->toArray();

        // Add courses codes as the first row
        array_unshift($coursesCodes, "Courses Codes");
        $conflictData[] = $coursesCodes;

        foreach ($coursesCodes as $courseCode) {
            if ($courseCode == "Courses Codes") {
                continue;
            }
            $row = [$courseCode];
            foreach ($coursesCodes as $otherCourseCode) {
                // Count the number of students taking the two courses
                $count = 0;
                if (
                    $courseCode == "Courses Codes" ||
                    $otherCourseCode == "Courses Codes"
                ) {
                    continue;
                }
                if ($courseCode == $otherCourseCode) {
                    $count = StudentCourse::where(
                        "course_id",
                        Course::where("code", $courseCode)->first()->id
                    )
                        ->where("status_id", 3)
                        ->count();
                } else {
                    $count = StudentCourse::where(
                        "course_id",
                        Course::where("code", $courseCode)->first()->id
                    )
                        ->where("status_id", 3)
                        ->when($otherCourseCode, function ($query) use (
                            $otherCourseCode
                        ) {
                            $query->whereIn(
                                "student_id",
                                StudentCourse::where(
                                    "course_id",
                                    Course::where(
                                        "code",
                                        $otherCourseCode
                                    )->first()->id
                                )
                                    ->where("status_id", 3)
                                    ->pluck("student_id")
                            );
                        })
                        ->count();
                }
                $row[] = $count;
            }
            $conflictData[] = $row;
        }
        // Extract the first array as keys
        $keys = $conflictData[0];

        // Remove the first array from the main array
        $data = array_slice($conflictData, 1);

        // Combine the keys with the remaining arrays
        $result = [];
        foreach ($data as $row) {
            $result[] = array_combine($keys, $row);
        }
        // Export the data to an Excel sheet using FastExcel
        (new FastExcel($result))->export(
            base_path("app\scripts\conflict_table.xlsx")
        );
    }
    public function createHallsFile()
    {
        $halls = Hall::select("name", "capacity")
            ->where("is_active", true)
            ->get()
            ->toArray();
        (new FastExcel($halls))->export(base_path("app\scripts\halls.xlsx"));
    }
    public function uploadExamTimeTable()
    {
        $examsSheet = base_path("app/scripts/Exams_Table.xlsx");
        // Get the list of halls from the first row of the sheet
        $halls = (new FastExcel())
            ->configureCsv("\t")
            ->import($examsSheet, function ($line) {
                return array_slice($line, 1);
            })
            ->first();
        #get the keys of the halls
        $halls = array_keys($halls);
        // Get the list of courses and their corresponding halls from the remaining rows of the sheet
        $exams = [];
        $day = Carbon::parse($this->examsStartDate . " 9:00:00");
        (new FastExcel())
            ->configureCsv("\t")
            ->import($examsSheet, function ($line) use (
                $halls,
                &$exams,
                &$day
            ) {
                $courses = array_slice($line, 1);
                foreach ($courses as $key => $course) {
                    if ($course != "-") {
                        $exams[] = [
                            "day" => $day->format("Y-m-d H:i:s"),
                            "course_code" => $course,
                            "hall_name" => $key,
                        ];
                    }
                }
                $day->addDay();
            });
        foreach ($exams as $exam) {
            $course = Course::where("code", $exam["course_code"])->first();
            $hall = Hall::where("name", $exam["hall_name"])->first();
            if ($course && $hall) {
                $examTimeTable = new ExamsTimeTable();
                $examTimeTable->day = $exam["day"];
                $examTimeTable->course_id = $course->id;
                $examTimeTable->hall_id = $hall->id;
                $examTimeTable->save();
            }
        }
    }
}