<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Activities extends Model
{
    use HasFactory;

    protected $table = 'activities';

    protected $fillable = [
        'user_id',
        'caused_by',
        'model_id',
        'type',
        'message',
        'count',
        'is_read',
    ];

    public function sender(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'caused_by');
    }
}
