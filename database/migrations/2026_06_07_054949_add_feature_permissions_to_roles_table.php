<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('can_jadwal_kelas')->default(false);
            $table->boolean('can_laporan_presensi_siswa_guru')->default(false);
            $table->boolean('can_laporan_presensi_siswa_all')->default(false);
            $table->boolean('can_laporan_presensi_guru')->default(false);
            $table->boolean('can_monitoring_kelas')->default(false);
            $table->boolean('can_laporan_jurnal_pembelajaran')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn([
                'can_jadwal_kelas',
                'can_laporan_presensi_siswa_guru',
                'can_laporan_presensi_siswa_all',
                'can_laporan_presensi_guru',
                'can_monitoring_kelas',
                'can_laporan_jurnal_pembelajaran'
            ]);
        });
    }
};