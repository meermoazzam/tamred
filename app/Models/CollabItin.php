<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CollabItin extends Model
{
    use HasFactory;

    protected $table = 'collab_itins';

    protected $fillable = [
        'user_id',
        'itin_id',
    ];
}
