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
        Schema::create('course_pres', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("course_id")->unsigned();
            $table->integer("coursePre_id")->unsigned();
            $table->boolean("passed")->default(false);
            $table->timestamps();
            $table->foreign("course_id")->references("id")->on("courses");
            $table->foreign("coursePre_id")->references("id")->on("courses");
            $table->unique(['coursePre_id', 'course_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('course_pres');
    }
};