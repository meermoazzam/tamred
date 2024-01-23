<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'status',
        'title',
        'description',
        'album_id',
        'total_likes',
        'location',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'tags',
        'tagged_users',
    ];

    protected function tags(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => json_decode($value, true),
            set: fn (array $value) => json_encode($value),
        );
    }
}
