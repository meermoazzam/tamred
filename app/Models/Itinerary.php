<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itineraries';

    protected $fillable = [
        'name',
        'user_id',
        'album_id',
        'is_collaborative',
        'data',
    ];

    public function scopeStatus(Builder $query, mixed $status): Builder
    {
        $status = is_array($status) ? $status : [$status];
        return $query->whereIn('status', $status);
    }

    public function scopeStatusNot(Builder $query, mixed $status): Builder
    {
        $status = is_array($status) ? $status : [$status];
        return $query->whereNotIn('status', $status);
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, ItinPost::class, 'itin_id', 'post_id')->withTimestamps();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

}
