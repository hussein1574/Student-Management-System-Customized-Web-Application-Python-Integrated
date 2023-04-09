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
        Schema::create('departments', function (Blueprint $table) {
            $table->increments("id");
            $table->string('name');
            $table->string('min_hours_per_term');
            $table->string('high_gpa');
            $table->string('low_gpa');
            $table->string('max_hours_per_term_for_high_gpa');
            $table->string('max_hours_per_term_for_avg_gpa');
            $table->string('max_hours_per_term_for_low_gpa');
            $table->string('graduation_hours');
            $table->string('graduation_gpa');
            $table->string('max_gpa_to_retake_a_course');
            $table->string('graduation_project_needed_hours');
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
        Schema::dropIfExists('departments');
    }
};