<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumPost extends Model
{
    use HasFactory;

    protected $table = 'album_posts';

    protected $fillable = [
        'album_id',
        'post_id',
    ];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}
