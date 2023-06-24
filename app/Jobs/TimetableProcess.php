<?php

namespace App\Jobs;

use Alert;
use Throwable;
use Carbon\Carbon;
use App\Models\Hall;
use App\Models\Course;
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

class TimetableProcess implements ShouldQueue
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
                $this->createConflictTable();
                $this->createHallsFile();
                // $command = "python " . base_path('app/scripts/main.py');
                // exec($command);
                // $this->uploadExamTimeTable();    
    }
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
    public function createConflictTable() {
                         // Build the query to retrieve the conflicts
                         $conflicts = DB::table('student_courses as sc1')
                         ->join('student_courses as sc2', function($join) {
                             $join->on('sc1.student_id', '=', 'sc2.student_id')
                                 ->on('sc1.course_id', '<=', 'sc2.course_id');
                         })
                         ->join('courses as c1', 'sc1.course_id', '=', 'c1.id')
                         ->join('courses as c2', 'sc2.course_id', '=', 'c2.id')
                         ->selectRaw('CASE WHEN c1.id < c2.id THEN c1.code ELSE c2.code END AS course1, 
                                     CASE WHEN c1.id < c2.id THEN c2.code ELSE c1.code END AS course2, 
                                     COUNT(sc1.student_id) AS count')
                         ->where('sc1.status_id', '=', 3)
                         ->where('sc2.status_id', '=', 3)
                         ->groupBy('course1', 'course2')
                         ->get();
                        
                        // Convert the conflicts data to an array format suitable for FastExcel
                        $data = [];
                        $columnHeaders = [''];
                        
                        // Add the course names to the column headers
                        foreach($conflicts as $conflict) {
                            $course1 = $conflict->course1;
                            $course2 = $conflict->course2;
                            $count = $conflict->count;
                        
                            if(!in_array($course1, $columnHeaders)) {
                                $columnHeaders[] = $course1;
                            }
                            if(!in_array($course2, $columnHeaders)) {
                                $columnHeaders[] = $course2;
                            }
                        
                            // Add the conflict count to the data array
                            if(!isset($data[$course1])) {
                                $data[$course1] = ['' => $course1];
                            }
                            $data[$course1][$course2] = $count;
                            if(!isset($data[$course2])) {
                                $data[$course2] = ['' => $course2];
                            }
                            $data[$course2][$course1] = $count;
                        }
                        
                        // Insert the column headers at the start of the data array
                        array_unshift($data, $columnHeaders);
                        unset($data[0]);
                        // Export the data to an Excel sheet using FastExcel
                        (new FastExcel($data))->export(base_path('app\scripts\conflict_table.xlsx'));

    }
    public function createHallsFile() {
        // get halls and halls capacity and halls department (from halls_departments table)
        $halls = Hall::select('halls.name', 'halls.capacity', 'departments.name as department')
                     ->join('halls_departments', 'halls.id', '=', 'halls_departments.hall_id')
                     ->join('departments', 'halls_departments.department_id', '=', 'departments.id')
                     ->where('halls.is_active', true)
                     ->get()
                     ->toArray();
        (new FastExcel($halls))->export(base_path('app\scripts\halls.xlsx'));    
    }
    public function createProffFile()
    {
        $courses = Course::select('courses.code')
                         ->get()
                         ->toArray();
    }
    public function uploadExamTimeTable() {
        $examsSheet = base_path('app/scripts/Exams_Table.xlsx');
        // Get the list of halls from the first row of the sheet
        $halls = (new FastExcel)->configureCsv("\t")->import($examsSheet, function($line) {
            return array_slice($line, 1);
        })->first();
        #get the keys of the halls
        $halls = array_keys($halls);
        // Get the list of courses and their corresponding halls from the remaining rows of the sheet
        $exams = [];
        $day = Carbon::parse($this->examsStartDate.' 9:00:00');
        (new FastExcel)->configureCsv("\t")->import($examsSheet, function($line) use ($halls, &$exams, &$day) {
            $courses = array_slice($line, 1);
            foreach ($courses as $key => $course) {
                if ($course != "-") {
                    $exams[] = [
                        'day' => $day->format('Y-m-d H:i:s'),
                        'course_code' => $course,
                        'hall_name' => $key,
                    ];
                }
            }
            $day->addDay();
        });
        foreach($exams as $exam)
        {
            $course = Course::where('code', $exam['course_code'])->first();
            $hall = Hall::where('name', $exam['hall_name'])->first();
            if($course && $hall)
            {
                $examTimeTable = new ExamsTimeTable();
                $examTimeTable->day = $exam['day'];
                $examTimeTable->course_id = $course->id;
                $examTimeTable->hall_id = $hall->id;
                $examTimeTable->save();
            }
        }
    }
}