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

        // Validate name - allow letters, numbers, spaces, and limited symbols with min length 5
        $namePattern = '/^[A-Za-z0-9@\.\'\-_ ]{5,}$/';
        if (!preg_match($namePattern, trim($data['name']))) {
            return redirect()->back()->with('error', 'Invalid name format. Use at least 5 characters with letters, numbers, spaces, and only @ . \" - _ allowed.');
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

    // Edit existing user
    public function editUser(int $userId)
    {
        $session = session();
        $currentUserId = (int) $session->get('userID');

        helper(['form']);
        $data = $this->request->getPost();

        $existingRecord = $this->userModel->withDeleted()->find($userId);
        if (! $existingRecord) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if (!$data || empty($data['name']) || empty($data['email']) || empty($data['role'])) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        // Prevent changing the currently logged-in admin into a deleted user indirectly.
        $email = trim(strtolower($data['email']));
        $namePattern = '/^[A-Za-z0-9@\.\'\-_ ]{5,}$/';
        if (!preg_match($namePattern, trim($data['name']))) {
            return redirect()->back()->with('error', 'Invalid name format. Use at least 5 characters with letters, numbers, spaces, and only @ . \" - _ allowed.');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Invalid email format.');
        }

        $isEditingSelf = ($userId === $currentUserId);

        if (!in_array($data['role'], ['admin', 'student', 'teacher'], true)) {
            return redirect()->back()->with('error', 'Invalid role selected.');
        }

        // Ensure email uniqueness (case-insensitive) excluding the current record.
        $existingUser = $this->userModel
            ->where('LOWER(email)', $email)
            ->where('id !=', $userId)
            ->withDeleted()
            ->first();

        if ($existingUser) {
            return redirect()->back()->with('error', 'Email is already taken by another user.');
        }

        $updateData = [
            'name' => trim($data['name']),
            'email' => $email,
            'role' => $isEditingSelf ? $existingRecord['role'] : $data['role'],
        ];

        // Optional password change
        if (!empty($data['password'])) {
            if (strlen($data['password']) < 6) {
                return redirect()->back()->with('error', 'Password must be at least 6 characters long.');
            }
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $this->userModel->update($userId, $updateData);
        return redirect()->back()->with('success', 'User updated successfully.');
    }

    // Soft-delete user (cannot delete currently logged-in user)
    public function deleteUser(int $userId)
    {
        $session = session();
        $currentUserId = (int) $session->get('userID');

        if ($userId === $currentUserId) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        $user = $this->userModel->withDeleted()->find($userId);
        if (! $user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $this->userModel->delete($userId);
        return redirect()->back()->with('success', 'User deleted (can be restored).');
    }

    // Restore soft-deleted user
    public function restoreUser(int $userId)
    {
        $user = $this->userModel->withDeleted()->find($userId);
        if (! $user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        if ($user['deleted_at'] === null) {
            return redirect()->back()->with('info', 'User is already active.');
        }

        $this->userModel->update($userId, ['deleted_at' => null]);
        return redirect()->back()->with('success', 'User restored successfully.');
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
            'email' => $email,
            // Return fresh CSRF tokens so the next POST does not fail after regeneration
            'csrfTokenName' => csrf_token(),
            'csrfTokenValue' => csrf_hash(),
        ]);
    }

}