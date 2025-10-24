<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\CLIRequest;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;
use App\Models\NotificationModel; // added to fetch notifications

/**
 * Class BaseController
 *
 * BaseController provides a convenient place for loading components
 * and performing functions that are needed by all your controllers.
 * Extend this class in any new controllers:
 *     class Home extends BaseController
 *
 * For security be sure to declare any new methods as protected or private.
 */
abstract class BaseController extends Controller
{
    /**
     * Instance of the main Request object.
     *
     * @var CLIRequest|IncomingRequest
     */
    protected $request;

    /**
     * An array of helpers to be loaded automatically upon
     * class instantiation. These helpers will be available
     * to all other controllers that extend BaseController.
     *
     * @var list<string>
     */
    protected $helpers = [];

    /**
     * Be sure to declare properties for any property fetch you initialized.
     * The creation of dynamic property is deprecated in PHP 8.2.
     */
    // protected $session;

    // Added properties for session and notifications
    protected $session;
    protected $notificationCount = 0;

    /**
     * @return void
     */
    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        // Do Not Edit This Line
        parent::initController($request, $response, $logger);

        // Preload any models, libraries, etc, here.

        // E.g.: $this->session = service('session');
        $this->session = session();

        // Fetch unread notifications if user is logged in
        if ($this->session->get('isLoggedIn')) {
            try {
                $notificationModel = new NotificationModel();
                // Make sure your session key matches login (userID or user_id)
                $userId = $this->session->get('userID');
                if ($userId) {
                    $this->notificationCount = $notificationModel->getUnreadCount($userId);
                }
            } catch (\Exception $e) {
                // If notifications table doesn't exist or other error, set count to 0
                log_message('error', 'Failed to load notification count: ' . $e->getMessage());
                $this->notificationCount = 0;
            }
        }
    }

    /**
     * Override the view method to automatically include notification data
     */
    protected function view(string $name, array $data = [], array $options = []): string
    {
        // Automatically add notification count to all views
        $data['notificationCount'] = $this->notificationCount;
        return view($name, $data, $options);
    }

    /**
     * Optional helper to load view with notifications count (legacy method)
     */
    public function displayNotif(string $view, array $data = []): string
    {
        $data['notificationCount'] = $this->notificationCount;
        return view('templates/header', $data) . view($view, $data);
    }
}
