<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

    // Home, About, Contact
    $routes->get('/', 'Auth::login');
    $routes->get('/about', 'Home::about');
    $routes->get('/contact', 'Home::contact');

    // Lab 4 - User Authentication
        $routes->get('register', 'Auth::register');
        $routes->post('register', 'Auth::register');
        $routes->get('login', 'Auth::login');
        $routes->post('login', 'Auth::login');
        $routes->get('logout', 'Auth::logout');
        $routes->get('dashboard', 'Auth::dashboard');

        // Role-based dashboards
        $routes->get('admin/dashboard', 'Auth::adminDashboard');
        $routes->get('teacher/dashboard', 'Auth::teacherDashboard');
        $routes->get('student/dashboard', 'Auth::studentDashboard');

