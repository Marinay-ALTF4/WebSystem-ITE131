<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class AdminController extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }

    // Add a new user
    public function addUser()
    {
        helper(['form']);
        $data = $this->request->getPost();

        // Validate input
        if (!$data || empty($data['name']) || empty($data['email']) || empty($data['password']) || empty($data['role'])) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        // Validate name - no special characters or numbers
        $namePattern = '/^[A-Za-z][A-Za-z\s\.\'\-]*$/';
        if (!preg_match($namePattern, trim($data['name']))) {
            return redirect()->back()->with('error', 'Invalid name format. Name must start with a letter and can only contain letters, spaces, periods (.), apostrophes (\'), and hyphens (-). Numbers and special characters are not allowed.');
        }

        // Validate email format
        $email = trim(strtolower($data['email']));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Invalid email format.');
        }

        // Check for duplicate email (case-insensitive)
        $existingUser = $this->userModel->where('email', $email)->first();
        if (!$existingUser) {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM users WHERE LOWER(email) = ?", [strtolower($email)]);
            $existingUser = $query->getRowArray();
        }
        
        if ($existingUser) {
            return redirect()->back()->with('error', '❌ Email Already Taken: The email address "' . htmlspecialchars($data['email']) . '" is already registered. Please use a different email address.');
        }

        // Validate password length
        if (strlen($data['password']) < 6) {
            return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
        }

        // Validate role
        if (!in_array($data['role'], ['admin', 'student', 'teacher'], true)) {
            return redirect()->back()->with('error', 'Invalid role selected.');
        }

        // Save user (normalize email to lowercase)
        $result = $this->userModel->save([
            'name' => trim($data['name']),
            'email' => $email,
            'role' => $data['role'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        if ($result) {
            return redirect()->back()->with('success', 'User added successfully.');
        } else {
            return redirect()->back()->with('error', 'Failed to add user. Please try again.');
        }
    }

    // Check if email exists (AJAX endpoint for real-time validation)
    public function checkEmail()
    {
        $email = trim(strtolower($this->request->getPost('email') ?? ''));
        
        if (empty($email)) {
            return $this->response->setJSON(['exists' => false]);
        }
        
        // Check for duplicate email (case-insensitive)
        $existingUser = $this->userModel->where('email', $email)->first();
        
        if (!$existingUser) {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM users WHERE LOWER(email) = ?", [strtolower($email)]);
            $existingUser = $query->getRowArray();
        }
        
        return $this->response->setJSON([
            'exists' => $existingUser !== null,
            'email' => $email
        ]);
    }

}