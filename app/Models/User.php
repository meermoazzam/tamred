<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'bio',
        'nickname',
        'username',
        'email',
        'password',
        'email_verified_at',
        'date_of_birth',
        'gender',
        'image',
        'thumbnail',
        'cover',
        'location',
        'latitude',
        'longitude',
        'city',
        'state',
        'country',
        'status',
        'device_id',
        'notification_settings',
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


    public function scopeStatus(Builder $query, mixed $status): Builder
    {
        $status = is_array($status) ? $status : [$status];
        return $query->whereIn('status', $status);
    }

    public function scopeStatusNot(Builder $query, mixed $status): Builder
    {
        $status = is_array($status) ? $status : [$status];
        return $query->whereNotIn('status', $status);
    }

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

    public function album(): HasMany
    {
        return $this->hasMany(Album::class, 'user_id', 'id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, (new UserCategory())->getTable());
    }

    public function getImageAttribute($value)
    {
        if($value) {
            return Storage::disk(env('STORAGE_DISK', 's3'))->url($value);
        } else {
            return Storage::disk('public')->url("/images/person.jpg");
        }
    }

    public function getThumbnailAttribute($value)
    {
        if($value) {
            return Storage::disk(env('STORAGE_DISK', 's3'))->url($value);
        } else {
            return Storage::disk('public')->url("/images/person.jpg");
        }
    }
}
