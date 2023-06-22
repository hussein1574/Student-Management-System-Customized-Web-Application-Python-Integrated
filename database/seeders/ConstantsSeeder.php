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
            ['name' => 'No. a course opens to be must', 'value' => 5],
            ['name' => 'Regestration Opened' , 'value' => 1],
            ['name' => 'Timetable Published', 'value'=> 0],
            ['name' => 'ExamTimetable Published', 'value'=>0]
        ];

        foreach ($constants as $constant) {
            Constant::create([
                'name' => $constant['name'],
                'value' => $constant['value'],
            ]);
        }
    }
}