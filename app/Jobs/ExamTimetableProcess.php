<?php

namespace App\Jobs;

use Alert;
use Throwable;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class ExamTimetableProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $timeout = 0;
    private $maxStds;
    private $maxRooms;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($maxStds, $maxRooms)
    {
        $this->maxStds = $maxStds;
        $this->maxRooms = $maxRooms;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $command = "python " . base_path('app/scripts/main.py') . " $this->maxStds $this->maxRooms";
        exec($command);
    }
    public function failed(Throwable $exception)
    {
        // Send user notification of failure, etc...
    }
}