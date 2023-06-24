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
        Schema::create('halls_departments', function (Blueprint $table) {
            $table->id();
            $table->integer("hall_id")->unsigned();
            $table->integer("department_id")->unsigned();
            $table->foreign("hall_id")->references("id")->on("halls");
            $table->foreign("department_id")->references("id")->on("departments");
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
        Schema::dropIfExists('halls_departments');
    }
};