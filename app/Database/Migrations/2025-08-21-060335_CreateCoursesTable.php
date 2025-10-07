<?php
// Kani nga file maghimo ug migration para sa `courses` table.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up()
    {
        // up(): Paghimo sa structure sa `courses` table.
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                // title sa course.
            ],
            'description' => [
                'type' => 'TEXT',
                'null' => true,
                // detalye sa course.
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
                //kung unsa oras nahimo ang record.
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                // bag'ong oras sa sa pag update.
            ],
        ]);
        $this->forge->addKey('id', true); // Primary key sa id.
        $this->forge->createTable('courses'); // Paghimo sa courses table.
    }

    public function down()
    {
        // down(): I-undo ang up() — hawaon ang table.
        $this->forge->dropTable('courses');
    }
}
