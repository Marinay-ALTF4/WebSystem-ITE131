<?php
// Kani nga file maghimo ug migration para sa `enrollments` table.

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentsTable extends Migration
{
    public function up()
    {
        // up(): Diri ma define ang mga column ug constraints sa table.
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                // user_id: ID sa user nga nag enroll.
            ],
            'course_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                // course_id: ID sa course nga gienrollan.
            ],
			'enrollment_date' => [
				'type' => 'DATETIME',
				'null' => false,
				// enrollment_date: data ug time sa pag-enroll.
			],
        ]);
        $this->forge->addKey('id', true); // Primary key sa `id`.
		$this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE'); // Foreign key paingon sa users.id
		$this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'CASCADE'); // Foreign key paingon sa courses.id
        $this->forge->createTable('enrollments'); // Paghimo sa enrollments table.
    }

    public function down()
    {
        // down(): I-rollback ang gihimo sa up() — hawaon ang table.
        $this->forge->dropTable('enrollments');
    }
}
