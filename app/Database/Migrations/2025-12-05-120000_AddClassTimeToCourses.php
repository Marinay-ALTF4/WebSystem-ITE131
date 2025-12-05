<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddClassTimeToCourses extends Migration
{
    public function up()
    {
        $this->forge->addColumn('courses', [
            'class_time' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
                'after'      => 'school_year',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', 'class_time');
    }
}
