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
        Schema::create('department_courses', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("department_id")->unsigned();
            $table->integer("course_id")->unsigned();
            $table->timestamps();
            $table->foreign("department_id")->references("id")->on("departments");
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
        Schema::dropIfExists('department_courses');
    }
};