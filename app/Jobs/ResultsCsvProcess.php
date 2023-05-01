<?php

namespace App\Jobs;

use Throwable;
use App\Models\StudentCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ResultsCsvProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $data;
    public $header;
    public $courseId;
    public $timeout = 0;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $header, $courseId)
    {
        $this->data   = $data;
        $this->header = $header;
        $this->courseId = $courseId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach($this->data as $course){
            $row = array_combine($this->header, $course);        
            $studentCourse = StudentCourse::where('student_id', $row['﻿Student ID'])->where('course_id', $this->courseId)->first();
            $studentCourse->grade = $row['GPA'];
            $studentCourse->status_id = strtolower($row['Status'])  == 'pass' ? 7 : 8;
            $studentCourse->save();
        }
        
    }
    public function failed(Throwable $exception)
    {
        foreach($this->data as $course){
            $row = array_combine($this->header, $course);        
            $studentCourse = StudentCourse::where('student_id', $row['Student ID'])->where('course_id', $this->courseId)->first();
            $studentCourse->status_id = 3;
            $studentCourse->save();
        }
    }
}