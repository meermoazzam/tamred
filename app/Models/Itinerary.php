<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Itinerary extends Model
{
    use HasFactory;

    protected $table = 'itineararies';

    protected $fillable = [
        'name',
        'user_id',
        'data',
    ];
}
