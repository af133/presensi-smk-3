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
        Schema::create('presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->date('date');
            $table->time('check_in_time')->nullable();
            $table->timestamps();
        });

        Schema::create('student_presences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')->constrained();
            $table->foreignId('student_id')->constrained();
            $table->enum('status', ['hadir', 'sakit', 'izin', 'alpha', 'bolos']);
            $table->timestamps();
        });

        Schema::create('journals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('presence_id')->constrained();
            $table->text('topic'); // Materi
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presences');
        Schema::dropIfExists('student_presences');
        Schema::dropIfExists('journals');
    }
};
