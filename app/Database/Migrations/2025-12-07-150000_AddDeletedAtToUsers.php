<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToUsers extends Migration
{
    public function up()
    {
        $db = db_connect();

        // Add the column only if it does not yet exist
        if (! in_array('deleted_at', $db->getFieldNames('users'), true)) {
            $this->forge->addColumn('users', [
                'deleted_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
            ]);
        }
    }

    public function down()
    {
        $db = db_connect();

        // Drop the column only if it exists
        if (in_array('deleted_at', $db->getFieldNames('users'), true)) {
            $this->forge->dropColumn('users', 'deleted_at');
        }
    }
}
