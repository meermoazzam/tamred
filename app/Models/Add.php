<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Add extends Model
{
    use HasFactory;

    protected $table = 'adds';

    protected $fillable = [
        'title',
        'author',
        'link',
        'start_date',
        'end_date',
        'gender',
        'min_age',
        'max_age',
        'latitude',
        'longitude',
        'range',
        'status',
    ];

    public function media()
    {
        return $this->morphMany(Media::class, 'mediable', 'mediable_type', 'mediable_id', 'id')->where('status', 'published');
    }
}
