<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name', 
        'can_jadwal_kelas',
        'can_laporan_presensi_siswa_guru',
        'can_laporan_presensi_siswa_all',
        'can_laporan_presensi_guru',
        'can_monitoring_kelas',
        'can_laporan_jurnal_pembelajaran',
        'can_laporan_presensi_siswa_perguru'
    ];
    protected $casts = [
        'can_jadwal_kelas' => 'boolean',
        'can_laporan_presensi_siswa_guru' => 'boolean',
        'can_laporan_presensi_siswa_all' => 'boolean',
        'can_laporan_presensi_guru' => 'boolean',
        'can_monitoring_kelas' => 'boolean',
        'can_laporan_jurnal_pembelajaran' => 'boolean',
        'can_laporan_presensi_siswa_perguru'=>'boolean'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class, 'model_has_roles', 'role_id', 'user_id');
    }
}