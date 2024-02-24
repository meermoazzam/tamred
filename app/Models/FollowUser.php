<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUser extends Model
{
    use HasFactory;

    protected $table = 'follow_users';

    protected $fillable = [
        'user_id',
        'followed_id',
        'is_approved',
    ];

    public function scopeIsApproved(Builder $query, bool $value = true): Builder
    {
        return $query->where('is_approved', $value);
    }

    public function userDetailByUserId(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function userDetailByFollowedId(): BelongsTo
    {
        return $this->belongsTo(User::class, 'followed_id');
    }
}
