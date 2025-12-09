<?php

namespace App\Controllers;

use App\Models\AssignmentModel;
use App\Models\CourseModel;
use App\Models\EnrollmentModel;
use App\Models\MaterialModel;
use App\Models\NotificationModel;
use App\Models\UserModel;
use App\Models\AssignmentSubmissionModel;
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
        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
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

        $oldStatus = strtolower($enrollment['status'] ?? '');
        $pendingDeclineRemoval = $oldStatus === 'pending' && $status === 'declined';

        if ($pendingDeclineRemoval) {
            // Remove pending requests once declined so they disappear from the table.
            $enrollmentModel->delete($enrollmentId);
        } else {
            $enrollmentModel->update($enrollmentId, [
                'status' => $status,
                'teacher_id' => $teacherId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // Create appropriate notification message based on status change
        $statusLabel = $status === 'accepted' ? 'enrolled' : 'dropped';
        $message = "Your enrollment for '{$enrollment['course_title']}' status has been updated to {$statusLabel}.";
        
        // If declining a pending enrollment, use different message
        if ($oldStatus === 'pending' && $status === 'declined') {
            $message = "Your enrollment request for '{$enrollment['course_title']}' was declined by the teacher.";
        } elseif ($oldStatus === 'pending' && $status === 'accepted') {
            $message = "Your enrollment request for '{$enrollment['course_title']}' was accepted. You are now enrolled.";
        } elseif ($oldStatus === 'accepted' && $status === 'declined') {
            $message = "You have been dropped from '{$enrollment['course_title']}' by the teacher.";
        } elseif ($oldStatus === 'accepted' && $status === 'accepted') {
            $message = "Your enrollment status for '{$enrollment['course_title']}' remains enrolled.";
        }

        $notificationModel->createNotification(
            (int) $enrollment['user_id'],
            $message
        );

        $successMessage = $pendingDeclineRemoval
            ? 'Enrollment request declined and removed.'
            : ($status === 'accepted' ? 'Student enrolled successfully.' : 'Student dropped successfully.');
        return redirect()->back()->with('success', $successMessage);
    }

    public function removeEnrollment(int $enrollmentId)
    {
        $session = session();
        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
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
        $role = strtolower((string) $session->get('role'));
        
        if (! $session->get('isLoggedIn') || !in_array($role, ['teacher', 'admin'], true)) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $course = $this->courseModel->find($courseId);
        if (! $course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        // If teacher, check if course belongs to them. Admin can edit any course.
        if ($role === 'teacher') {
            $teacherId = (int) $session->get('userID');
            if ((int) ($course['teacher_id'] ?? 0) !== $teacherId) {
                return redirect()->back()->with('error', 'Course not found or not assigned to you.');
            }
        }

        $semesterInput = trim((string) $this->request->getPost('semester'));
        $termInput = trim((string) $this->request->getPost('term'));

        $semesterLabel = $semesterInput;
        if ($termInput !== '') {
            $semesterLabel = $semesterInput !== '' ? $semesterInput . ' / ' . $termInput : $termInput;
        }

        if ($semesterLabel === '') {
            return redirect()->back()->with('error', 'Please select a semester or term.');
        }

        $data = [
            'title' => trim((string) $this->request->getPost('title')),
            'description' => trim((string) $this->request->getPost('description')),
            'semester' => $semesterLabel,
            'school_year' => trim((string) $this->request->getPost('school_year')),
            'class_time' => trim((string) $this->request->getPost('class_time')),
        ];

        $this->courseModel->update($courseId, $data);

        return redirect()->back()->with('success', 'Course details updated.');
    }

    public function teacherCourse(int $courseId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course || (int) ($course['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found or not assigned to you.');
        }

        $materialModel = new MaterialModel();
        $materials = $materialModel->where('course_id', $courseId)->findAll();

        $enrollmentModel = new EnrollmentModel();
        $enrollments = $enrollmentModel
            ->select('enrollments.*, users.name AS student_name, users.email AS student_email')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('enrollments.course_id', $courseId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();

        $assignmentModel = new AssignmentModel();
        $assignments = $assignmentModel
            ->where('course_id', $courseId)
            ->orderBy('due_date', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return view('teacher/course_dashboard', [
            'course' => $course,
            'materials' => $materials,
            'enrollments' => $enrollments,
            'assignments' => $assignments,
        ]);
    }

    public function deleteCourse(int $courseId)
    {
        $session = session();
        $role = strtolower((string) $session->get('role'));

        if (! $session->get('isLoggedIn') || $role !== 'admin') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $course = $this->courseModel->find($courseId);
        if (! $course) {
            return redirect()->back()->with('error', 'Course not found.');
        }

        $materialModel = new MaterialModel();
        $enrollmentModel = new EnrollmentModel();
        $assignmentModel = new AssignmentModel();
        $submissionModel = new AssignmentSubmissionModel();

        $materials = $materialModel->where('course_id', $courseId)->findAll();
        foreach ($materials as $material) {
            $path = FCPATH . $material['file_path'];
            if (! empty($material['file_path']) && is_file($path)) {
                @unlink($path); // best-effort cleanup
            }
        }

        $materialModel->where('course_id', $courseId)->delete();
        $enrollmentModel->where('course_id', $courseId)->delete();
        $assignmentModel->where('course_id', $courseId)->delete();
        $submissionModel->where('course_id', $courseId)->delete();
        $this->courseModel->delete($courseId);

        return redirect()->back()->with('success', 'Course deleted successfully.');
    }

    public function studentCourseView(int $courseId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'student') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $studentId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found.');
        }

        $enrollmentModel = new EnrollmentModel();
        $isEnrolled = $enrollmentModel
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'accepted')
            ->first();

        if (! $isEnrolled) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not enrolled in this course.');
        }

        $assignmentModel = new AssignmentModel();
        $assignments = $assignmentModel
            ->where('course_id', $courseId)
            ->orderBy('due_date', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Attach this student's submission (including score/feedback) to each assignment
        $submissionModel = new AssignmentSubmissionModel();
        $studentSubmissions = $submissionModel
            ->where('course_id', $courseId)
            ->where('student_id', $studentId)
            ->findAll();

        $submissionsByAssignment = [];
        foreach ($studentSubmissions as $sub) {
            $submissionsByAssignment[(int) $sub['assignment_id']] = $sub;
        }

        foreach ($assignments as &$assignment) {
            $aid = (int) ($assignment['id'] ?? 0);
            $assignment['submission'] = $submissionsByAssignment[$aid] ?? null;
        }
        unset($assignment);

        $classmates = $enrollmentModel
            ->select('users.id, users.name, users.email, users.role')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('enrollments.course_id', $courseId)
            ->where('enrollments.status', 'accepted')
            ->findAll();

        $userModel = new UserModel();
        $teacher = $userModel->find((int) ($course['teacher_id'] ?? 0));

        return view('student/course_view', [
            'course' => $course,
            'assignments' => $assignments,
            'classmates' => $classmates,
            'teacher' => $teacher,
        ]);
    }

    public function enrollExistingStudent(int $courseId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course || (int) ($course['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found or not assigned to you.');
        }

        $emailInput = strtolower(trim((string) $this->request->getPost('student_email')));
        if ($emailInput === '') {
            return redirect()->back()->with('error', 'Please provide a student email.');
        }

        $userModel = new UserModel();
        $student = $userModel->where('email', $emailInput)->where('role', 'student')->first();

        if (! $student) {
            return redirect()->back()->with('error', 'Student account not found.');
        }

        $enrollmentModel = new EnrollmentModel();

        $activeEnrollment = $enrollmentModel
            ->where('user_id', (int) $student['id'])
            ->where('course_id', $courseId)
            ->whereIn('status', ['pending', 'accepted'])
            ->first();

        if ($activeEnrollment) {
            return redirect()->back()->with('error', 'This student already has an active or pending enrollment.');
        }

        $existingDeclined = $enrollmentModel
            ->where('user_id', (int) $student['id'])
            ->where('course_id', $courseId)
            ->where('status', 'declined')
            ->first();

        $now = date('Y-m-d H:i:s');
        if ($existingDeclined) {
            $enrollmentModel->update((int) $existingDeclined['id'], [
                'status' => 'accepted',
                'teacher_id' => $teacherId,
                'updated_at' => $now,
            ]);
            $enrollmentId = (int) $existingDeclined['id'];
        } else {
            $enrollmentId = $enrollmentModel->insert([
                'user_id' => (int) $student['id'],
                'course_id' => $courseId,
                'teacher_id' => $teacherId,
                'status' => 'accepted',
                'enrollment_date' => $now,
                'updated_at' => $now,
            ], true);
        }

        if (! $enrollmentId) {
            return redirect()->back()->with('error', 'Unable to enroll student. Please try again.');
        }

        $notificationModel = new NotificationModel();
        $notificationModel->createNotification(
            (int) $student['id'],
            "You have been enrolled in '{$course['title']}' by your teacher."
        );

        return redirect()->back()->with('success', 'Student enrolled successfully.');
    }

    public function createAssignment(int $courseId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course || (int) ($course['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found or not assigned to you.');
        }

        $title = trim((string) $this->request->getPost('title'));
        $pointsInput = trim((string) $this->request->getPost('points'));
        $assignmentType = trim((string) $this->request->getPost('assignment_type'));
        $submitType = trim((string) $this->request->getPost('submit_type'));
        $attempts = (string) $this->request->getPost('attempts');
        $dueDate = $this->request->getPost('due_date');
        $availableAfter = $this->request->getPost('available_after');

        if ($title === '' || $assignmentType === '' || $submitType === '') {
            return redirect()->back()->with('error', 'Please fill in all required fields.');
        }

        $points = null;
        if ($pointsInput !== '') {
            if (! ctype_digit($pointsInput) || (int) $pointsInput <= 0) {
                return redirect()->back()->with('error', 'Points must be a positive number.');
            }
            $points = (int) $pointsInput;
        }

        $attemptsAllowed = null;
        if ($attempts !== 'unlimited') {
            $attemptValue = (int) $attempts;
            if ($attemptValue < 1 || $attemptValue > 3) {
                return redirect()->back()->with('error', 'Invalid attempt selection.');
            }
            $attemptsAllowed = $attemptValue;
        }

        $assignmentModel = new AssignmentModel();
        $now = date('Y-m-d H:i:s');

        $assignmentId = $assignmentModel->insert([
            'course_id' => $courseId,
            'teacher_id' => $teacherId,
            'title' => $title,
            'description' => trim((string) $this->request->getPost('description')),
            'points' => $points,
            'assignment_type' => $assignmentType,
            'submit_type' => $submitType,
            'attempts_allowed' => $attemptsAllowed,
            'due_date' => $dueDate ?: null,
            'available_after' => $availableAfter ?: null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        // Notify enrolled students of the new assignment
        try {
            $enrollmentModel = new EnrollmentModel();
            $notificationModel = new NotificationModel();
            $courseTitle = $course['title'] ?? 'Course';

            $students = $enrollmentModel
                ->where('course_id', $courseId)
                ->where('status', 'accepted')
                ->findAll();

            foreach ($students as $student) {
                $notificationModel->createNotification(
                    (int) $student['user_id'],
                    "New assignment '{$title}' posted in '{$courseTitle}'."
                );
            }
        } catch (\Throwable $e) {
            log_message('error', 'Failed to send assignment notifications: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Assignment created.');
    }

    public function teacherAssignment(int $courseId, int $assignmentId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $course = $this->courseModel->find($courseId);

        if (! $course || (int) ($course['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Course not found or not assigned to you.');
        }

        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->where('course_id', $courseId)->find($assignmentId);

        if (! $assignment) {
            return redirect()->to(base_url('teacher/course/' . $courseId))->with('error', 'Assignment not found.');
        }

        $submissionModel = new AssignmentSubmissionModel();
        $enrollmentModel = new EnrollmentModel();

        $submissions = $submissionModel
            ->select('assignment_submissions.*, users.name, users.email')
            ->join('users', 'users.id = assignment_submissions.student_id', 'left')
            ->where('assignment_submissions.assignment_id', $assignmentId)
            ->findAll();

        $enrolledStudents = $enrollmentModel
            ->select('users.id, users.name, users.email')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('enrollments.course_id', $courseId)
            ->where('enrollments.status', 'accepted')
            ->findAll();

        $submissionsByStudent = [];
        foreach ($submissions as $s) {
            $submissionsByStudent[(int) $s['student_id']] = $s;
        }

        $now = time();
        $dueTs = $assignment['due_date'] ? strtotime($assignment['due_date']) : null;

        $statuses = [];
        foreach ($enrolledStudents as $stu) {
            $sid = (int) $stu['id'];
            if (isset($submissionsByStudent[$sid])) {
                $sub = $submissionsByStudent[$sid];
                $submittedTs = $sub['submitted_at'] ? strtotime($sub['submitted_at']) : null;
                $late = $dueTs && $submittedTs && $submittedTs > $dueTs;
                $statuses[] = [
                    'student' => $stu,
                    'submission' => $sub,
                    'status' => $late ? 'late' : 'submitted',
                ];
            } else {
                $status = ($dueTs && $now > $dueTs) ? 'did_not_pass' : 'pending';
                $statuses[] = [
                    'student' => $stu,
                    'submission' => null,
                    'status' => $status,
                ];
            }
        }

        return view('teacher/assignment_detail', [
            'course' => $course,
            'assignment' => $assignment,
            'statuses' => $statuses,
        ]);
    }

    public function viewSubmission(int $assignmentId, int $studentId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (! $assignment || (int) ($assignment['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Assignment not found or not assigned to you.');
        }

        $submissionModel = new AssignmentSubmissionModel();
        $submission = $submissionModel
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if (! $submission) {
            return redirect()->to(base_url('teacher/course/' . ($assignment['course_id'] ?? 0) . '/assignments/' . $assignmentId))
                ->with('error', 'Submission not found for this student.');
        }

        $userModel = new UserModel();
        $student = $userModel->find($studentId);

        return view('teacher/submission_view', [
            'assignment' => $assignment,
            'submission' => $submission,
            'student' => $student ?? ['id' => $studentId],
        ]);
    }

    public function gradeSubmission(int $assignmentId, int $studentId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'teacher') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $teacherId = (int) $session->get('userID');
        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (! $assignment || (int) ($assignment['teacher_id'] ?? 0) !== $teacherId) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Assignment not found or not assigned to you.');
        }

        $submissionModel = new AssignmentSubmissionModel();
        $submission = $submissionModel
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        if (! $submission) {
            return redirect()->to(base_url('teacher/course/' . ($assignment['course_id'] ?? 0) . '/assignments/' . $assignmentId))
                ->with('error', 'Submission not found for this student.');
        }

        $scoreInput = trim((string) $this->request->getPost('score'));
        $feedback = trim((string) $this->request->getPost('feedback'));

        $score = null;
        if ($scoreInput !== '') {
            if (! is_numeric($scoreInput)) {
                return redirect()->back()->with('error', 'Score must be a number.');
            }
            $score = (int) $scoreInput;
            if ($score < 0) {
                return redirect()->back()->with('error', 'Score cannot be negative.');
            }
            if (! empty($assignment['points']) && $score > (int) $assignment['points']) {
                return redirect()->back()->with('error', 'Score cannot exceed assignment points.');
            }
        }

        $submissionModel->update((int) $submission['id'], [
            'score' => $score,
            'feedback' => $feedback === '' ? null : $feedback,
            'graded_by' => $teacherId,
            'graded_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to(base_url('teacher/assignments/' . $assignmentId . '/submissions/' . $studentId))
            ->with('success', 'Grade saved.');
    }

    public function submitAssignment(int $assignmentId)
    {
        $session = session();

        if (! $session->get('isLoggedIn') || strtolower((string) $session->get('role')) !== 'student') {
            return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
        }

        $studentId = (int) $session->get('userID');
        $assignmentModel = new AssignmentModel();
        $assignment = $assignmentModel->find($assignmentId);

        if (! $assignment) {
            return redirect()->to(base_url('dashboard'))->with('error', 'Assignment not found.');
        }

        $courseId = (int) ($assignment['course_id'] ?? 0);
        $enrollmentModel = new EnrollmentModel();
        $enrolled = $enrollmentModel
            ->where('user_id', $studentId)
            ->where('course_id', $courseId)
            ->where('status', 'accepted')
            ->first();

        if (! $enrolled) {
            return redirect()->to(base_url('dashboard'))->with('error', 'You are not enrolled in this course.');
        }

        $submitType = strtolower((string) ($assignment['submit_type'] ?? 'text'));
        $content = null;
        $filePath = null;

        if ($submitType === 'text') {
            $content = trim((string) $this->request->getPost('content'));
            if ($content === '') {
                return redirect()->back()->with('error', 'Please enter your answer.');
            }
        } else {
            $file = $this->request->getFile('submission_file');
            if (! $file || ! $file->isValid()) {
                return redirect()->back()->with('error', 'Please upload a file.');
            }
            $newName = $file->getRandomName();
            $uploadPath = WRITEPATH . 'uploads/assignments/';
            $file->move($uploadPath, $newName);
            $filePath = 'writable/uploads/assignments/' . $newName;
        }

        $submissionModel = new AssignmentSubmissionModel();
        $now = date('Y-m-d H:i:s');

        $existing = $submissionModel
            ->where('assignment_id', $assignmentId)
            ->where('student_id', $studentId)
            ->first();

        $data = [
            'assignment_id' => $assignmentId,
            'student_id' => $studentId,
            'course_id' => $courseId,
            'content' => $content,
            'file_path' => $filePath,
            'submitted_at' => $now,
            'updated_at' => $now,
        ];

        if ($existing) {
            $submissionModel->update((int) $existing['id'], $data);
        } else {
            $data['created_at'] = $now;
            $submissionModel->insert($data);
        }

        return redirect()->back()->with('success', 'Submission uploaded.');
    }
}

