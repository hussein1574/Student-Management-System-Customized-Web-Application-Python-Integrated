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
        Schema::create('courses', function (Blueprint $table) {
            $table->increments("id");
            $table->string('code')->unique();
            $table->string('name');
            $table->integer("LectureHours");
            $table->integer("sectionHours")->nullable();
            $table->integer("labHours")->nullable();
            $table->integer('level');
            $table->boolean("isElective");
            $table->boolean("isClosed")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
};