<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    protected $fillable = [
        'user_id',
        'mediable_id',
        'mediable_type',
        'type',
        'size',
        'media_key',
        'thumbnail_key',
        'sequence',
        'status',
    ];

    public function getMediaKeyAttribute($value)
    {
        if($value) {
            return Storage::disk(env('STORAGE_DISK', 's3'))->url($value);
        } else {
            return $value;
        }
    }

    public function getThumbnailKeyAttribute($value)
    {
        if($value) {
            return Storage::disk(env('STORAGE_DISK', 's3'))->url($value);
        } else {
            return $value;
        }
    }
}
