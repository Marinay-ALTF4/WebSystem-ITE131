<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSemesterToCourse extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'semester' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
                'after'      => 'class_time',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'semester');
    }
}
