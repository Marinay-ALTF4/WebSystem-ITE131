<?php

namespace App\Models;

use CodeIgniter\Model;

class AssignmentModel extends Model
{
    protected $table = 'assignments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'course_id',
        'teacher_id',
        'title',
        'description',
        'points',
        'assignment_type',
        'submit_type',
        'attempts_allowed',
        'due_date',
        'available_after',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = false;
}
