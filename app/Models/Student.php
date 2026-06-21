<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = ['nisn', 'name'];

    public function rombels()
    {
        return $this->belongsToMany(Rombel::class, 'rombel_student');
    }
}