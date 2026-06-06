<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    public $timestamps = false;
    protected $fillable = ['name'];
}