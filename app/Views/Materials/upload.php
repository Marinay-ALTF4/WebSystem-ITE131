<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Material</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light py-5">

<div class="container">
  <div class="card shadow border-0">
    <div class="card-header bg-dark text-white">
      <h4 class="mb-0">Upload Course Materials</h4>
    </div>

    <div class="card-body">

      <!-- Flash messages -->
      <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
      <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
      <?php endif; ?>

      <!-- Upload form -->
      <form 
  action="<?= site_url('admin/course/' . esc($course_id ?? '0') . '/upload') ?>" 
  method="POST" 
  enctype="multipart/form-data">

       

        <?= csrf_field() ?>

        <div class="mb-3">
          <label for="material_file" class="form-label fw-bold">Choose File</label>
          <input type="file" name="material_file" id="material_file" class="form-control" required>
          <div class="form-text">Allowed: pdf, doc, docx, ppt, pptx, jpg, png, mp4, zip (max 5MB)</div>
        </div>

        <button type="submit" class="btn btn-dark">Upload</button>
        <a href="<?= site_url('dashboard') ?>" class="btn btn-secondary">Back</a>
      </form>

      <hr>

      <!-- Uploaded materials list -->
      <h5 class="mb-3">Uploaded Materials</h5>
      <?php if (!empty($materials)): ?>
        <ul class="list-group">
          <?php foreach ($materials as $material): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <span><?= esc($material['file_name']) ?></span>
              <div>
                <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-success btn-sm">Download</a>
                <a href="<?= site_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this file?')">Delete</a>
              </div>
            </li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p class="text-muted">No materials uploaded yet.</p>
      <?php endif; ?>

    </div>
  </div>
</div>

</body>
</html>
