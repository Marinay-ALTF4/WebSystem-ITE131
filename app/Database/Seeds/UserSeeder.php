<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'     => 'admin',
                'email'    => 'admin@example.com',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role'     => 'admin',
            ],
            [
                'name'     => 'Prof. Lemuel',
                'email'    => 'Lemuel@example.com',
                'password' => password_hash('lemuel123', PASSWORD_DEFAULT),
                'role'     => 'teacher',
            ],
            [
                'name'     => 'Bugoy na Koykoy',
                'email'    => 'koy@example.com',
                'password' => password_hash('koy123', PASSWORD_DEFAULT),
                'role'     => 'student',
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
