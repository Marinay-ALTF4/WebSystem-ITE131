<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{
    protected $table = 'enrollments';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'course_id', 'status', 'teacher_id', 'enrollment_date', 'updated_at'];
    protected $useTimestamps = false;

    public function getUserEnrollments(int $userId, ?string $status = 'accepted'): array
    {
        $builder = $this->select('enrollments.*, courses.title, courses.description, courses.semester, courses.school_year, courses.class_time, courses.teacher_id, users.name AS student_name, users.email AS student_email, teacher.name AS teacher_name')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->join('users as teacher', 'teacher.id = courses.teacher_id', 'left')
            ->where('enrollments.user_id', $userId);

        if ($status !== null) {
            $builder->where('enrollments.status', $status);
        }

        return $builder->orderBy('enrollments.enrollment_date', 'DESC')->findAll();
    }

    public function getEnrollmentsForTeacher(int $teacherId, ?string $status = null): array
    {
        $builder = $this->select('enrollments.*, users.name AS student_name, users.email AS student_email, courses.title AS course_title, courses.semester, courses.school_year, courses.class_time, courses.teacher_id')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('courses.teacher_id', $teacherId);

        if ($status !== null) {
            $builder->where('enrollments.status', $status);
        }

        return $builder->orderBy('enrollments.enrollment_date', 'DESC')->findAll();
    }

    public function findWithCourseAndStudent(int $enrollmentId): ?array
    {
        return $this->select('enrollments.*, courses.teacher_id, courses.title AS course_title, courses.class_time, courses.school_year, users.name AS student_name, users.id AS student_id')
            ->join('courses', 'courses.id = enrollments.course_id', 'left')
            ->join('users', 'users.id = enrollments.user_id', 'left')
            ->where('enrollments.id', $enrollmentId)
            ->first();
    }

    public function isAlreadyEnrolled(int $userId, int $courseId): bool
    {
        return $this->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->whereIn('status', ['pending', 'accepted'])
            ->countAllResults() > 0;
    }

    public function getAvailableCoursesForUser(int $userId): array
    {
        $db = $this->db;
        $builder = $db->table('courses');
        $builder->select('courses.*, users.name AS teacher_name');
        $builder->join('users', 'users.id = courses.teacher_id', 'left');
        $builder->whereNotIn('courses.id', function($sub) use ($userId) {
            $sub->select('course_id')
                ->from('enrollments')
                ->where('user_id', $userId)
                ->whereIn('status', ['pending', 'accepted']);
        });

        return $builder->get()->getResultArray();
    }
}

