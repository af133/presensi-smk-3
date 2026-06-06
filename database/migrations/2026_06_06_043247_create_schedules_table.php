<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
        });

        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->string('day'); 
            $table->integer('jam_ke');
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('rombel_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('classroom_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
        Schema::dropIfExists('schedules');
    }
};
