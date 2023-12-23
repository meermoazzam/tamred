<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'posts';

    protected $fillable = [
        'user_id',
        'status',
        'title',
        'description',
        'location',
        'lat',
        'lng',
        'city',
        'state',
        'country',
        'tags',
        'tagged_users',
    ];
}
