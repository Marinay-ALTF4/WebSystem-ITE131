<?php

namespace App\Controllers;

class Dashboard extends BaseController
{
    public function index()
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to(base_url('login'));
        }

        return view('auth/dashboard'); 
    }
}
