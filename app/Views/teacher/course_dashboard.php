<?php
include('app/Views/templates/header.php');
?>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">Course Dashboard</h4>
    <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#enrollStudentModal">Enroll Student</button>
  </div>

  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" role="alert">
      <?= esc(session()->getFlashdata('success')) ?>
    </div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <!-- Enroll Student Modal -->
  <div class="modal fade" id="enrollStudentModal" tabindex="-1" aria-labelledby="enrollStudentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form action="<?= base_url('teacher/course/' . ($course['id'] ?? 0) . '/enroll-student'); ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-header">
            <h5 class="modal-title" id="enrollStudentModalLabel">Enroll Existing Student</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <p class="text-muted mb-3">Enter the student's email. Only existing student accounts can be enrolled.</p>
            <div class="mb-3">
              <label for="student_email" class="form-label">Student Email</label>
              <input type="email" class="form-control" id="student_email" name="student_email" placeholder="student@example.com" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-dark">Enroll</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="card-title mb-2"><?= esc($course['title'] ?? 'Course') ?></h5>
      <p class="mb-2 text-dark">Course Code: <?= esc($course['description'] ?? 'N/A') ?></p>
      <div class="text-muted">Semester/Term: <?= esc($course['semester'] ?? 'Not set') ?></div>
      <div class="text-muted">Time: <?= esc($course['class_time'] ?? 'TBA') ?></div>
      <div class="text-muted">School Year: <?= esc($course['school_year'] ?? 'TBD') ?></div>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Enrollments</span>
      <span class="badge bg-dark">Pending: <?= count(array_filter($enrollments ?? [], fn($e) => strtolower($e['status'] ?? 'pending') === 'pending')) ?></span>
    </div>
    <div class="card-body p-0">
      <?php if (!empty($enrollments)): ?>
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Status</th>
                <th class="text-center">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($enrollments as $enrollment): ?>
                <?php $status = strtolower($enrollment['status'] ?? 'pending'); ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= esc($enrollment['student_name'] ?? 'Student') ?></div>
                    <div class="text-muted small"><?= esc($enrollment['student_email'] ?? '') ?></div>
                  </td>
                  <td>
                    <?php
                      $badge = 'secondary';
                      $label = 'Pending';
                      if ($status === 'accepted') {
                        $badge = 'success';
                        $label = 'Enrolled';
                      } elseif ($status === 'declined') {
                        $badge = 'danger';
                        $label = 'Dropped';
                      }
                    ?>
                    <span class="badge bg-<?= $badge ?> text-uppercase"><?= esc($label) ?></span>
                  </td>
                  <td class="text-center">
                    <?php if ($status === 'pending'): ?>
                      <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn btn-sm btn-success">Accept</button>
                      </form>
                      <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="declined">
                        <button type="submit" class="btn btn-sm btn-outline-danger">Decline</button>
                      </form>
                    <?php elseif ($status === 'accepted'): ?>
                      <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="declined">
                        <button type="submit" class="btn btn-sm btn-warning" onclick="return confirm('Drop this student from the course?');">Drop</button>
                      </form>
                    <?php elseif ($status === 'declined'): ?>
                      <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="accepted">
                        <button type="submit" class="btn btn-sm btn-outline-success">Re-enroll</button>
                      </form>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted m-3">No enrollment requests for this course yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Assignments</span>
      <button class="btn btn-sm btn-dark" data-bs-toggle="modal" data-bs-target="#assignmentModal">Add Assignment</button>
    </div>
    <div class="card-body p-0">
      <?php if (!empty($assignments)): ?>
        <div class="list-group list-group-flush">
          <?php foreach ($assignments as $item): ?>
            <div class="list-group-item">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <a class="fw-semibold text-decoration-none" href="<?= base_url('teacher/course/' . ($course['id'] ?? 0) . '/assignments/' . $item['id']) ?>"><?= esc($item['title']) ?></a>
                  <?php if (!empty($item['points'])): ?>
                    <div class="text-muted small">Points: <?= esc($item['points']) ?></div>
                  <?php endif; ?>
                  <div class="text-muted small">Type: <?= esc($item['assignment_type']) ?></div>
                  <div class="text-muted small">Submit: <?= esc(ucfirst((string) $item['submit_type'])) ?></div>
                  <div class="text-muted small">Attempts: <?= $item['attempts_allowed'] === null ? 'Unlimited' : esc($item['attempts_allowed']) ?></div>
                  <div class="text-muted small">Due: <?= $item['due_date'] ? esc(date('M d, Y h:i A', strtotime($item['due_date']))) : 'None' ?></div>
                  <div class="text-muted small">Available After: <?= $item['available_after'] ? esc(date('M d, Y h:i A', strtotime($item['available_after']))) : 'Immediately' ?></div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted m-3">No assignments yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Assignment Modal -->
  <div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form action="<?= base_url('teacher/course/' . ($course['id'] ?? 0) . '/assignments'); ?>" method="post">
          <?= csrf_field() ?>
          <div class="modal-header">
            <h5 class="modal-title" id="assignmentModalLabel">Create Assignment</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" required>
              </div>
              <div class="col-12">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add instructions or details"></textarea>
              </div>
              <div class="col-md-6">
                <label for="points" class="form-label">Points (optional)</label>
                <input type="number" min="1" class="form-control" id="points" name="points" placeholder="e.g. 100">
              </div>
              <div class="col-md-6">
                <label for="assignment_type" class="form-label">Type of Assignment</label>
                <select class="form-select" id="assignment_type" name="assignment_type" required>
                  <option value="">Select type</option>
                  <option value="Homework">Homework</option>
                  <option value="Quiz">Quiz</option>
                  <option value="Project">Project</option>
                  <option value="Exam">Exam</option>
                  <option value="Activity">Activity</option>
                </select>
              </div>
              <div class="col-md-6">
                <label class="form-label d-block">Submit Type</label>
                <div class="d-flex gap-3">
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="submit_type" id="submit_file" value="file" required>
                    <label class="form-check-label" for="submit_file">File Upload</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input" type="radio" name="submit_type" id="submit_text" value="text" required>
                    <label class="form-check-label" for="submit_text">Text</label>
                  </div>
                </div>
              </div>
              <div class="col-md-6">
                <label for="attempts" class="form-label">Attempts</label>
                <select class="form-select" id="attempts" name="attempts">
                  <option value="unlimited">Unlimited</option>
                  <option value="1">1</option>
                  <option value="2">2</option>
                  <option value="3">3</option>
                </select>
              </div>
              <div class="col-md-6">
                <label for="due_date" class="form-label">Due Date</label>
                <input type="datetime-local" class="form-control" id="due_date" name="due_date">
              </div>
              <div class="col-md-6">
                <label for="available_after" class="form-label">Available After</label>
                <input type="datetime-local" class="form-control" id="available_after" name="available_after">
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-dark">Save Assignment</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="card shadow-sm mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <span class="fw-semibold">Materials</span>
      <a class="btn btn-sm btn-dark" href="<?= base_url('admin/course/' . ($course['id'] ?? 0) . '/upload'); ?>">Add Material</a>
    </div>
    <div class="card-body p-0">
      <?php if (!empty($materials)): ?>
        <div class="list-group list-group-flush">
          <?php foreach ($materials as $mat): ?>
            <div class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold text-truncate" style="max-width: 480px;"><?= esc($mat['file_name']) ?></div>
              </div>
              <div class="d-flex gap-2">
                <a class="btn btn-sm btn-outline-dark" href="<?= site_url('materials/download/' . $mat['id']) ?>">Download</a>
                <a class="btn btn-sm btn-outline-danger" href="<?= site_url('materials/delete/' . $mat['id']) ?>" onclick="return confirm('Delete this file?');">Delete</a>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="text-muted m-3">No materials uploaded yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
