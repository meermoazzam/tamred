<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';

    protected $fillable = [
        'user_id',
        'name',
        'status',
        'is_collaborative',
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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itineraries(): HasMany
    {
        return $this->hasMany(Itinerary::class)->status('published');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, (new AlbumPost)->getTable())->status('published');
    }

    public function getMediaCountAttribute()
    {
        return $this->posts->flatMap->media->where('status', 'published')->count();
    }

    public function getFirstMediaAttribute()
    {
        return $this->posts->flatMap->media->where('status', 'published')->first();
    }

    public function getFirstPostAttribute()
    {
        return $this->posts->where('status', 'published')->first();
    }

    public function collaborators(): BelongsToMany
    {
        return $this->belongsToMany(User::class, CollabAlbum::class, 'album_id', 'user_id')->status('active')->withTimestamps();
    }

    public function collabAlbums(): HasMany
    {
        return $this->hasMany(CollabAlbum::class, 'album_id');
    }
}
