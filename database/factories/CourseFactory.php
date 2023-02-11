<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Course;
use Faker\Generator as Faker;

$factory->define(Course::class, function (Faker $faker) {
    return [
        'name' => $faker->word,
        'hours' => $faker->numberBetween(1, 10),
        'isElective' => $faker->boolean,
        'hasLab' => $faker->boolean,
        'hasSection' => $faker->boolean
    ];
});
