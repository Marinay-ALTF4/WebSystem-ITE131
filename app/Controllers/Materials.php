<?php

namespace App\Controllers;

use App\Models\MaterialModel;
use App\Models\EnrollmentModel;

class Materials extends BaseController
{
    public function upload($course_id)
    {
        helper(['form', 'url']);
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }
    
        $materialModel = new MaterialModel();
        $notificationModel = new \App\Models\NotificationModel(); // Added notification model
        $enrollmentModel = new EnrollmentModel(); // To get enrolled students
    
        if ($this->request->getMethod() === 'POST') {
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
    
                // ===== Notification logic =====
                try {
                    $courseModel = new \App\Models\CourseModel();
                    $course = $courseModel->find($course_id);
                    $courseTitle = $course ? $course['title'] : 'Course';
                    
                    $students = $enrollmentModel
                        ->where('course_id', $course_id)
                        ->where('status', 'accepted')
                        ->findAll();
                    
                    foreach ($students as $student) {
                        $notificationModel->createNotification(
                            $student['user_id'],
                            "New material uploaded in '{$courseTitle}'"
                        );
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Failed to send material upload notifications: ' . $e->getMessage());
                    // Continue even if notifications fail
                }
                // ==============================
    
                return redirect()->back()->with('success', 'Material uploaded successfully and students notified!');
            } else {
                return redirect()->back()->with('error', 'File upload failed.');
            }
        }
    
        $materials = $materialModel->getMaterialsByCourse($course_id);
        return view('materials/upload', ['course_id' => $course_id, 'materials' => $materials]);
    }
    
    public function delete($material_id)
    {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in first.');
        }

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if ($material) {
            try {
                $filePath = FCPATH . $material['file_path'];
                if (file_exists($filePath)) {
                    unlink($filePath);
                }

                $materialModel->deleteMaterial($material_id);
                return redirect()->back()->with('success', 'Material deleted successfully.');
            } catch (\Exception $e) {
                log_message('error', 'Failed to delete material: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Failed to delete material.');
            }
        } else {
            return redirect()->back()->with('error', 'Material not found.');
        }
    }

    public function download($material_id)
    {
        $session = session();

        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Please log in to download materials.');
        }

        $user_id = $session->get('userID'); // Correct session variable
        $role = $session->get('role');

        $materialModel = new MaterialModel();
        $material = $materialModel->getMaterialById($material_id);

        if (!$material) {
            return redirect()->back()->with('error', 'Material not found.');
        }

        // If student, check enrollment
        if ($role === 'student') {
            $enrollmentModel = new EnrollmentModel();
            $isEnrolled = $enrollmentModel
                ->where('user_id', $user_id)       // use user_id instead of student_id
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
