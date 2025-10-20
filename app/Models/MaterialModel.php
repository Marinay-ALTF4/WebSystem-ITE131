<?php

namespace App\Models;

use CodeIgniter\Model;

class MaterialModel extends Model
{
   
    protected $table = 'materials';

    protected $primaryKey = 'id';

    // Allowed fields para ma-save sa database
    protected $allowedFields = ['course_id', 'file_name', 'file_path', 'created_at'];

    protected $useTimestamps = false;

    // Function para mo-insert ug new material record
    public function insertMaterial($data)
    {
        return $this->insert($data);
    }

    // Function para kuhaon tanan materials base sa course_id
    public function getMaterialsByCourse($course_id)
    {
        return $this->where('course_id', $course_id)
                    ->orderBy('created_at', 'DESC') 
                    ->findAll();
    }
}
