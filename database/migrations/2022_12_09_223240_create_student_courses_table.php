<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('student_courses', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("student_id")->unsigned();
            $table->integer("course_id")->unsigned();
            $table->integer("status_id")->unsigned();
            $table->double("grade")->nullable();
            $table->timestamps();
            $table->foreign("student_id")->references("id")->on("students");
            $table->foreign("course_id")->references("id")->on("courses");
            $table->foreign("status_id")->references("id")->on("course_statuses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('student_courses');
    }
};
