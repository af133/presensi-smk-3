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
            $table->string('name'); 
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });
        Schema::create('rombels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            $table->foreignId('guru_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('academic_year_id')->constrained()->onDelete('cascade'); 
            $table->timestamps();
        });
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('nisn')->unique();
            $table->string('name');
            $table->timestamps();
        });
        Schema::create('rombel_student', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rombel_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('rombel_student');
    }
};
