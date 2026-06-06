<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Day extends Model {
    public $timestamps = false;
    protected $guard=[];
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
    public function timeSlots()
{
    return $this->hasMany(TimeSlot::class);
}
}
