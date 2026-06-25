<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Presence extends Model
{
    protected $fillable = [
        'user_id', 
        'date', 
        'check_in_time',
        'start_time',
        'end_time',
        'rombel_name',
        'subject_name',
        'classroom_name',
        'academic_years'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function studentPresences(): HasMany
    {
        return $this->hasMany(StudentPresence::class);
    }

    public function journal(): HasOne
    {
        return $this->hasOne(Journal::class, 'presence_id');
    }
}