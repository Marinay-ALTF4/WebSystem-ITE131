<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use App\Models\NotificationModel;
use App\Models\CourseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Course extends BaseController
{
    protected $courseModel;

    public function __construct()
    {
        $this->courseModel = new CourseModel();
    }

    public function enroll(): ResponseInterface
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'message' => 'Please log in first.']);
        }

        $userId = (int) $session->get('userID');
        $courseId = (int) ($this->request->getPost('course_id') ?? 0);

        if ($courseId <= 0) {
            return $this->response->setStatusCode(400)
                ->setJSON(['success' => false, 'message' => 'Invalid course_id.']);
        }

        $enrollmentModel = new EnrollmentModel();

        $existing = $enrollmentModel->where(['user_id' => $userId, 'course_id' => $courseId])->first();
        if ($existing) {
            return $this->response->setJSON(['success' => true, 'alreadyEnrolled' => true, 'message' => 'Already enrolled.']);
        }

        $insertData = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'enrollment_date' => date('Y-m-d H:i:s'),
        ];

        $inserted = $enrollmentModel->insert($insertData, true);
        if ($inserted === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Enrollment failed.']);
        }

        // Notification
        $notificationModel = new NotificationModel();
        $course = $this->courseModel->find($courseId);
        $courseTitle = $course ? $course['title'] : 'Course';

        $notificationModel->createNotification(
            $userId,
            "You have successfully enrolled in '{$courseTitle}'"
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Enrolled successfully.',
        ]);
    }

    // --------------------------
    // LAB 9: Search Function
    // --------------------------
    public function search()
    {
        $searchTerm = $this->request->getGet('search_term');

        if (!empty($searchTerm)) {
            $this->courseModel->like('title', $searchTerm);
            $this->courseModel->orLike('description', $searchTerm);
        }

        $courses = $this->courseModel->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        return view('Courses/search', [
            'courses' => $courses,
            'searchTerm' => $searchTerm
        ]);
    }
}
