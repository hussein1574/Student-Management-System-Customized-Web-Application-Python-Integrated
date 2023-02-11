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
            ['name' => 'maxHoursPerTerm', 'value' => 18],
            ['name' => 'minHoursPerTerm', 'value' => 12],
            ['name' => 'minGPA', 'value' => 2],
            ['name' => 'maxHoursPerTermForMinGPA', 'value' => 14],
            ['name' => 'minHoursPerTermForMinGPA', 'value' => 10],
            ['name' => 'graduationHours', 'value' => 180],
            ['name' => 'maxRetakeGPA', 'value' => 2],
        ];

        foreach ($constants as $constant) {
            Constant::create([
                'name' => $constant['name'],
                'value' => $constant['value'],
            ]);
        }
    }
}
