<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable=[
        'time_slot_id',
        'rombel_id',
        'subject_id',
        'teacher_id',
        'classroom_id'
    ];

    public function rombel() {
        return $this->belongsTo(Rombel::class);
    }
    public function time(){
        return $this->belongsTo(TimeSlot::class);
    }

    public function subject() {
        return $this->belongsTo(Subject::class);
    }

    public function teacher() {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classroom() {
        return $this->belongsTo(Classroom::class);
    }
}