<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up() {
        Schema::create('days', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Senin, Selasa, dll
        });

        Schema::create('time_slots', function (Blueprint $table) {
            $table->id();
            $table->string('jam_ke'); // 'Jam ke-1'
            $table->time('start_time');
            $table->time('end_time');
            $table->foreignId('day_id')->constrained()->onDelete('cascade');
        });

        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
        });

        Schema::create('classrooms', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
        });

        // Tabel Utama Schedule
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('time_slot_id')->constrained();
            $table->foreignId('rombel_id')->constrained();
            $table->foreignId('subject_id')->constrained();
            $table->foreignId('teacher_id')->constrained('users');
            $table->foreignId('classroom_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('time_slots');
        Schema::dropIfExists('days');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('classrooms');
    }
};