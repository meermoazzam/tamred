<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Activities extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'user_id',
        'caused_by',
        'model_id',
        'type',
        'message',
        'count',
        'is_read',
    ];
}
