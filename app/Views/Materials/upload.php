<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Upload Material</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">

<div class="container">
    <h4 class="mb-4">Upload Course Materials</h4>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php elseif (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('materials/upload/' . $course_id) ?>" method="post" enctype="multipart/form-data" class="border p-3 bg-white rounded">
        <div class="mb-3">
            <label for="material_file" class="form-label">Choose File</label>
            <input type="file" name="material_file" id="material_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>

    <hr>

    <h5>Uploaded Materials</h5>
    <ul class="list-group">
        <?php if (!empty($materials)): ?>
            <?php foreach ($materials as $material): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?= esc($material['file_name']) ?>
                    <div>
                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-success btn-sm">Download</a>
                        <a href="<?= site_url('materials/delete/' . $material['id']) ?>" class="btn btn-danger btn-sm">Delete</a>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li class="list-group-item text-muted">No materials uploaded yet.</li>
        <?php endif; ?>
    </ul>
</div>

</body>
</html>
