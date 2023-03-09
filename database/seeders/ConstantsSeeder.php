<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Constant;

class ConstantsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $constants = [
            ['name' => 'Min Hours Per Term', 'value' => 12],
            ['name' => 'High GPA', 'value' => 3],
            ['name' => 'Low GPA', 'value' => 2],
            ['name' => 'Max Hours Per Term For High GPA', 'value' => 21],
            ['name' => 'Max Hours Per Term For Avg GPA', 'value' => 18],
            ['name' => 'Max Hours Per Term For Low GPA', 'value' => 14],
            ['name' => 'Graduation Hours', 'value' => 160],
            ['name' => 'Graduation GPA', 'value' => 2],
            ['name' => 'Max GPA to retake a course', 'value' => 2],
            ['name' => 'no. a course opens to be must', 'value' => 5],
            ['name' => 'Graduation Project Needed Hours', 'value' => 130]
        ];

        foreach ($constants as $constant) {
            Constant::create([
                'name' => $constant['name'],
                'value' => $constant['value'],
            ]);
        }
    }
}