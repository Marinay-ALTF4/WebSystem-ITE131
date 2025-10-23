<?php

namespace App\Models;
use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = 'notifications';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'message', 'is_read', 'created_at'];
    protected $useTimestamps = false;

    /**
     * Get count of unread notifications for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Get latest notifications for a user (limit optional)
     */
    public function getNotificationsForUser($userId, $limit = 5)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get only unread notifications
     */
    public function getUnread($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll($limit);
    }

    /**
     * Mark a single notification as read
     */
    public function markAsRead($notificationId)
    {
        return $this->update($notificationId, ['is_read' => 1]);
    }

    /**
     * Mark multiple notifications as read
     * @param array $ids Notification IDs to mark as read; if empty, mark all unread
     */
    public function markAsReadBulk($userId, $ids = [])
    {
        $builder = $this->builder();
        $builder->where('user_id', $userId)
                ->where('is_read', 0);

        if (!empty($ids)) {
            $builder->whereIn('id', $ids);
        }

        return $builder->update(['is_read' => 1]);
    }

    /**
     * Create a new notification for a user
     */
    public function createNotification($userId, $message)
    {
        $data = [
            'user_id'    => $userId,
            'message'    => $message,
            'is_read'    => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        return $this->insert($data);
    }
}
