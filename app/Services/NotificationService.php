<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;

class NotificationService
{
    /**
     * Create a new notification for a user
     */
    public function createNotification(User $user, $type, $title, $message, $data = [])
    {
        return Notification::create([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data,
            'is_read' => false,
        ]);
    }

    /**
     * Mark a notification as read
     */
    public function markAsRead(Notification $notification)
    {
        return $notification->update(['is_read' => true, 'read_at' => now()]);
    }

    /**
     * Get unread notifications for a user
     */
    public function getUnreadNotifications(User $user)
    {
        return $user->notifications()->where('is_read', false)->get();
    }

    /**
     * Get all notifications for a user
     */
    public function getAllNotifications(User $user)
    {
        return $user->notifications()->orderBy('created_at', 'desc')->get();
    }

    /**
     * Send notification to multiple users
     */
    public function sendToMultipleUsers($users, $type, $title, $message, $data = [])
    {
        $notifications = [];

        foreach ($users as $user) {
            $notifications[] = $this->createNotification($user, $type, $title, $message, $data);
        }

        return $notifications;
    }
}
