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
        Schema::create('academic_years', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 2025/2026
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // 10-A, 10-B
            $table->timestamps();
        });
        
        // Siswa tidak butuh email/password
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained();
            $table->string('nisn')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('academic_years');
        Schema::dropIfExists('rombels');
        Schema::dropIfExists('students');
    }
};
