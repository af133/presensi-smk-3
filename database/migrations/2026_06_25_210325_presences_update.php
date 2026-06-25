<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            if (!Schema::hasColumn('presences', 'start_time')) {
                $table->time('start_time')->nullable();
            }
            if (!Schema::hasColumn('presences', 'end_time')) {
                $table->time('end_time')->nullable();
            }
            if (!Schema::hasColumn('presences', 'rombel_name')) {
                $table->string('rombel_name')->after('end_time');
            }
            if (!Schema::hasColumn('presences', 'subject_name')) {
                $table->string('subject_name')->after('rombel_name');
            }
            if (!Schema::hasColumn('presences', 'user_id')) {
                $table->foreignId('user_id')->constrained('users');
            }
            if (!Schema::hasColumn('presences', 'check_in_time')) {
                $table->time('check_in_time')->nullable()->after('date');
            }
            if (!Schema::hasColumn('presences', 'classroom_name')) {
                $table->string('classroom_name')->after('rombel_name');
            }
            if (!Schema::hasColumn('presences', 'academic_years')) {
                $table->text('academic_years')->after('classroom_name');
            }
            $table->dropForeign(['schedule_id']);
            $table->dropColumn('schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('presences', function (Blueprint $table) {
            $table->dropForeign(['journals_id']);
            $table->dropForeign(['user_id']); 
            $table->foreignId('schedule_id')->nullable()->constrained('schedules');
            $table->dropColumn([
                'start_time', 'end_time', 'rombel_name', 'subject_name', 
                'user_id', 'journals_id', 'check_in_time', 'classroom_name', 'academic_years'
            ]);
        });
    }
};