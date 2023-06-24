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
        Schema::create('professor_days', function (Blueprint $table) {
            $table->increments("id");
            $table->integer("professor_id")->unsigned();
            $table->integer("day_id")->unsigned();
            $table->integer("period_id")->unsigned();
            $table->timestamps();
            $table->foreign("professor_id")->references("id")->on("professors");
            $table->foreign("day_id")->references("id")->on("days");
            $table->foreign("period_id")->references("id")->on("lectures_times");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('professor_days');
    }
};