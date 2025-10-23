<?php

namespace App\Models;

use CodeIgniter\Model;

class CourseModel extends Model
{
    protected $table      = 'courses';
    protected $primaryKey = 'courseID';
    protected $allowedFields = ['courseTitle', 'courseDescription', 'teacherID'];
}