<?php
include('app/Views/templates/header.php');
?>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">Assignment</h4>
      <div class="text-muted">Course: <?= esc($course['title'] ?? '') ?></div>
    </div>
    <a class="btn btn-outline-dark" href="<?= base_url('teacher/course/' . ($course['id'] ?? 0)) ?>">Back to Course</a>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h5 class="mb-1"><?= esc($assignment['title'] ?? 'Assignment') ?></h5>
      <?php if (!empty($assignment['points'])): ?>
        <div class="text-muted small">Points: <?= esc($assignment['points']) ?></div>
      <?php endif; ?>
      <div class="text-muted small">Type: <?= esc($assignment['assignment_type'] ?? '') ?> | Submit: <?= esc(ucfirst((string) ($assignment['submit_type'] ?? ''))) ?></div>
      <div class="text-muted small">Attempts: <?= $assignment['attempts_allowed'] === null ? 'Unlimited' : esc($assignment['attempts_allowed']) ?></div>
      <div class="text-muted small">Due: <?= $assignment['due_date'] ? esc(date('M d, Y h:i A', strtotime($assignment['due_date']))) : 'None' ?></div>
      <div class="text-muted small">Available After: <?= $assignment['available_after'] ? esc(date('M d, Y h:i A', strtotime($assignment['available_after']))) : 'Immediately' ?></div>
      <?php if (!empty($assignment['description'])): ?>
        <hr class="my-3">
        <div>
          <div class="fw-semibold mb-1">Description</div>
          <div class="text-dark"><?= nl2br(esc($assignment['description'])) ?></div>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">Submissions</div>
    <div class="card-body p-0">
      <?php if (!empty($statuses)): ?>
        <div class="table-responsive">
          <table class="table mb-0 align-middle">
            <thead class="table-light">
              <tr>
                <th>Student</th>
                <th>Status</th>
                <th>Submitted At</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($statuses as $row): ?>
                <?php
                  $status = $row['status'];
                  $badge = 'secondary';
                  $label = 'Pending';
                  if ($status === 'submitted') { $badge = 'success'; $label = 'Submitted'; }
                  if ($status === 'late') { $badge = 'warning'; $label = 'Late'; }
                  if ($status === 'did_not_pass') { $badge = 'danger'; $label = 'Did Not Pass'; }
                ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= esc($row['student']['name'] ?? 'Student') ?></div>
                    <div class="text-muted small"><?= esc($row['student']['email'] ?? '') ?></div>
                  </td>
                  <td><span class="badge bg-<?= $badge ?> text-uppercase"><?= esc($label) ?></span></td>
                  <td class="text-muted small">
                    <?php if (!empty($row['submission']['submitted_at'])): ?>
                      <?= esc(date('M d, Y h:i A', strtotime($row['submission']['submitted_at']))) ?>
                    <?php else: ?>
                      —
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if (!empty($row['submission'])): ?>
                      <a class="btn btn-sm btn-outline-dark" href="<?= base_url('teacher/assignments/' . ($assignment['id'] ?? 0) . '/submissions/' . ($row['student']['id'] ?? 0)) ?>">View</a>
                    <?php else: ?>
                      <span class="text-muted small">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php else: ?>
        <p class="text-muted m-3">No students yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
