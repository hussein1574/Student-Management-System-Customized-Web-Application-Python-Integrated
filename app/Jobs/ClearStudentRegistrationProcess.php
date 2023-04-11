<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ClearStudentRegistrationProcess implements ShouldQueue
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
        $pendingCourses = \App\Models\StudentCourse::where('status_id', 4)->get();
        foreach ($pendingCourses as $pendingCourse) {
            if($pendingCourse->grade != null)
            {
                if($pendingCourse->grade < 2)
                {
                    $pendingCourse->status_id = 2;
                }
                else
                {
                    $pendingCourse->status_id = 1;
                }
                $pendingCourse->save();
            }
            else
            {
                $pendingCourse->delete();
            }
        }
    }
}