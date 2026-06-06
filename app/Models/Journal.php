<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Journal extends Model
{
    protected $fillable = ['presence_id', 'topic'];

    public function presence(): BelongsTo
    {
        return $this->belongsTo(Presence::class);
    }
}