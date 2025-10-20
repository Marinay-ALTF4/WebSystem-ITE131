<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel; 
use CodeIgniter\Controller;

class Materials extends Controller
{
 
    // STEP 4: FILE UPLOAD FUNCTION
   
    public function upload($course_id)
{
    helper(['form', 'url']);
    $materialModel = new MaterialModel();

    if ($this->request->getMethod() === 'post') {
        // File validation
        $validationRule = [
            'material_file' => [
                'label' => 'Material File',
                'rules' => 'uploaded[material_file]|ext_in[material_file,pdf,doc,docx,ppt,pptx,jpg,png,mp4,zip]|max_size[material_file,5120]',
            ],
        ];

        if (!$this->validate($validationRule)) {
            return redirect()->back()->with('error', $this->validator->getErrors());
        }

        $file = $this->request->getFile('material_file');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $uploadPath = FCPATH . 'uploads/materials/';

            if (!is_dir($uploadPath)) mkdir($uploadPath, 0777, true);

            $file->move($uploadPath, $newName);

            $materialModel->insert([
                'course_id' => $course_id,
                'file_name' => $file->getClientName(),
                'file_path' => 'uploads/materials/' . $newName,
                'created_at' => date('Y-m-d H:i:s'),
            ]);

            return redirect()->back()->with('success', 'Material uploaded successfully!');
        } else {
            return redirect()->back()->with('error', 'File upload failed.');
        }
    }

    // GET request: show upload form
    return view('Materials/upload', ['course_id' => $course_id]);
}

        
    // DELETE MATERIAL (Admin/Teacher)
    
    public function delete($material_id)
    {
        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            if (file_exists($material['file_path'])) {
                unlink($material['file_path']); // delete actual file
            }

            $materialModel->delete($material_id);
            return redirect()->back()->with('success', 'Material deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }

    
    // STEP 7: SECURE DOWNLOAD METHOD
    
    public function download($material_id)
    {
        $session = session();
        $user_id = $session->get('id');
        $role = $session->get('role');

        // Check kung naka-login
        if (!$user_id) {
            return redirect()->to('/login')->with('error', 'Please log in to download materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // Check kung student ug enrolled siya sa course
        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();

            $isEnrolled = $enrollmentModel
                ->where('student_id', $user_id)
                ->where('course_id', $material['course_id'])
                ->first();

            if (!$isEnrolled) {
                return redirect()->back()->with('error', 'You are not enrolled in this course.');
            }
        }

        // Proceed to download if file exists
        if (file_exists($material['file_path'])) {
            return $this->response->download($material['file_path'], null)
                                  ->setFileName($material['file_name']);
        } else {
            return redirect()->back()->with('error', 'File not found on the server.');
        }
    }

    
    // VIEW MATERIALS BY COURSE
    
    public function viewMaterials($course_id)
    {
        $materialModel = new MaterialModel();
        $materials = $materialModel->where('course_id', $course_id)->findAll();

        echo view('uploads/material_upload', ['course_id' => $course_id]);

    }

        
    // STUDENT DASHBOARD MATERIALS
        
    public function studentDashboard()
    {
        $materialModel = new MaterialModel();
        $course_id = session()->get('course_id');
    
        $data['materials'] = $materialModel->where('course_id', $course_id)->findAll();
        $data['profile'] = [
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'course_id' => $course_id
        ];
        $data['role'] = 'student';
    
        echo view('dashboard', ['data' => $data, 'role' => $data['role']]);
    }
}    