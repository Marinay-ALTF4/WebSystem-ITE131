<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

    // Home, About, Contact
    $routes->get('/', 'home::index'); // Optional kung gusto diretso bisan walay nay home

    $routes->get('/home', 'home::index');
    $routes->get('/about', 'home::about');
    $routes->get('/contact', 'home::contact');

    // Lab 4 - User Authentication
        $routes->get('register', 'Auth::register');
        $routes->post('register', 'Auth::register');
        $routes->get('login', 'Auth::login');
        $routes->post('login', 'Auth::login');
        $routes->get('logout', 'Auth::logout');
        $routes->get('/dashboard', 'Auth::dashboard');

        // Role-based dashboards
        $routes->get('admin/dashboard', 'Auth::adminDashboard');
        $routes->get('teacher/dashboard', 'Auth::teacherDashboard');
        $routes->get('student/dashboard', 'Auth::studentDashboard');

        // Courses
        $routes->post('courses/enroll', 'Course::enroll');
        $routes->get('/studentCourse', 'Auth::studentCourse');
        $routes->post('teacher/course/add', 'Auth::addCourse');


       // Materials routes
       $routes->get('/admin/course/(:num)/upload', 'Materials::upload/$1');

        $routes->post('/admin/course/(:num)/upload', 'Materials::upload/$1');

        $routes->get('/materials/delete/(:num)', 'Materials::delete/$1');

        $routes->get('/materials/download/(:num)', 'Materials::download/$1');

        // Notifications Lab 8
        // Notifications
        $routes->get('/notifications', 'Notifications::get');
        $routes->get('/notifications/count', 'Notifications::count');
        $routes->get('/notifications/view', 'Notifications::index'); // ← shows notification.php
        $routes->post('/notifications/mark_read/(:num)', 'Notifications::mark_as_read/$1');
        $routes->post('/notifications/mark_all', 'Notifications::mark_all');




        
