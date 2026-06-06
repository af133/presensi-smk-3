<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['nisn', 'name'];

    // Relasi Many-to-Many
    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_student');
    }
}