<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUser extends Model
{
    use HasFactory;

    protected $table = 'follow_users';

    protected $fillable = [
        'user_id',
        'followed_id',
        'is_approved',
    ];
}
