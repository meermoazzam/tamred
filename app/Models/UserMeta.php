<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserMeta extends Model
{
    use HasFactory;

    protected $table = 'user_meta';


    /**
     * User Meta is catering following fillables
     * 
     */
    protected $fillable = [
        "user_id",
        "meta_key",
        "meta_value",
    ];
}
