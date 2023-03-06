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
            ['name' => 'Max Hours Per Term', 'value' => 18],
            ['name' => 'Min Hours Per Term', 'value' => 12],
            ['name' => 'Min GPA', 'value' => 2],
            ['name' => 'Max Hours Per Term For Min GPA', 'value' => 14],
            ['name' => 'Min Hours Per Term For Min GPA', 'value' => 10],
            ['name' => 'Graduation Hours', 'value' => 180],
            ['name' => 'Max Retake GPA', 'value' => 2],
            ['name' => 'Min Graph Length', 'value' => 5],
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