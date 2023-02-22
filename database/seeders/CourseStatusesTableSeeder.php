<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CourseStatus;

class CourseStatusesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $statuses = [
            'Pass',
            'Fail',
            'Studying'
        ];
        foreach ($statuses as $statue) {
            CourseStatus::create([
                'status' => $statue
            ]);
        }
    }
}
