<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\LecturesTime;

class LecturesTimesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $times = [
            '9:00AM-10:30AM',
            '10:30AM-12:00PM',
            '12:30PM-2:00PM',
            '2:00PM-3:30PM',
            '3:30PM-5:00PM',
        ];

        foreach ($times as $time) {
            LecturesTime::create([
                'timePeriod' => $time,
            ]);
        }
    }
}
