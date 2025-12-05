<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CourseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title' => 'Introduction to Web Development',
                'description' => 'Learn the fundamentals of HTML, CSS, and JavaScript for building modern websites.',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'MWF 9:00-10:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'PHP Programming Fundamentals',
                'description' => 'Master PHP programming from basics to advanced concepts including OOP and database integration.',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'TTh 1:00-2:30 PM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design and Management',
                'description' => 'Learn to design, implement, and manage relational databases using MySQL.',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'Sat 8:00-11:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'CodeIgniter Framework',
                'description' => 'Build web applications using the CodeIgniter PHP framework with MVC architecture.',
                'teacher_id' => 3,
                'school_year' => '2024-2025',
                'class_time' => 'MWF 10:00-11:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'JavaScript Advanced Concepts',
                'description' => 'Explore advanced JavaScript features including ES6+, async programming, and DOM manipulation.',
                'teacher_id' => 3,
                'school_year' => '2024-2025',
                'class_time' => 'TTh 3:00-4:30 PM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            
        ];

        $this->db->table('courses')->insertBatch($data);
    }
}
