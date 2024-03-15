<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
        return $this->belongsToMany(Post::class, ItinPost::class, 'itin_id', 'post_id')->status('published')->withTimestamps();
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, CollabItin::class, 'itin_id', 'user_id')->status('active')->withTimestamps();
    }

    public function collabItins(): HasMany
    {
        return $this->hasMany(CollabItin::class, 'itin_id');
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
