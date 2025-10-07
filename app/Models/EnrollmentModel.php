<?php

namespace App\Models;

use CodeIgniter\Model;

class EnrollmentModel extends Model
{

    protected $table            = 'enrollments'; // table.
    protected $primaryKey       = 'id'; // Primary key sa table.
    protected $returnType       = 'array'; // Porma sa ibalik nga data.
    protected $allowedFields    = ['user_id', 'course_id', 'enrollment_date']; // field selction.

    /**
     * enrollUser($data): Diri mainsert ang bag-ong enrollment record.
     * Gamiton kung mag-enroll ang user sa usa ka course.
     */
    public function enrollUser(array $data)
    {
        // Kung walay gi-set nga enrollment_date, butangi ug current datetime.
        if (empty($data['enrollment_date'])) {
            $data['enrollment_date'] = date('Y-m-d H:i:s'); // current date and time sa enrollees.
        }

        return $this->insert($data); // Ibalik ang inserted ID kung naay mali.
    }

    /**
     * getUserEnrollments($user_id): magkuha sa mga courses nga gi-enrollan sa user.
     * tapos join sa courses table para makuha ang detail sa course.
     */
    public function getUserEnrollments(int $user_id): array
    {
        return $this->select('enrollments.*, courses.title, courses.description')
            ->join('courses', 'courses.id = enrollments.course_id', 'left') // I-apil ang course info.
            ->where('enrollments.user_id', $user_id)
            ->orderBy('enrollments.enrollment_date', 'DESC')
            ->findAll();
    }

    /**
     * isAlreadyEnrolled($user_id, $course_id): taga check kung naka-enroll na ba ang user
     * sa maong course to avoid duplicate..
     */
    public function isAlreadyEnrolled(int $user_id, int $course_id): bool
    {
        return $this->where('user_id', $user_id)
            ->where('course_id', $course_id)
            ->countAllResults() > 0; // True kung naa nay existing nga enrollment.
    }
}


