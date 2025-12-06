<?php

namespace App\Controllers;

use App\Models\UserModel; 
use App\Models\EnrollmentModel;
use CodeIgniter\Controller;
use App\Models\CourseModel;

class Auth extends BaseController
{

    public function register()
    {
        helper(['form']);
        $session = session();

        if ($this->request->getMethod() === 'POST') {
            $rules = [
                'name' => [
                    'rules'  => 'required|min_length[3]|regex_match[/^[A-Za-z][A-Za-z\s\.\'\-]*$/]',
                    'errors' => [
                        'regex_match' => 'Name may only contain letters, spaces, periods (no numbers or symbols).',
                    ],
                ],
                'email' => [
                    'rules'  => 'required|valid_email|is_unique[users.email]',
                    'errors' => [
                        'is_unique' => 'Email is already registered.',
                    ],
                ],
                'password'         => 'required|min_length[6]',
                'password_confirm' => 'matches[password]'
            ];

            if ($this->validate($rules)) {
                $userModel = new UserModel();
                $userModel->save([
                    'name'     => $this->request->getVar('name'),
                    'email'    => $this->request->getVar('email'),
                    'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                    'role'     => 'student' // Default role sa register
                ]);

                return redirect()->to('/login')->with('success', 'Registration Success. Proceed to login.');
            }

            if ($this->validator->hasError('email')) {
                $session->setFlashdata('error', $this->validator->getError('email'));
            }

            return view('auth/register', ['validation' => $this->validator]);
        }

        return view('auth/register');
    }




    public function login()
    {
        helper(['form']);

        if ($this->request->getMethod() === 'POST') {
            $session   = session();
            $userModel = new UserModel();

            $rules = [
                'email'    => 'required|valid_email',
                'password' => 'required|min_length[6]'
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            $user = $userModel->where('email', $this->request->getVar('email'))->first();
            if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
                $session->set([
                    'userID'    => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'],
                    'isLoggedIn'=> true
                ]);
                $session->setFlashdata('success', 'Welcome ' . $user['name']);
                return redirect()->to('dashboard');
            }

            $session->setFlashdata('error', 'Invalid login credentials');
            return redirect()->back();
        }

        return view('auth/login');
    }




    public function logout()
    {
        session()->destroy();
        return redirect()->to('login');
    }




    public function dashboard()
{
    $session = session();

    if (! $session->get('isLoggedIn')) {
        return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
    }

    $role = strtolower((string) $session->get('role'));

    $userModel = new UserModel();
    $courseModel = new \App\Models\CourseModel(); 
    $notificationModel = new \App\Models\NotificationModel();

    $data = [
        'notifications' => $notificationModel->getNotificationsForUser((int)$session->get('userID'))
    ];

    switch ($role) {
        case 'admin':
            $data['usersCount'] = $userModel->countAllResults();
            $data['recentUsers'] = $userModel->orderBy('id', 'DESC')->limit(5)->find();
            // Get courses with teacher names
            $data['courses'] = $courseModel->select('courses.*, users.name AS teacher_name')
                ->join('users', 'users.id = courses.teacher_id', 'left')
                ->findAll();
            // Get all teachers for admin to select when adding courses
            $data['teachers'] = $userModel->where('role', 'teacher')->orderBy('name', 'ASC')->findAll();
            break;

        case 'teacher':
            $teacherId = (int) $session->get('userID');
            $enrollmentModel = new EnrollmentModel();
            $data['courses'] = $courseModel->where('teacher_id', $teacherId)->findAll();
            // Show accepted, pending, and declined enrollments (declined shows as "Dropped" and can be updated)
            $allEnrollments = $enrollmentModel->getEnrollmentsForTeacher($teacherId);
            $data['enrollments'] = array_filter($allEnrollments, function($enrollment) {
                $status = strtolower($enrollment['status'] ?? '');
                // Show all statuses except those that were never accepted (initial declines are removed)
                // But we'll show all to allow updates - the view will handle display
                return in_array($status, ['accepted', 'pending', 'declined'], true);
            });
            $data['pendingEnrollments'] = $enrollmentModel->getEnrollmentsForTeacher($teacherId, 'pending');
            break;

        case 'student':
        default:
            $userId = (int) $session->get('userID');
            
            // Profile
            $data['profile'] = $userModel->find($userId);
        
            // Enrolled courses
            $enrollmentModel = new EnrollmentModel();
            $data['courses'] = $enrollmentModel->getUserEnrollments($userId, 'accepted');
            $data['pendingEnrollments'] = $enrollmentModel->getUserEnrollments($userId, 'pending');
        
            // Materials for the student's courses
            $materialModel = new \App\Models\MaterialModel();
            $data['materials'] = [];

            foreach ($data['courses'] as $course) {
                $courseMaterials = $materialModel->where('course_id', $course['course_id'])->findAll();
                $data['materials'] = array_merge($data['materials'], $courseMaterials);
            }
            break;
    }

    return view('auth/dashboard', [
        'role' => $role,
        'data' => $data,
    ]);
}

    
    

public function studentCourse()
{
    $session = session();

    if (! $session->get('isLoggedIn')) {
        return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
    }

    $role = strtolower((string) $session->get('role'));

    if ($role !== 'student') {
        return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
    }

    $userId = (int)$session->get('userID');
    $userModel = new UserModel();
    $enrollmentModel = new EnrollmentModel();
    $notificationModel = new \App\Models\NotificationModel();

    $data = [
        'enrolledCourses'  => $enrollmentModel->getUserEnrollments($userId, 'accepted'),
        'pendingCourses'   => $enrollmentModel->getUserEnrollments($userId, 'pending'),
        'availableCourses' => $enrollmentModel->getAvailableCoursesForUser($userId),
        'notifications'    => $notificationModel->getNotificationsForUser($userId)
    ];

    return view('auth/studentCourse', [
        'role' => $role,
        'data' => $data,
    ]);
}
    
public function addCourse()
{
    $session = session();
    $role = strtolower((string) $session->get('role'));
    
    if (! $session->get('isLoggedIn') || !in_array($role, ['teacher', 'admin'], true)) {
        return redirect()->to(base_url('dashboard'))->with('error', 'Access denied.');
    }

    $title = $this->request->getPost('title');
    $description = $this->request->getPost('description');
    $semester = $this->request->getPost('semester');
    $classTime = $this->request->getPost('class_time');
    $schoolYear = $this->request->getPost('school_year');
    
    // If admin, get teacher_id from form. If teacher, use their own ID.
    if ($role === 'admin') {
        $teacherId = (int) $this->request->getPost('teacher_id');
        if ($teacherId <= 0) {
            return redirect()->back()->with('error', 'Please select a teacher for this course.');
        }
    } else {
        $teacherId = (int) $session->get('userID');
    }

    $courseModel = new CourseModel();
    $courseModel->save([
        'title' => $title,
        'description' => $description,
        'teacher_id' => $teacherId,
        'semester' => $semester,
        'school_year' => $schoolYear,
        'class_time' => $classTime
    ]);

    return redirect()->back()->with('success', 'Course added successfully.');
}

}