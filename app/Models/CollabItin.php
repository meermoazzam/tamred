<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CollabItin extends Model
{
    use HasFactory;

    protected $table = 'collab_itins';

    protected $fillable = [
        'user_id',
        'itin_id',
    ];

    public function itin(): BelongsTo
    {
        return $this->belongsTo(Itinerary::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
