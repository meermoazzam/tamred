<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'mediable_id',
        'mediable_type',
        'name',
        'type',
        'size',
        'key',
    ];

    public function getKeyAttribute($value)
    {
        return env("AWS_BUCKET") . '.s3.' . env("AWS_DEFAULT_REGION") . '.amazonaws.com/' . $value;
    }
}
