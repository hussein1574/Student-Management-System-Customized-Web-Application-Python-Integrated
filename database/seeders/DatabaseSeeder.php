<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\ConstantsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call(CourseStatusesTableSeeder::class);
        $this->call(ConstantsSeeder::class);
        $this->call(DaysTableSeeder::class);
        $this->call(LecturesTimesSeeder::class);
        \App\Models\User::factory(10)->create();
        //$this->call(CoursesSeeder::class);
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}