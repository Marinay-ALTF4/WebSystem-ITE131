<?php

namespace App\Controllers;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;

    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        helper('url');
    }

    public function get()
    {
        try {
            $session = session();
            if (!$session->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'Not logged in']);
            }

            $userId = $session->get('userID');
            
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'User ID not found']);
            }
            
            $notifications = $this->notificationModel->getNotificationsForUser($userId, 10);
            $unreadCount = $this->notificationModel->getUnreadCount($userId);
            
            return $this->response->setJSON([
                'unread_count' => $unreadCount,
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Notification error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to load notifications',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function count()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)->setJSON(['error' => 'Not logged in']);
        }

        $userId = $session->get('userID');
        $unreadCount = $this->notificationModel->getUnreadCount($userId);
        
        return $this->response->setJSON(['unread_count' => $unreadCount]);
    }

    public function index()
    {
        return view('notifications/index');
    }

    public function mark_as_read($id)
    {
        try {
            $session = session();
            if (!$session->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'Not logged in']);
            }

            $userId = $session->get('userID');
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'User ID not found']);
            }

            // Verify the notification belongs to the current user
            $notification = $this->notificationModel->find($id);
            if (!$notification || $notification['user_id'] != $userId) {
                return $this->response->setStatusCode(403)->setJSON(['error' => 'Notification not found or access denied']);
            }

            $success = $this->notificationModel->markAsRead($id);
            return $this->response->setJSON(['success' => $success]);
        } catch (\Exception $e) {
            log_message('error', 'Mark as read error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to mark notification as read',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function mark_all()
    {
        try {
            $session = session();
            if (!$session->get('isLoggedIn')) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'Not logged in']);
            }

            $userId = $session->get('userID');
            
            if (!$userId) {
                return $this->response->setStatusCode(401)->setJSON(['error' => 'User ID not found']);
            }
            
            $success = $this->notificationModel->markAsReadBulk($userId);
            
            return $this->response->setJSON([
                'success' => $success !== false,
                'message' => $success !== false ? 'All notifications marked as read' : 'Failed to mark notifications as read'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Mark all as read error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Failed to mark all notifications as read',
                'message' => $e->getMessage()
            ]);
        }
    }
}
