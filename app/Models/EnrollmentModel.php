<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'enrollment_date'];
    protected $useTimestamps = false;

    public function getUserEnrollments(int $userId): array
    {
        return $this->select('enrollments.*, courses.title, courses.description')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->where('enrollments.user_id', $userId)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();
    }

    public function isAlreadyEnrolled(int $userId, int $courseId): bool
    {
        return $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->countAllResults() > 0;
    }

    public function getAvailableCoursesForUser(int $userId): array
    {
        // Return all courses not yet enrolled by the given user
        $db = $this->db;
        $builder = $db->table('courses');
        $builder->select('courses.*');
        $builder->whereNotIn('courses.id', function($sub) use ($userId) {
            $sub->select('course_id')
                ->from('enrollments')
                ->where('user_id', $userId);
        });

        return $builder->get()->getResultArray();
    }
}

