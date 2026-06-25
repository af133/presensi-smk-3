<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Rombel extends Model
{
    use SoftDeletes;
    protected $fillable = ['name', 'guru_id', 'academic_year_id'];

    public function students()
    {
        return $this->belongsToMany(Student::class, 'rombel_student');
    }

    public function waliKelas()
    {
        return $this->belongsTo(User::class, 'guru_id');
    }

    // Relasi ke Tahun Akademik
    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}