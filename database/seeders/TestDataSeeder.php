<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use Database\Factories;
use App\Models\Professor;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\StudentCourse;
use App\Models\ProfessorCourse;
use Illuminate\Database\Seeder;
use App\Jobs\UpdateStudentsGPAProcess;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    // public function run()
    // {
    //     $faker = Faker::create();
    //     #region Students
    //     //First Year students
    //     for ($i = 0; $i < 10; $i++) {
    //         $user = User::create([
    //             "name" => $faker->name,
    //             "email" => $faker->unique()->safeEmail(),
    //             "isActivated" => true,
    //             "isAdmin" => false,
    //             "password" => "12345678",
    //             "remember_token" => Str::random(10),
    //         ]);
    //         $student = Student::create([
    //             "user_id" => $user->id,
    //             "department_id" => 1,
    //             "batch" => 0,
    //             "grade" => 0,
    //         ]);
    //         // First Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 67 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 71 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 72 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 66 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         // First Year - First Semester Optional Courses
    //         if ($i % 2 == 0) {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 128 + 128,
    //                 "status_id" => 3,
    //                 "grade" => 0,
    //                 "class_work_grade" => 0,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 74 + 128,
    //                 "status_id" => 3,
    //                 "grade" => 0,
    //                 "class_work_grade" => 0,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-09-01 00:00:01",
    //             ]);
    //         } else {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 97 + 128,
    //                 "status_id" => 3,
    //                 "grade" => 0,
    //                 "class_work_grade" => 0,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 81 + 128,
    //                 "status_id" => 3,
    //                 "grade" => 0,
    //                 "class_work_grade" => 0,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-09-01 00:00:01",
    //             ]);
    //         }
    //     }
    //     //Second Year students
    //     for ($i = 0; $i < 10; $i++) {
    //         $user = User::create([
    //             "name" => $faker->name,
    //             "email" => $faker->unique()->safeEmail(),
    //             "isActivated" => true,
    //             "isAdmin" => false,
    //             "password" => "12345678",
    //             "remember_token" => Str::random(10),
    //         ]);
    //         $student = Student::create([
    //             "user_id" => $user->id,
    //             "department_id" => 1,
    //             "batch" => 0,
    //             "grade" => 0,
    //         ]);
    //         // First Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 67 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 71 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 20,
    //             "lab_grade" => 0,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 72 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 20,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 66 + 128,
    //             "status_id" => 2,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         // First Year - First Semester Optional Courses
    //         if ($i % 2 == 0) {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 128 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 10,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 74 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 20,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-09-01 00:00:01",
    //             ]);
    //         } else {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 97 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 81 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-09-01 00:00:01",
    //             ]);
    //         }
    //         // First Year - Second Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 66 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 78 + 128,
    //             "status_id" => 1,
    //             "grade" => 40,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 86 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 20,
    //             "lab_grade" => 20,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         // First Year - Second Semester Optional Courses
    //         if ($i % 2 == 0) {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 68 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-02-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 69 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 20,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-02-01 00:00:01",
    //             ]);
    //         } else {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 70 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-02-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 73 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2021-02-01 00:00:01",
    //             ]);
    //         }
    //         // Second Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 77 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 80 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 75 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 76 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 85 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //     }
    //     //Thirth Year students
    //     for ($i = 0; $i < 10; $i++) {
    //         $user = User::create([
    //             "name" => $faker->name,
    //             "email" => $faker->unique()->safeEmail(),
    //             "isActivated" => true,
    //             "isAdmin" => false,
    //             "password" => "12345678",
    //             "remember_token" => Str::random(10),
    //         ]);
    //         $student = Student::create([
    //             "user_id" => $user->id,
    //             "department_id" => 1,
    //             "batch" => 0,
    //             "grade" => 0,
    //         ]);
    //         // First Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 67 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 10,
    //             "created_at" => "2019-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 71 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 20,
    //             "lab_grade" => 0,
    //             "created_at" => "2019-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 72 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 20,
    //             "created_at" => "2019-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 66 + 128,
    //             "status_id" => 2,
    //             "grade" => 50,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2019-09-01 00:00:01",
    //         ]);
    //         // First Year - First Semester Optional Courses
    //         if ($i % 2 == 0) {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 128 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 10,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2019-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 74 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 20,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2019-09-01 00:00:01",
    //             ]);
    //         } else {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 97 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2019-09-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 81 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2019-09-01 00:00:01",
    //             ]);
    //         }
    //         // First Year - Second Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 66 + 128,
    //             "status_id" => 1,
    //             "grade" => 50,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 0,
    //             "created_at" => "2020-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 78 + 128,
    //             "status_id" => 1,
    //             "grade" => 40,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 0,
    //             "created_at" => "2020-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 86 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 20,
    //             "lab_grade" => 20,
    //             "created_at" => "2020-02-01 00:00:01",
    //         ]);
    //         // First Year - Second Semester Optional Courses
    //         if ($i % 2 == 0) {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 68 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-02-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 69 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 50,
    //                 "class_work_grade" => 20,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-02-01 00:00:01",
    //             ]);
    //         } else {
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 70 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-02-01 00:00:01",
    //             ]);
    //             StudentCourse::create([
    //                 "student_id" => $student->id,
    //                 "course_id" => 73 + 128,
    //                 "status_id" => 1,
    //                 "grade" => 40,
    //                 "class_work_grade" => 30,
    //                 "lab_grade" => 0,
    //                 "created_at" => "2020-02-01 00:00:01",
    //             ]);
    //         }
    //         // Second Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 77 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 80 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 75 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         // Second Year - First Semester Optional Courses
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 76 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 85 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2020-09-01 00:00:01",
    //         ]);
    //         // Second Year - Second Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 91 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 88 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         // Second Year - Second Semester Optional Courses
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 90 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 92 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 93 + 128,
    //             "status_id" => 1,
    //             "grade" => 30,
    //             "class_work_grade" => 30,
    //             "lab_grade" => 10,
    //             "created_at" => "2021-02-01 00:00:01",
    //         ]);
    //         // Third Year - First Semester Courses Must Be Added
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 89 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 84 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         // Third Year - First Semester Optional Courses
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 79 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 98 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //         StudentCourse::create([
    //             "student_id" => $student->id,
    //             "course_id" => 101 + 128,
    //             "status_id" => 3,
    //             "grade" => 0,
    //             "class_work_grade" => 0,
    //             "lab_grade" => 0,
    //             "created_at" => "2021-09-01 00:00:01",
    //         ]);
    //     }
    //     dispatch(new UpdateStudentsGPAProcess());
    //     #endregion
    //     #region Professors
    //     for ($i = 0; $i < 10; $i++) {
    //         $user = User::create([
    //             "name" => $faker->name,
    //             "email" => $faker->unique()->safeEmail(),
    //             "isActivated" => true,
    //             "isAdmin" => true,
    //             "password" => "12345678",
    //             "remember_token" => Str::random(10),
    //         ]);
    //         $professor = Professor::create([
    //             "user_id" => $user->id,
    //         ]);
    //         if ($i === 0) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 67 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 71 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 72 + 128,
    //             ]);
    //         } elseif ($i === 1) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 66 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 128 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 74 + 128,
    //             ]);
    //         } elseif ($i === 2) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 66 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 97 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 81 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 78 + 128,
    //             ]);
    //         } elseif ($i === 3) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 86 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 68 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 69 + 128,
    //             ]);
    //         } elseif ($i === 4) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 70 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 73 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 77 + 128,
    //             ]);
    //         } elseif ($i === 5) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 80 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 75 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 76 + 128,
    //             ]);
    //         } elseif ($i === 6) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 85 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 91 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 88 + 128,
    //             ]);
    //         } elseif ($i === 7) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 90 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 92 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 93 + 128,
    //             ]);
    //         } elseif ($i === 8) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 90 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 89 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 84 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 98 + 128,
    //             ]);
    //         } elseif ($i === 9) {
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 79 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 98 + 128,
    //             ]);
    //             ProfessorCourse::create([
    //                 "professor_id" => $professor->id,
    //                 "course_id" => 101 + 128,
    //             ]);
    //         }
    //     }
    //     #endregion
    // }
    public function run()
    {
        dispatch(new UpdateStudentsGPAProcess());
    }
}