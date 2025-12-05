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
        $notificationModel = new NotificationModel();

        $course = $this->courseModel->find($courseId);
        if (! $course) {
            return $this->response->setStatusCode(404)
                ->setJSON(['success' => false, 'message' => 'Course not found.']);
        }

        $teacherId = (int) ($course['teacher_id'] ?? 0);
        $studentName = (string) ($session->get('name') ?? 'Student');

        $existing = $enrollmentModel->where(['user_id' => $userId, 'course_id' => $courseId])->first();
        if ($existing) {
            if ($existing['status'] === 'declined') {
                $enrollmentModel->update($existing['id'], [
                    'status' => 'pending',
                    'teacher_id' => $teacherId ?: null,
                    'enrollment_date' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                if ($teacherId > 0) {
                    $notificationModel->createNotification(
                        $teacherId,
                        "$studentName requested to enroll again in '{$course['title']}'."
                    );
                }

                return $this->response->setJSON([
                    'success' => true,
                    'status' => 'pending',
                    'message' => 'Enrollment request resubmitted and pending approval.',
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'alreadyEnrolled' => true,
                'status' => $existing['status'],
                'message' => $existing['status'] === 'pending' ? 'Enrollment request is already pending approval.' : 'Already enrolled.',
            ]);
        }

        $insertData = [
            'user_id' => $userId,
            'course_id' => $courseId,
            'teacher_id' => $teacherId ?: null,
            'status' => 'pending',
            'enrollment_date' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $inserted = $enrollmentModel->insert($insertData, true);
        if ($inserted === false) {
            return $this->response->setStatusCode(500)
                ->setJSON(['success' => false, 'message' => 'Enrollment failed.']);
        }

        $courseTitle = $course ? $course['title'] : 'Course';

        $notificationModel->createNotification(
            $userId,
            "Your enrollment request for '{$courseTitle}' has been sent and is pending approval."
        );

        if ($teacherId > 0) {
            $notificationModel->createNotification(
                $teacherId,
                "$studentName requested to enroll in '{$courseTitle}'."
            );
        }

        return $this->response->setJSON([
            'success' => true,
            'status' => 'pending',
            'message' => 'Enrollment request submitted and pending approval.',
        ]);
    }

    // --------------------------
    // LAB 9: Search Function
    // --------------------------
    public function search()
    {
        $searchTerm = trim((string) $this->request->getGet('search_term'));

        if ($searchTerm === '') {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([]);
            }

            return view('Courses/search', [
                'courses'    => [],
                'searchTerm' => $searchTerm,
                'error'      => 'Please enter a search term.',
            ]);
        }

        $courses = $this->courseModel
            ->groupStart()
            ->like('title', $searchTerm)
            ->orLike('description', $searchTerm)
            ->groupEnd()
            ->findAll();

        if ($this->request->isAJAX()) {
            return $this->response->setJSON($courses);
        }

        return view('Courses/search', [
            'courses' => $courses,
            'searchTerm' => $searchTerm
        ]);
    }

    public function updateEnrollmentStatus(int $enrollmentId)
    {
        $session = session();
        if (! $session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $status = strtolower((string) $this->request->getPost('status'));
        if (! in_array($status, ['accepted', 'declined'], true)) {
            return redirect()->back()->with('error', 'Invalid status provided.');
        }

        $enrollmentModel = new EnrollmentModel();
        $notificationModel = new NotificationModel();
        $enrollment = $enrollmentModel->findWithCourseAndStudent($enrollmentId);

        if (! $enrollment || (int) ($enrollment['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->back()->with('error', 'Enrollment not found or not assigned to you.');
        }

        $enrollmentModel->update($enrollmentId, [
            'status' => $status,
            'teacher_id' => $teacherId,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $notificationModel->createNotification(
            (int) $enrollment['user_id'],
            "Your enrollment for '{$enrollment['course_title']}' was {$status} by the teacher."
        );

        return redirect()->back()->with('success', 'Enrollment status updated.');
    }

    public function removeEnrollment(int $enrollmentId)
    {
        $session = session();
        if (! $session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $enrollmentModel = new EnrollmentModel();
        $notificationModel = new NotificationModel();
        $enrollment = $enrollmentModel->findWithCourseAndStudent($enrollmentId);

        if (! $enrollment || (int) ($enrollment['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->back()->with('error', 'Enrollment not found or not assigned to you.');
        }

        $enrollmentModel->delete($enrollmentId);

        $notificationModel->createNotification(
            (int) $enrollment['user_id'],
            "You were removed from '{$enrollment['course_title']}'."
        );

        return redirect()->back()->with('success', 'Enrollment removed.');
    }

    public function updateCourse(int $courseId)
    {
        $session = session();
        if (! $session->get('isLoggedIn') || strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course || (int) ($course['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->back()->with('error', 'Course not found or not assigned to you.');
        }

        $data = [
            'title' => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'school_year' => trim((string) $this->request->getPost('school_year')),
        ];

        $this->courseModel->update($courseId, $data);

        return redirect()->back()->with('success', 'Course details updated.');
    }
}
