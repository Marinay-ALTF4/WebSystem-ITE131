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
        $data = $this->request->getPost();

        // Validate input
        if (!$data || empty($data['name']) || empty($data['email']) || empty($data['password'])) {
            return redirect()->back()->with('error', 'All fields are required.');
        }

        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return redirect()->back()->with('error', 'Invalid email format.');
        }

        // Check for duplicate email
        if ($this->userModel->where('email', $data['email'])->first()) {
            return redirect()->back()->with('error', 'Email already exists.');
        }

        // Save user
        $this->userModel->save([
            'name' => $data['name'],
            'email' => $data['email'],
            'role' => $data['role'] ?? 'user', // default role if not provided
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);

        return redirect()->back()->with('success', 'User added successfully.');
    }

}