<?php

namespace App\Controllers;

use App\Models\EnrollmentModel;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Controller;

class Course extends Controller
{
    
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

        return $this->response->setJSON(['success' => true, 'message' => 'Enrolled successfully.']);
    }
}


