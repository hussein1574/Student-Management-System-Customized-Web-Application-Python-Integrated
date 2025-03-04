<?php

namespace App\Jobs;

use Alert;
use Throwable;
use Carbon\Carbon;
use App\Models\Day;
use App\Models\Hall;
use App\Models\User;
use App\Models\Course;
use App\Models\Constant;
use App\Models\Professor;
use App\Models\Department;
use App\Models\LecturesTime;
use App\Models\ProfessorDay;
use App\Models\StudentCourse;
use Illuminate\Bus\Queueable;
use App\Models\ExamsTimeTable;
use App\Models\LecturesTimeTable;
use Illuminate\Support\Facades\DB;
use Rap2hpoutre\FastExcel\FastExcel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class TimetableProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $lecturesSheet = base_path("app\scripts\lectures\Lecture_Table.xlsx");
        // if the file exists delete it
        if (file_exists($lecturesSheet)) {
            unlink($lecturesSheet);
        }
        Constant::where("name", "Timetable Published")->update([
            "value" => false,
        ]);
        LecturesTimeTable::truncate();
        $this->createConflictTable();
        $this->createHallsFile();
        $this->createProffFile();
        $this->createSubjectsFile();
        $this->createProffTimesFile();
        $this->createPeriodsFile();
        $this->createDaysFile();
        $command = "python " . base_path("app/scripts/lectures/main.py");
        exec($command);
        $this->uploadTimeTable();
    }
    public function failed(Throwable $exception)
    {
        LecturesTimeTable::truncate();
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
            base_path("app\scripts\lectures\conflict_table.xlsx")
        );
    }

    public function createHallsFile()
    {
        // Retrieve all halls with their corresponding department
        $halls = Hall::with("hallsDepartments")->get();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($halls as $hall) {
            if ($hall->is_active == false) {
                continue;
            }
            $hallName = $hall->name;
            $hallCapacity = $hall->capacity;
            $hallDepartment = "";
            if (count($hall->hallsDepartments) > 1) {
                foreach ($hall->hallsDepartments as $hallsDepartment) {
                    if ($hallDepartment == "") {
                        $hallDepartment = Department::where(
                            "id",
                            $hallsDepartment->department_id
                        )->get()[0]->name;
                    } else {
                        $hallDepartment .=
                            "," .
                            Department::where(
                                "id",
                                $hallsDepartment->department_id
                            )->get()[0]->name;
                    }
                }
            } else {
                if (count($hall->hallsDepartments) == 1) {
                    $hallDepartment = Department::where(
                        "id",
                        $hall->hallsDepartments[0]->department_id
                    )->get()[0]->name;
                } else {
                    $hallDepartment = "";
                }
            }

            // If the course code does not exist, add a new entry to the export data array
            $exportData[] = [
                "hall" => $hallName,
                "capacity" => $hallCapacity,
                "department" => $hallDepartment,
            ];
        }
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\halls.xlsx")
        );
    }
    public function createProffFile()
    {
        // Retrieve all courses with their corresponding professors
        $courses = Course::with("professorCourses")->get();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each course
        foreach ($courses as $course) {
            $courseCode = $course->code;
            $professorNames = "";
            if (count($course->professorCourses) > 1) {
                foreach ($course->professorCourses as $professorCourse) {
                    if ($professorNames == "") {
                        $professorNames = Professor::where(
                            "id",
                            $professorCourse->professor_id
                        )->get()[0]->user->name;
                    } else {
                        $professorNames .=
                            "," .
                            Professor::where(
                                "id",
                                $professorCourse->professor_id
                            )->get()[0]->user->name;
                    }
                }
            } else {
                if (count($course->professorCourses) == 1) {
                    $professorNames = Professor::where(
                        "id",
                        $course->professorCourses[0]->professor_id
                    )->get()[0]->user->name;
                } else {
                    $professorNames = "";
                }
            }

            
            $exportData[] = [
                "subject" => $courseCode,
                "prof" => $professorNames,
            ];
        }
        // remove all the subjects that have no professors
        $exportData = array_filter($exportData, function ($value) {
            return $value["prof"] != "";
        });
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\Profs.xlsx")
        );
    }
    public function createSubjectsFile()
    {
        // Retrieve all courses with their corresponding professors
        $courses = Course::with("professorCourses")->get();

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

            $departments = "";
            if (count($course->departmentCourses) > 1) {
                foreach ($course->departmentCourses as $departmentCourses) {
                    if ($departments == "") {
                        $departments = Department::where(
                            "id",
                            $departmentCourses->department_id
                        )->get()[0]->name;
                    } else {
                        $departments .=
                            "," .
                            Department::where(
                                "id",
                                $departmentCourses->department_id
                            )->get()[0]->name;
                    }
                }
            } else {
                if (count($course->departmentCourses) == 1) {
                    $departments = Department::where(
                        "id",
                        $course->departmentCourses[0]->department_id
                    )->get()[0]->name;
                } else {
                    $departments = "";
                }
            }

            // If the course code does not exist, add a new entry to the export data array
            $exportData[] = [
                "subject" => $courseCode,
                "sec" => $courseSection,
                "lab" => $courseLab,
                "lecTime" => $courseLectureTime,
                "secTime" => $courseSectionTime,
                "labTime" => $courseLabTime,
                "department" => $departments,
            ];
        }
        // remove all the subjects that doesn't have a record in student courses with status 3
        $exportData = array_filter($exportData, function ($value) {
            $course = Course::where("code", $value["subject"])->first();
            $studentCourses = StudentCourse::where([
                ["course_id", $course->id],
                ["status_id", 3],
            ])->get();
            return !$studentCourses->isEmpty();
        });
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\subjects.xlsx")
        );
    }
    public function createProffTimesFile()
    {
        $professors = Professor::all();
        $days = Day::all();
        $periods = LecturesTime::all();

        // Create an empty array to store the course code and professor names
        $exportData = [];

        // Loop through each professor
        foreach ($professors as $professor) {
            $professorName = $professor->user->name;

            // Check if the professor has any records in ProfessorDay
            $professorDays = $professor->professorDays;
            if ($professorDays->isEmpty()) {
                // If the professor has no records, add all days and periods
                $professorDaysPeriods = "";
                foreach ($days as $day) {
                    foreach ($periods as $period) {
                        $professorDaysPeriods .=
                            $day->name . "-" . $period->timePeriod . ",";
                    }
                }
                $professorDaysPeriods = rtrim($professorDaysPeriods, ",");
                $exportData[] = [
                    "profs" => $professorName,
                    "periods" => $professorDaysPeriods,
                ];
            } else {
                // If the professor has records, loop through each record
                $professorExists = false;
                foreach ($professorDays as $professorDay) {
                    $professorDayPeriod =
                        $professorDay->day->name .
                        "-" .
                        $professorDay->period->timePeriod;
                    // Check if the professor name exists in the export data array
                    if ($professorExists) {
                        foreach ($exportData as &$data) {
                            if ($data["profs"] == $professorName) {
                                $data["periods"] .= "," . $professorDayPeriod;
                            }
                        }
                    } else {
                        // If the professor name does not exist, add a new entry to the export data array
                        $exportData[] = [
                            "profs" => $professorName,
                            "periods" => $professorDayPeriod,
                        ];
                        $professorExists = true;
                    }
                }
            }
        }
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\Profs Time.xlsx")
        );
    }
    public function createPeriodsFile()
    {
        $periods = LecturesTime::all();
        $exportData = [];
        foreach ($periods as $period) {
            $exportData[] = [
                "period" => $period->timePeriod,
            ];
        }
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\periods.xlsx")
        );
    }
    public function createDaysFile()
    {
        $days = Day::all();
        $exportData = [];
        foreach ($days as $day) {
            $exportData[] = [
                "day" => $day->name,
            ];
        }
        (new FastExcel($exportData))->export(
            base_path("app\scripts\lectures\days.xlsx")
        );
    }
    public function uploadTimeTable()
    {
        $lecturesSheet = base_path("app\scripts\lectures\Lecture_Table.xlsx");
        // Get the list of halls from the first row of the sheet
        $periods = (new FastExcel())
            ->configureCsv("\t")
            ->import($lecturesSheet, function ($line) {
                return array_slice($line, 1);
            })
            ->first();
        #get the keys of the halls
        $periods = array_keys($periods);

        //get list of days
        $days = Day::all()
            ->pluck("name")
            ->toArray();
        $dayCounter = 0;
        // Get the list of courses and their corresponding halls from the remaining rows of the sheet
        $lectures = [];
        (new FastExcel())
            ->configureCsv("\t")
            ->import($lecturesSheet, function ($line) use (
                $periods,
                &$lectures,
                $days,
                &$dayCounter
            ) {
                $courses = array_slice($line, 1);
                foreach ($courses as $key => $course) {
                    if ($course != "") {
                        $periodCourses = explode("\n", $course);
                        foreach ($periodCourses as $periodCourse) {
                            $courseCode = explode("-", $periodCourse)[0];
                            $professorName = explode("-", $periodCourse)[1];
                            $hallName = explode("-", $periodCourse)[2];
                            $lectures[] = [
                                "day" => $days[$dayCounter],
                                "time_period" => $key,
                                "course_code" => $courseCode,
                                "hall_name" => $hallName,
                                "professor_name" => $professorName,
                            ];
                        }
                    }
                }
                $dayCounter++;
            });
        foreach ($lectures as $lecture) {
            $course = Course::where("code", $lecture["course_code"])->first();
            $hall = Hall::where("name", $lecture["hall_name"])->first();
            $day = Day::where("name", $lecture["day"])->first();
            $time = LecturesTime::where(
                "timePeriod",
                $lecture["time_period"]
            )->first();
            $professor = $lecture["professor_name"];

            if ($course && $hall && $day && $time) {
                $lectureTimeTable = new LecturesTimeTable();
                $lectureTimeTable->day_id = $day->id;
                $lectureTimeTable->course_id = $course->id;
                $lectureTimeTable->hall_id = $hall->id;
                $lectureTimeTable->lectureTime_id = $time->id;
                if ($professor == "lab") {
                    $lectureTimeTable->isLab = true;
                } elseif ($professor == "sec") {
                    $lectureTimeTable->isSection = true;
                } else {
                    $lectureTimeTable->professor_id = Professor::where(
                        "user_id",
                        User::where("name", $professor)->first()->id
                    )->first()->id;
                }
                $lectureTimeTable->save();
            }
        }
    }
}