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
        Schema::create('professor_courses', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("professor_id")->unsigned();
            $table->integer("course_id")->unsigned();
            $table->timestamps();
            $table->foreign("professor_id")->references("id")->on("professors");
            $table->foreign("course_id")->references("id")->on("courses");
            $table->unique(['professor_id', 'course_id']);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professor_courses');
    }
};