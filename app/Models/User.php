<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'nickname',
        'username',
        'email',
        'password',
        'email_verified_at',
        'date_of_birth',
        'gender',
        'location',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function follower(): HasMany
    {
        return $this->hasMany(FollowUser::class, 'followed_id', 'id')->isApproved(true);
    }

    public function following(): HasMany
    {
        return $this->hasMany(FollowUser::class, 'user_id', 'id')->isApproved(true);
    }

    public function post(): HasMany
    {
        return $this->hasMany(Post::class, 'user_id', 'id')->status('published');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, (new UserCategory())->getTable());
    }
}
