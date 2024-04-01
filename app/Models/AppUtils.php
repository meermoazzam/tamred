<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class AppUtils extends Model
{
    use HasFactory;

    protected $table = 'app_utils';

    protected $fillable = [
        'util_key',
        'data',
    ];

    protected function data(): Attribute
    {
        return Attribute::make(
            get: fn (string|null $value) => ($value) ? json_decode($value, true) : [],
            set: fn (array $value) => json_encode($value),
        );
    }
}
