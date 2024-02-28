<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'status',
        'title',
        'description',
        'total_likes',
        'total_comments',
        'location',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'tags',
        'tagged_users',
        'allow_comments',
    ];

    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => ($value) ? json_decode($value, true) : [],
            set: fn (array $value) => json_encode($value),
        );
    }

    protected function taggedUsers(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => ($value) ? json_decode($value, true) : [],
            set: fn (array $value) => json_encode($value),
        );
    }

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

    public function comment(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, (new AlbumPost)->getTable());
    }

    public function albumPosts(): HasMany
    {
        return $this->hasMany(AlbumPost::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, (new CategoryPost)->getTable());
    }

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable', 'mediable_type', 'mediable_id', 'id')->where('status', 'published');
    }
}
