<?php

namespace App\Controllers;

use App\Models\UserModel; 
use CodeIgniter\Controller;

class Auth extends Controller
{
    public function register()
    {
            helper(['form']);

            if ($this->request->getMethod() === 'POST') {
                $rules = [
                     'name'              => 'required|min_length[3]',
                     'email'             => 'required|valid_email|is_unique[users.email]',
                     'password'          => 'required|min_length[6]',
                     'password_confirm'  => 'matches[password]',
                     'role'              => 'required|in_list[admin,teacher,student]'
                ];

                if ($this->validate($rules)) {
                    $userModel = new \App\Models\UserModel();
                    $userModel->save([
                        'name'     => $this->request->getVar('name'),
                        'email'    => $this->request->getVar('email'),
                        'password' => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
                        'role'     => strtolower($this->request->getVar('role'))
                    ]);

                    return redirect()->to('/login')->with('success', 'Registration Success. Please proceed to login.');
                } else {
                    return view('auth/register', ['validation' => $this->validator]);
                }
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
                'password' => 'required|min_length[6]',
                'role'     => 'required|in_list[admin,teacher,student]'
            ];

            if (!$this->validate($rules)) {
                return view('auth/login', ['validation' => $this->validator]);
            }

            $user = $userModel->where('email', $this->request->getVar('email'))->first();

            $selectedRole = strtolower($this->request->getVar('role'));

            if ($user && password_verify($this->request->getVar('password'), $user['password'])) {
                if (isset($user['role']) && strtolower($user['role']) !== $selectedRole) {
                    $session->setFlashdata('error', 'Selected role does not match this account.');
                    return redirect()->back()->withInput();
                }
                $session->set([
                    'userID'    => $user['id'],
                    'name'      => $user['name'],
                    'email'     => $user['email'],
                    'role'      => $user['role'] ?? $selectedRole,
                    'isLoggedIn'=> true
                ]);
                $session->setFlashdata('success', 'Welcome ' . $user['name']);

                $role = strtolower($session->get('role'));
                if ($role === 'admin') {
                    return redirect()->to('admin/dashboard');
                } elseif ($role === 'teacher') {
                    return redirect()->to('teacher/dashboard');
                } elseif ($role === 'student') {
                    return redirect()->to('student/dashboard');
                }

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

    return view('auth/dashboard'); 
}

    public function adminDashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
        }
        if (strtolower($session->get('role')) !== 'admin') {
            return redirect()->to('login')->with('error', 'Unauthorized access.');
        }
        return view('auth/AdminRole');
    }

    public function teacherDashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
        }
        if (strtolower($session->get('role')) !== 'teacher') {
            return redirect()->to('login')->with('error', 'Unauthorized access.');
        }
        return view('auth/TeacherRole');
    }

    public function studentDashboard()
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'))->with('login_error', 'Please log in first.');
        }
        if (strtolower($session->get('role')) !== 'student') {
            return redirect()->to('login')->with('error', 'Unauthorized access.');
        }
        return view('auth/StudentRole');
    }
}