<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Staudenmeir\EloquentEagerLimit\HasEagerLimit;

class Reaction extends Model
{
    use HasFactory, HasEagerLimit;

    protected $table = 'reactions';

    protected $fillable = [
        'user_id',
        'reactable_id',
        'reactable_class',
        'type',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
