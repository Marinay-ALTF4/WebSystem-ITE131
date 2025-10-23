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
        $session = session();
        $userId = $session->get('userID');

        $notificationModel = new NotificationModel();
        $unreadCount = $notificationModel->getUnreadCount($userId);

        return $this->response->setJSON(['unreadCount' => $unreadCount]);
    }


    public function mark_as_read($id)
    {
        $success = $this->notificationModel->markAsRead($id);
        return $this->response->setJSON(['success' => $success]);
    }
}
