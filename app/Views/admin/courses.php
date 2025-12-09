<?php
include('app/Views/templates/header.php');
$currentYear = (int) date('Y');
$yearOptions = [
  $currentYear . '-' . ($currentYear + 1),
  ($currentYear + 1) . '-' . ($currentYear + 2),
  ($currentYear + 2) . '-' . ($currentYear + 3),
];
$timeOptions = [
  'MWF 8:00-9:00 AM',
  'MWF 9:00-10:00 AM',
  'MWF 10:00-11:00 AM',
  'MWF 1:00-2:00 PM',
  'TTh 9:00-10:30 AM',
  'TTh 1:00-2:30 PM',
  'TTh 3:00-4:30 PM',
  'Sat 8:00-11:00 AM',
];
?>

<div class="container my-5">
  <div class="card shadow-sm">
    <div class="card-body">
      <h4 class="mb-3">Available Courses</h4>

      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          <?= session()->getFlashdata('success') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          <?= session()->getFlashdata('error') ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <?php if (!empty($courses)): ?>
        <div class="list-group mb-3">
          <?php foreach ($courses as $course): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <h5 class="mb-1"><?= esc($course['title']); ?></h5>
                <p class="text-black mb-1">Course Code: <?= esc($course['description']); ?></p>
                <small class="text-dark d-block">Teacher: <?= esc($course['teacher_name'] ?? 'Not assigned') ?></small>
                <small class="text-dark d-block">Semester/Term: <?= esc($course['semester'] ?? 'Not set') ?></small>
                <small class="text-dark d-block">Time: <?= esc($course['class_time'] ?? 'TBA') ?></small>
                <small class="text-dark d-block">SY: <?= esc($course['school_year'] ?? 'Set school year') ?></small>
              </div>
              <div class="d-flex gap-2 align-items-center">
                <a href="<?= base_url('admin/course/' . $course['id'] . '/upload'); ?>" class="btn btn-dark btn-sm rounded-pill">Add Material</a>
                <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editAdminCourseModal<?= $course['id'] ?>">Edit</button>
                <form action="<?= base_url('admin/course/delete/' . $course['id']); ?>" method="post" class="m-0" onsubmit="return confirm('Delete this course? This will remove its enrollments and materials.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                </form>
              </div>
            </div>

            <!-- Edit Course Modal for Admin -->
            <div class="modal fade" id="editAdminCourseModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="editAdminCourseModalLabel<?= $course['id'] ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form action="<?= base_url('teacher/course/update/' . $course['id']) ?>" method="post">
                    <?= csrf_field() ?>

                    <div class="modal-header">
                      <h5 class="modal-title" id="editAdminCourseModalLabel<?= $course['id'] ?>">Edit Course</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="adminTitle<?= $course['id'] ?>" class="form-label">Course Title</label>
                        <input type="text" class="form-control" id="adminTitle<?= $course['id'] ?>" name="title" value="<?= esc($course['title']) ?>" required>
                      </div>

                      <div class="mb-3">
                        <label for="adminDescription<?= $course['id'] ?>" class="form-label">Course Code</label>
                        <input type="text" class="form-control" id="adminDescription<?= $course['id'] ?>" name="description" value="<?= esc($course['description']) ?>" required>
                      </div>

                      <div class="mb-3">
                        <label for="adminSemester<?= $course['id'] ?>" class="form-label">Semester</label>
                        <select class="form-select" id="adminSemester<?= $course['id'] ?>" name="semester">
                          <option value="">Select Semester</option>
                          <option value="1st Semester" <?= (isset($course['semester']) && strpos((string) $course['semester'], '1st Semester') !== false) ? 'selected' : '' ?>>1st Semester</option>
                          <option value="2nd Semester" <?= (isset($course['semester']) && strpos((string) $course['semester'], '2nd Semester') !== false) ? 'selected' : '' ?>>2nd Semester</option>
                          <option value="Summer" <?= (isset($course['semester']) && strpos((string) $course['semester'], 'Summer') !== false) ? 'selected' : '' ?>>Summer</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="adminTerm<?= $course['id'] ?>" class="form-label">Term</label>
                        <select class="form-select" id="adminTerm<?= $course['id'] ?>" name="term">
                          <option value="">Select Term</option>
                          <option value="Term 1" <?= (isset($course['semester']) && strpos((string) $course['semester'], 'Term 1') !== false) ? 'selected' : '' ?>>Term 1</option>
                          <option value="Term 2" <?= (isset($course['semester']) && strpos((string) $course['semester'], 'Term 2') !== false) ? 'selected' : '' ?>>Term 2</option>
                          <option value="Term 3" <?= (isset($course['semester']) && strpos((string) $course['semester'], 'Term 3') !== false) ? 'selected' : '' ?>>Term 3</option>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="adminSchoolYear<?= $course['id'] ?>" class="form-label">School Year</label>
                        <select class="form-select" id="adminSchoolYear<?= $course['id'] ?>" name="school_year" required>
                          <option value="">Select School Year</option>
                          <?php foreach ($yearOptions as $y): ?>
                            <option value="<?= esc($y) ?>" <?= (isset($course['school_year']) && $course['school_year'] === $y) ? 'selected' : '' ?>><?= esc($y) ?></option>
                          <?php endforeach; ?>
                        </select>
                      </div>

                      <div class="mb-3">
                        <label for="adminClassTime<?= $course['id'] ?>" class="form-label">Time</label>
                        <input list="timeOptions" type="text" class="form-control" id="adminClassTime<?= $course['id'] ?>" name="class_time" value="<?= esc($course['class_time'] ?? '') ?>" placeholder="Select or type time">
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                      <button type="submit" class="btn btn-dark">Save Changes</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted text-center mt-3">No courses found.</p>
      <?php endif; ?>

      <div class="text-center my-4">
        <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addAdminCourseModal">
          <i class="bi bi-plus-circle me-1"></i> Add New Course
        </button>
      </div>

      <!-- Add Course Modal for Admin -->
      <div class="modal fade" id="addAdminCourseModal" tabindex="-1" aria-labelledby="addAdminCourseModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form action="<?= base_url('admin/course/add') ?>" method="post">
              <?= csrf_field() ?>

              <div class="modal-header">
                <h5 class="modal-title" id="addAdminCourseModalLabel">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>

              <div class="modal-body">
                <div class="mb-3">
                  <label for="adminCourseTitle" class="form-label">Course Title</label>
                  <input type="text" class="form-control" id="adminCourseTitle" name="title" required>
                </div>

                <div class="mb-3">
                  <label for="adminCourseDescription" class="form-label">Course Code</label>
                  <input type="text" class="form-control" id="adminCourseDescription" name="description" required>
                </div>

                <div class="mb-3">
                  <label for="adminCourseTeacher" class="form-label">Select Teacher</label>
                  <select class="form-select" id="adminCourseTeacher" name="teacher_id" required>
                    <option value="">Select Teacher</option>
                    <?php if (!empty($teachers)): ?>
                      <?php foreach ($teachers as $teacher): ?>
                        <option value="<?= esc($teacher['id']) ?>"><?= esc($teacher['name']) ?> (<?= esc($teacher['email']) ?>)</option>
                      <?php endforeach; ?>
                    <?php endif; ?>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="adminCourseSemester" class="form-label">Semester</label>
                  <select class="form-select" id="adminCourseSemester" name="semester">
                    <option value="">Select Semester</option>
                    <option value="1st Semester">1st Semester</option>
                    <option value="2nd Semester">2nd Semester</option>
                    <option value="Summer">Summer</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="adminCourseTerm" class="form-label">Term</label>
                  <select class="form-select" id="adminCourseTerm" name="term">
                    <option value="">Select Term</option>
                    <option value="Term 1">Term 1</option>
                    <option value="Term 2">Term 2</option>
                    <option value="Term 3">Term 3</option>
                  </select>
                </div>

                <div class="mb-3">
                  <label for="adminCourseClassTime" class="form-label">Time</label>
                  <input list="timeOptions" type="text" class="form-control" id="adminCourseClassTime" name="class_time" placeholder="Select or type time">
                </div>

                <div class="mb-3">
                  <label for="adminCourseSchoolYear" class="form-label">School Year</label>
                  <select class="form-select" id="adminCourseSchoolYear" name="school_year" required>
                    <option value="">Select School Year</option>
                    <?php foreach ($yearOptions as $y): ?>
                      <option value="<?= esc($y) ?>"><?= esc($y) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
              </div>

              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-dark">Add Course</button>
              </div>
            </form>
          </div>
        </div>
      </div>

    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<datalist id="timeOptions">
  <?php foreach ($timeOptions as $t): ?>
    <option value="<?= esc($t) ?>">
  <?php endforeach; ?>
</datalist>

