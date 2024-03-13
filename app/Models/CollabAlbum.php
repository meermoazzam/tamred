<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollabAlbum extends Model
{
    use HasFactory;

    protected $table = 'collab_albums';

    protected $fillable = [
        'user_id',
        'album_id',
    ];
}
