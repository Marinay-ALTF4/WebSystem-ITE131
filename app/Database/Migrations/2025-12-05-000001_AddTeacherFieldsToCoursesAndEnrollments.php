<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTeacherFieldsToCoursesAndEnrollments extends Migration
{
    public function up()
    {
        // Courses: teacher assignment and school year tagging
        $this->forge->addColumn('courses', [
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'description',
            ],
            'school_year' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
                'after'      => 'teacher_id',
            ],
        ]);

        // Enrollments: approval flow
        $this->forge->addColumn('enrollments', [
            'status' => [
                'type'    => "ENUM('pending','accepted','declined')",
                'default' => 'pending',
                'null'    => false,
                'after'   => 'course_id',
            ],
            'teacher_id' => [
                'type'       => 'INT',
                'constraint' => 5,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'status',
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'enrollment_date',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('courses', ['teacher_id', 'school_year']);
        $this->forge->dropColumn('enrollments', ['status', 'teacher_id', 'updated_at']);
    }
}
