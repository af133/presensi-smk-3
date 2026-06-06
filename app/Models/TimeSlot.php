<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model {
    public $timestamps = false;
    protected $fillable = ['jam_ke', 'start_time', 'end_time','day_id'];
    
    public function schedules() {
        return $this->hasMany(Schedule::class);
    }
    public function day()
{
    return $this->belongsTo(Day::class);
}
}
