<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form action="<?= base_url('teacher/course/add') ?>" method="post">
      <?= csrf_field() ?>
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="courseTitle" class="form-label">Course Title</label>
            <input type="text" class="form-control" name="title" id="courseTitle" required>
          </div>
          <div class="mb-3">
            <label for="courseDescription" class="form-label">Description</label>
            <textarea class="form-control" name="description" id="courseDescription" rows="3" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-success">Add Course</button>
        </div>
      </div>
    </form>
  </div>
</div>
