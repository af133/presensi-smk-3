<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presence extends Model
{
    protected $fillable = ['schedule_id', 'user_id', 'date', 'check_in_time','start_time','end_time'];

    // Relasi ke Jadwal
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    // Relasi ke User (Guru yang menginput/piket)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke Daftar Kehadiran Siswa
    public function studentPresences(): HasMany
    {
        return $this->hasMany(StudentPresence::class);
    }

    // Relasi ke Jurnal (Biasanya 1 Presence = 1 Jurnal)
    public function journal(): HasOne
    {
        return $this->hasOne(Journal::class);
    }
}