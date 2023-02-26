<?php

namespace App\Jobs;

use GuzzleHttp\Client;
use App\Models\StudentCourse;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendFinishedHoursByYearResponseJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $student;
    private $userId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($student,$userId)
    {
        $this->student = $student;
        $this->userId =  $userId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $courses = StudentCourse::where('student_id', $this->student->id)
        ->whereIn('status_id', [1, 2])
        ->get();

        $finishedHoursByYear = $this->calculateHoursPerYear($courses);

        $client = new Client();
        $response = $client->get('http://localhost:8000/api/test/'.$this->userId, [
            'status' => 'success',
            'results' => count($finishedHoursByYear),
            'data' => [
                'years' => $finishedHoursByYear
            ]
        ]);
    }
    public function calculateHoursPerYear($courses)
    {
        $finishedHoursByYear = [];
        foreach ($courses as $course) {
            $year = date("Y", strtotime($course->created_at));
            if (!isset($finishedHoursByYear[$year])) {
                $finishedHoursByYear[$year] = 0;
            }
            $finishedHoursByYear[$year] += $course->course->hours;
        }
        return $finishedHoursByYear;
    }
}