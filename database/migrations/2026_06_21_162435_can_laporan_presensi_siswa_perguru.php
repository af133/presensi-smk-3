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
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_laporan_presensi_siswa_perguru')->default(false)->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Menghapus kolom jika rollback
            $table->dropColumn('can_laporan_presensi_siswa_perguru');
        });
    }
};
