<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    protected $model;

    public function __construct(User $user)
    {
        $this->model = $user;
    }

    public funcion getDeviceIdByUserId(int $userId = 0)
    {
        return $this->model->where('id', $userId)->value('device_id');
    }
}
