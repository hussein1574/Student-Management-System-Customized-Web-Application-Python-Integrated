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
        Schema::create('exams_time_table', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("day")->unique();
            $table->integer("hall_id")->unsigned();
            $table->integer("course_id")->unsigned();
            $table->timestamps();
            $table->foreign("hall_id")->references("id")->on("halls");
            $table->foreign("course_id")->references("id")->on("courses");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('exams_time_table');
    }
};
