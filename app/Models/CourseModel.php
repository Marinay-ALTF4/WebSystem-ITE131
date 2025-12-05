<?php

namespace App\Models;
use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table = 'courses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['title', 'description', 'teacher_id', 'school_year', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
}
