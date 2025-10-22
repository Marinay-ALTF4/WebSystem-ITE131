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
        // $routes->get('Materials/upload', 'Materials::upload');

      

        // Materials, File upload and download
        
       // Teacher/Admin upload material
       // Materials routes
$routes->get('materials/upload/(:num)', 'materials::upload/$1');   // For showing upload page
$routes->post('materials/upload/(:num)', 'materials::upload/$1');  // For uploading files
$routes->get('materials/delete/(:num)', 'materials::delete/$1');   // For deleting materials
$routes->get('materials/download/(:num)', 'materials::download/$1'); // For downloading files



        
