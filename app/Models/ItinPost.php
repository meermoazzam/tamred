<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ItinPost extends Model
{
    use HasFactory;

    protected $table = 'itin_posts';

    protected $fillable = [
        'itin_id',
        'post_id',
    ];
}
