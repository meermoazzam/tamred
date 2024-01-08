<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    use HasFactory;

    protected $table = 'albums';

    protected $fillable = [
        'user_id',
        'name',
        'status',
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
}
