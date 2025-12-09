<?php
include('app/Views/templates/header.php');
?>

<div class="container my-5">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h4 class="mb-1">Submission</h4>
      <div class="text-muted">Assignment: <?= esc($assignment['title'] ?? '') ?></div>
      <div class="text-muted">Student: <?= esc($student['name'] ?? '') ?> (<?= esc($student['email'] ?? '') ?>)</div>
    </div>
    <a class="btn btn-outline-dark" href="<?= base_url('teacher/course/' . ($assignment['course_id'] ?? 0) . '/assignments/' . ($assignment['id'] ?? 0)) ?>">Back</a>
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

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <div class="d-flex justify-content-between flex-wrap gap-2 mb-3">
        <div>
          <div class="text-muted small">Submitted At</div>
          <div class="fw-semibold">
            <?= !empty($submission['submitted_at']) ? esc(date('M d, Y h:i A', strtotime($submission['submitted_at']))) : 'Not submitted' ?>
          </div>
        </div>
        <?php if (!empty($assignment['points'])): ?>
          <div>
            <div class="text-muted small">Points Possible</div>
            <div class="fw-semibold"><?= esc($assignment['points']) ?></div>
          </div>
        <?php endif; ?>
        <?php if (!empty($submission['score'])): ?>
          <div>
            <div class="text-muted small">Score</div>
            <div class="fw-semibold">
              <?= esc($submission['score']) ?><?php if (!empty($assignment['points'])): ?>/<?= esc($assignment['points']) ?><?php endif; ?>
            </div>
          </div>
        <?php endif; ?>
      </div>

      <?php if (!empty($submission['content'])): ?>
        <div class="mb-3">
          <div class="fw-semibold mb-1">Text Answer</div>
          <div class="border rounded p-3 bg-light">
            <pre class="mb-0" style="white-space: pre-wrap; word-break: break-word;"><?= esc($submission['content']) ?></pre>
          </div>
        </div>
      <?php endif; ?>

      <?php if (!empty($submission['file_path'])): ?>
        <div class="mb-3">
          <div class="fw-semibold mb-1">Uploaded File</div>
          <a class="btn btn-outline-dark btn-sm" href="<?= base_url($submission['file_path']) ?>" target="_blank" rel="noopener">Open File</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header fw-semibold">Grade</div>
    <div class="card-body">
      <form action="<?= base_url('teacher/assignments/' . ($assignment['id'] ?? 0) . '/submissions/' . ($student['id'] ?? 0) . '/grade') ?>" method="post">
        <?= csrf_field() ?>
        <div class="row g-3">
          <div class="col-md-4">
            <label for="score" class="form-label">Score</label>
            <input type="number" name="score" id="score" class="form-control" min="0" <?php if (!empty($assignment['points'])): ?>max="<?= esc($assignment['points']) ?>"<?php endif; ?> value="<?= esc($submission['score'] ?? '') ?>">
            <?php if (!empty($assignment['points'])): ?>
              <div class="text-muted small mt-1">Out of <?= esc($assignment['points']) ?> points.</div>
            <?php endif; ?>
          </div>
          <div class="col-12">
            <label for="feedback" class="form-label">Feedback</label>
            <textarea name="feedback" id="feedback" class="form-control" rows="4" placeholder="Provide feedback (optional)"><?= esc($submission['feedback'] ?? '') ?></textarea>
          </div>
        </div>
        <div class="mt-3 d-flex justify-content-end gap-2">
          <button type="submit" class="btn btn-dark">Save Grade</button>
        </div>
      </form>
    </div>
  </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
