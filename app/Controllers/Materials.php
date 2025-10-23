<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        helper(['form', 'url']);
        $materialModel = new MaterialModel();

        if ($this->request->getMethod() === 'post') {
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

                $materialModel->insertMaterial([
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

        $materials = $materialModel->getMaterialsByCourse($course_id);
        return view('materials/upload', ['course_id' => $course_id, 'materials' => $materials]);
    }

    public function delete($material_id)
    {
        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);

        if ($material) {
            $filePath = FCPATH . $material['file_path'];
            if (file_exists($filePath)) unlink($filePath);

            $materialModel->delete($material_id);
            return redirect()->back()->with('success', 'Material deleted successfully.');
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }

    public function download($material_id)
    {
        $session = session();
        $user_id = $session->get('id');
        $role = $session->get('role');

        if (!$user_id) {
            return redirect()->to('/login')->with('error', 'Please log in to download materials.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->find($material_id);
        if (!$material) return redirect()->back()->with('error', 'Material not found.');

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

        $filePath = FCPATH . $material['file_path'];
        if (file_exists($filePath)) {
            return $this->response->download($filePath, null)->setFileName($material['file_name']);
        } else {
            return redirect()->back()->with('error', 'File not found on the server.');
        }
    }
}