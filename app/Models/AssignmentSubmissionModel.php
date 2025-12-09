<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentSubmissionModel extends Model
{
    protected $table = 'assignment_submissions';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'assignment_id',
        'student_id',
        'course_id',
        'content',
        'file_path',
        'score',
        'feedback',
        'graded_by',
        'graded_at',
        'submitted_at',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
}
