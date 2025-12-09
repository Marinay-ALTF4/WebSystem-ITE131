<?php

declare(strict_types=1);

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPointsAndGrading extends Migration
{
    public function up(): void
    {
        // Add points to assignments
        $this->forge->addColumn('assignments', [
            'points' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'description',
            ],
        ]);

        // Add grading columns to assignment_submissions
        $this->forge->addColumn('assignment_submissions', [
            'score' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'file_path',
            ],
            'feedback' => [
                'type' => 'TEXT',
                'null' => true,
                'after' => 'score',
            ],
            'graded_by' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'after' => 'feedback',
            ],
            'graded_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'graded_by',
            ],
        ]);
    }

    public function down(): void
    {
        $this->forge->dropColumn('assignments', 'points');
        $this->forge->dropColumn('assignment_submissions', ['score', 'feedback', 'graded_by', 'graded_at']);
    }
}
