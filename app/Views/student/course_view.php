<?php
include('app/Views/templates/header.php');
?>

<style>
  .course-nav .list-group-item-action:hover {
    background-color: #000;
    color: #fff;
    border-color: #000;
  }
  .course-nav .list-group-item-action.active {
    background-color: #000;
    border-color: #000;
    color: #fff;
  }
</style>

<div class="container my-5">
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success" role="alert">
      <?= esc(session()->getFlashdata('success')) ?>
    </div>
  <?php elseif (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger" role="alert">
      <?= esc(session()->getFlashdata('error')) ?>
    </div>
  <?php endif; ?>

  <div class="row g-4">
    <div class="col-md-3">
      <div class="list-group shadow-sm course-nav">
        <a class="list-group-item list-group-item-action border-dark" data-bs-toggle="tab" href="#tab-home">Home</a>
        <a class="list-group-item list-group-item-action border-dark" data-bs-toggle="tab" href="#tab-assignments">Assignments</a>
        <a class="list-group-item list-group-item-action border-dark" data-bs-toggle="tab" href="#tab-grades">Grades</a>
        <a class="list-group-item list-group-item-action border-dark" data-bs-toggle="tab" href="#tab-people">People</a>
      </div>
    </div>

    <div class="col-md-9">
      <div class="card shadow-sm">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
            <div>
              <h4 class="mb-1"><?= esc($course['title'] ?? 'Course') ?></h4>
              <div class="text-muted">Course Code: <?= esc($course['description'] ?? 'N/A') ?></div>
              <div class="text-muted">Semester/Term: <?= esc($course['semester'] ?? 'Not set') ?></div>
              <div class="text-muted">Time: <?= esc($course['class_time'] ?? 'TBA') ?></div>
              <div class="text-muted">School Year: <?= esc($course['school_year'] ?? 'TBD') ?></div>
            </div>
            <a class="btn btn-outline-dark" href="<?= base_url('studentCourse') ?>">Back to My Courses</a>
          </div>

          <div class="tab-content">
            <div class="tab-pane fade show active" id="tab-home">
              <h5 class="mb-2">Course Information</h5>
              <p class="text-dark mb-1">Teacher: <?= esc($teacher['name'] ?? 'TBD') ?></p>
              <p class="text-dark">Description: <?= esc($course['description'] ?? 'No description') ?></p>
            </div>

            <div class="tab-pane fade" id="tab-assignments">
              <div class="d-flex justify-content-between align-items-center mb-2">
                <h5 class="mb-0">Assignments</h5>
                <span class="badge bg-dark">Total: <?= count($assignments ?? []) ?></span>
              </div>
              <?php if (!empty($assignments)): ?>
                <div class="list-group list-group-flush">
                  <?php foreach ($assignments as $a): ?>
                    <div class="list-group-item">
                      <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                          <div class="fw-semibold"><?= esc($a['title']) ?></div>
                          <?php if (!empty($a['description'])): ?>
                            <div class="text-dark small mb-1">Description: <?= nl2br(esc($a['description'])) ?></div>
                          <?php endif; ?>
                          <div class="text-muted small">Type: <?= esc($a['assignment_type']) ?> | Submit: <?= esc(ucfirst((string) $a['submit_type'])) ?></div>
                          <div class="text-muted small">Attempts: <?= $a['attempts_allowed'] === null ? 'Unlimited' : esc($a['attempts_allowed']) ?></div>
                          <?php if (!empty($a['points'])): ?>
                            <div class="text-muted small">Points: <?= esc($a['points']) ?></div>
                          <?php endif; ?>
                          <?php if ($a['available_after']): ?>
                            <div class="text-muted small">Available After: <?= esc(date('M d, Y h:i A', strtotime($a['available_after']))) ?></div>
                          <?php endif; ?>
                          <?php if ($a['due_date']): ?>
                            <div class="text-muted small">Due: <?= esc(date('M d, Y h:i A', strtotime($a['due_date']))) ?></div>
                          <?php endif; ?>
                            <?php if (!empty($a['submission'])): ?>
                              <div class="text-success small">Submitted: <?= esc(date('M d, Y h:i A', strtotime($a['submission']['submitted_at']))) ?></div>
                              <?php if (!empty($a['submission']['score'])): ?>
                                <div class="text-dark small">Score: <?= esc($a['submission']['score']) ?><?php if (!empty($a['points'])): ?>/<?= esc($a['points']) ?><?php endif; ?></div>
                              <?php endif; ?>
                              <?php if (!empty($a['submission']['feedback'])): ?>
                                <div class="text-muted small">Feedback: <?= esc($a['submission']['feedback']) ?></div>
                              <?php endif; ?>
                            <?php endif; ?>
                        </div>
                          <?php if (!empty($a['submission'])): ?>
                            <button class="btn btn-sm btn-success" disabled>Submitted</button>
                          <?php else: ?>
                            <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#submitModal<?= $a['id'] ?>">Submit</button>
                          <?php endif; ?>
                      </div>
                    </div>

                    <!-- Submit Modal -->
                    <div class="modal fade" id="submitModal<?= $a['id'] ?>" tabindex="-1" aria-labelledby="submitModalLabel<?= $a['id'] ?>" aria-hidden="true">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <form action="<?= base_url('student/assignments/' . $a['id'] . '/submit') ?>" method="post" enctype="multipart/form-data">
                            <?= csrf_field() ?>
                            <div class="modal-header">
                              <h5 class="modal-title" id="submitModalLabel<?= $a['id'] ?>">Submit: <?= esc($a['title']) ?></h5>
                              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                              <?php if (!empty($a['description'])): ?>
                                <div class="mb-3">
                                  <div class="text-muted small fw-semibold">Description</div>
                                  <div class="text-dark small"><?= nl2br(esc($a['description'])) ?></div>
                                </div>
                              <?php endif; ?>
                              <?php if (strtolower((string) $a['submit_type']) === 'text'): ?>
                                <div class="mb-3">
                                  <label for="content<?= $a['id'] ?>" class="form-label">Your Answer</label>
                                  <textarea class="form-control" id="content<?= $a['id'] ?>" name="content" rows="5" required></textarea>
                                </div>
                              <?php else: ?>
                                <div class="mb-3">
                                  <label for="file<?= $a['id'] ?>" class="form-label">Upload File</label>
                                  <input type="file" class="form-control" id="file<?= $a['id'] ?>" name="submission_file" required>
                                </div>
                              <?php endif; ?>
                            </div>
                            <div class="modal-footer">
                              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                              <button type="submit" class="btn btn-dark">Submit</button>
                            </div>
                          </form>
                        </div>
                      </div>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No assignments posted yet.</p>
              <?php endif; ?>
            </div>

            <div class="tab-pane fade" id="tab-grades">
              <h5 class="mb-2">Grades</h5>
              <p class="text-muted">Grades will appear here when available.</p>
            </div>

            <div class="tab-pane fade" id="tab-people">
              <h5 class="mb-3">People</h5>
              <?php if ($teacher): ?>
                <div class="d-flex align-items-center mb-3">
                  <div class="me-3">
                    <span class="badge bg-dark">Teacher</span>
                  </div>
                  <div>
                    <div class="fw-semibold"><?= esc($teacher['name']) ?></div>
                    <div class="text-muted small"><?= esc($teacher['email'] ?? '') ?></div>
                  </div>
                </div>
              <?php endif; ?>

              <div class="fw-semibold mb-2">Classmates</div>
              <?php if (!empty($classmates)): ?>
                <div class="list-group list-group-flush">
                  <?php foreach ($classmates as $mate): ?>
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                      <div>
                        <div class="fw-semibold"><?= esc($mate['name'] ?? 'Student') ?></div>
                        <div class="text-muted small"><?= esc($mate['email'] ?? '') ?></div>
                      </div>
                      <span class="badge bg-secondary text-uppercase"><?= esc($mate['role'] ?? 'student') ?></span>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php else: ?>
                <p class="text-muted">No classmates listed yet.</p>
              <?php endif; ?>
            </div>
          </div>

        </div>
      </div>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
