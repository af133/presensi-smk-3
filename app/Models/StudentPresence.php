<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentPresence extends Model
{
    protected $fillable = ['presence_id', 'student_id', 'status'];

    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}