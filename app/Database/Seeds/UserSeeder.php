<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'Admin User',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Justin Nabunturan',
                'email'    => 'jstnabunturan@gmail.com',
                'password' => password_hash('student123', PASSWORD_DEFAULT),
                'role'     => 'student',
            ],
    
            [
                'name'     => 'Lemuel Patotoya',
                'email'    => 'lmlpatotoya@gmail.com',
                'password' => password_hash('instructor123', PASSWORD_DEFAULT),
                'role'     => 'instructor',
            ],
        ];

        // Insert multiple rows at once
        $this->db->table('users')->insertBatch($data);
    }
}
