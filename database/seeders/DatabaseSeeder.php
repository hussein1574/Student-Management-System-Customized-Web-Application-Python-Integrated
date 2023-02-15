<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolesTableSeeder::class);
        $this->call(CourseStatusesTableSeeder::class);
        $this->call(ConstantsSeeder::class);
        $this->call(DaysTableSeeder::class);
        $this->call(LecturesTimesSeeder::class);
        // \App\Models\User::factory(10)->create();
       

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}
