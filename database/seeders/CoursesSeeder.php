<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Database\Factories;
use Faker\Factory as Faker;
use App\Models\Course;

class CoursesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $faker = Faker::create();

        for ($i = 0; $i < 50; $i++) {
            Course::create([
                'name' => $faker->word,
                'hours' => $faker->numberBetween(1, 10),
                'isElective' => $faker->boolean,
                'hasLab' => $faker->boolean,
                'hasSection' => $faker->boolean,
            ]);
        }
    }
}
