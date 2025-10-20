

<div class="container mt-4">
    <?php if(session()->getFlashdata('success')): ?>
        <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
    <?php endif; ?>
    <?php if(session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
    <?php endif; ?>

    <form action="<?= site_url('admin/course/'.$course_id.'/upload') ?>" method="post" enctype="multipart/form-data" class="border p-3 bg-white rounded">
        <div class="mb-3">
            <label for="material_file" class="form-label">Choose File</label>
            <input type="file" name="material_file" id="material_file" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Upload</button>
    </form>
</div>
