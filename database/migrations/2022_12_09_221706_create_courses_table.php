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
            $table->integer("hours");
            $table->integer('level');
            $table->boolean("isElective");
            $table->boolean("hasLab");
            $table->boolean("hasSection");
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