<?php

namespace App\Jobs;

use App\Models\Student;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use App\Http\Controllers\StudentCoursesController;

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
                $student->grade += StudentCoursesController::getGPA($studentCourse->grade + $studentCourse->class_work_grade + $studentCourse->lab_grade) * $studentCourse->course->LectureHours + $studentCourse->course->labHours + $studentCourse->course->sectionHours;
                $totalHours += $studentCourse->course->LectureHours + $studentCourse->course->labHours + $studentCourse->course->sectionHours;
            }
            $student->grade = round($student->grade / $totalHours,2);
            $student->save();
        }
    }
}