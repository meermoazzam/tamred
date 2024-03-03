<?php

namespace App\Services;

use OneSignal;
use App\Repositories\UserRepository;

class OneSignalNotificationService
{
    protected $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        // Add your code here
    }

    public function sendNewMessageNotification(int $userId = null, string $url = null, array $data = null, array $buttons = null, int $schedule = null)
    {
        $message = "You have received a new message";
        $deviceId = $this->userRepository->getDeviceIdByUserId($userId);
        return $this->sendNotification($message, $deviceId, $url, $data, $buttons, $schedule);
    }

    public function sendCommentedNotification(int $userId = null, string $url = null, array $data = null, array $buttons = null, int $schedule = null)
    {
        $message = "Someone commented on your post";
        $deviceId = $this->userRepository->getDeviceIdByUserId($userId);
        return $this->sendNotification($message, $deviceId, $url, $data, $buttons, $schedule);
    }

    public function sendPostLikedNotification(int $userId = null, string $url = null, array $data = null, array $buttons = null, int $schedule = null)
    {
        $message = "Someone liked your post";
        $deviceId = $this->userRepository->getDeviceIdByUserId($userId);
        return $this->sendNotification($message, $deviceId, $url, $data, $buttons, $schedule);
    }

    public function sendFollowedNotification(int $userId = null, string $url = null, array $data = null, array $buttons = null, int $schedule = null)
    {
        $message = "Someone started following you";
        $deviceId = $this->userRepository->getDeviceIdByUserId($userId);
        return $this->sendNotification($message, $deviceId, $url, $data, $buttons, $schedule);
    }

    public function sendNotification(string $message = null, string $deviceId = null, string $url = null, array $data = null, array $buttons = null, int $schedule = null)
    {
        return OneSignal::sendNotificationToUser($message, $deviceId, $url, $data, $buttons, $schedule);
    }
}
