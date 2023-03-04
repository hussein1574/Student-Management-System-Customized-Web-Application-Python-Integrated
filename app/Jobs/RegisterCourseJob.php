<?php

namespace App\Jobs;

use App\Models\StudentCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class RegisterCourseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $courseId;
    private $studentId;
    private $retakeCourses;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($courseId, $studentId,$retakeCourses)
    {
        $this->courseId = $courseId;
        $this->studentId = $studentId;
        $this->retakeCourses = $retakeCourses;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if(in_array($this->courseId, $this->retakeCourses)){
            $studentCourse = StudentCourse::where('student_id', $this->studentId)->where('course_id', $this->courseId)->first();
            $studentCourse->status_id = 4;
            $studentCourse->save();
        }
        else
        {
            $studentCourse = new StudentCourse();
            $studentCourse->student_id = $this->studentId;
            $studentCourse->course_id = $this->courseId;
            $studentCourse->grade = 0;
            $studentCourse->status_id = 4;
            $studentCourse->save();
        }
    }
}