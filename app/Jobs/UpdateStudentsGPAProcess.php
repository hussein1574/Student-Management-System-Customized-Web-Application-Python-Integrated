<?php

namespace App\Jobs;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class UpdateStudentsGPAProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $students = Student::all();
        foreach($students as $student){
            $student->grade = 0;
            $totalHours = 0;
            $studentCourses = $student->studentCourses;
            foreach($studentCourses as $studentCourse){
                $student->grade += $studentCourse->grade * $studentCourse->course->hours;
                $totalHours += $studentCourse->course->hours;
            }
            $student->grade = round($student->grade / $totalHours,2);
            $student->save();
        }
    }
}