<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload Material</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light p-4">
<div class="container">
    <h4>Upload Material for Course ID: <?= $course_id ?></h4>

    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger">
            <?php
                $errors = session()->getFlashdata('error');
                if(is_array($errors)) {
                    foreach($errors as $err) echo $err . '<br>';
                } else {
                    echo $errors;
                }
            ?>
        </div>
    <?php endif; ?>

    <form action="<?= site_url('teacher/course/' . $course_id . '/upload') ?>" method="post" enctype="multipart/form-data" class="border p-3 bg-white rounded">
        <div class="mb-3">
            <label for="material_file" class="form-label">Choose File</label>
            <input type="file" name="material_file" id="material_file" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
</body>
</html>
