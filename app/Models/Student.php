<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = ['rombel_id', 'nisn', 'name'];

    public function rombel()
    {
        return $this->belongsTo(Rombel::class);
    }
}