<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDescriptionToAssignments extends Migration
{
    public function up()
    {
        $this->forge->addColumn('assignments', [
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'title',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('assignments', 'description');
    }
}
