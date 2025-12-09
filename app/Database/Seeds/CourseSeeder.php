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
                'description' => 'WEB-101',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'MWF 9:00-10:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'PHP Programming Fundamentals',
                'description' => 'PHP-201',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'TTh 1:00-2:30 PM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'Database Design and Management',
                'description' => 'DB-210',
                'teacher_id' => 2,
                'school_year' => '2024-2025',
                'class_time' => 'Sat 8:00-11:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'CodeIgniter Framework',
                'description' => 'CI-301',
                'teacher_id' => 3,
                'school_year' => '2024-2025',
                'class_time' => 'MWF 10:00-11:00 AM',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'title' => 'JavaScript Advanced Concepts',
                'description' => 'JS-310',
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
