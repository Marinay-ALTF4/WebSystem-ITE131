<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!-- Header -->
  <?php include('app/Views/templates/header.php'); ?>

  <div class="container my-5">
    <div class="card shadow-sm">
      <div class="card-body">

        <?php $role = $role ?? session()->get('role'); ?>

        <!-- Welcome -->
        <h3 class="mb-3"><i class="bi bi-person-circle me-2"></i>Welcome, <?= esc(session()->get('name') ?? 'User') ?>!</h3>
        <p class="text-muted mb-4">Role: <strong><?= esc($role ?? 'User') ?></strong></p>
        <hr>

        <!-- ================= ADMIN DASHBOARD ================= -->
        <?php if ($role === 'admin'): ?>
          <h4 class="mb-3"><i class="bi bi-speedometer2 me-2"></i>Admin Dashboard</h4>

          <p>Total Users: <strong><?= isset($data['usersCount']) ? (int)$data['usersCount'] : 0 ?></strong></p>

          <?php if (!empty($data['recentUsers'])): ?>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['recentUsers'] as $u): ?>
                    <tr>
                      <td><?= (int)$u['id'] ?></td>
                      <td><?= esc($u['name']) ?></td>
                      <td><?= esc($u['email']) ?></td>
                      <td><?= esc($u['role']) ?></td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No users found.</p>
          <?php endif; ?>

          <hr>
          <h4 class="card-title mb-3">Available Courses</h4>

          <?php if (!empty($data['courses'])): ?>
            <div class="list-group">
              <?php foreach ($data['courses'] as $course): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-1"><?= esc($course['title']); ?></h5>
                    <p class="text-muted mb-0"><?= esc($course['description']); ?></p>
                  </div>
                  <div>
                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload'); ?>" 
                      class="btn btn-primary btn-sm rounded-pill">
                      Add Material
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted text-center mt-3">No courses found.</p>
          <?php endif; ?>

        <!-- ================= TEACHER DASHBOARD ================= -->
        <?php elseif ($role === 'teacher'): ?>
          <h4 class="mb-3"><i class="bi bi-journal-text me-2"></i>Teacher Dashboard</h4>

          <h5>My Students</h5>
          <?php if (!empty($data['students'])): ?>
            <ul class="list-group mt-3 mb-4">
              <?php foreach ($data['students'] as $s): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <?= esc($s['name']) ?>
                  <span class="badge bg-secondary">Student</span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-muted">No students assigned yet.</p>
          <?php endif; ?>

          <hr>
          <h4 class="card-title mb-3">My Courses</h4>

          <?php if (!empty($data['courses'])): ?>
            <div class="list-group">
              <?php foreach ($data['courses'] as $course): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-1"><?= esc($course['title']); ?></h5>
                    <p class="text-muted mb-0"><?= esc($course['description']); ?></p>
                  </div>
                  <div>
                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload'); ?>" 
                      class="btn btn-primary btn-sm rounded-pill">
                      Add Material
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
          <?php endif; ?>

          <?php if (!empty($data['materials'])): ?>
            <div class="mt-4">
              <h5><i class="bi bi-folder2-open me-2"></i>Uploaded Materials</h5>
              <table class="table table-bordered table-striped mt-3">
                <thead class="table-secondary">
                  <tr><th>#</th><th>File Name</th><th>Action</th></tr>
                </thead>
                <tbody>
                  <?php foreach ($data['materials'] as $index => $mat): ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td><?= esc($mat['file_name']) ?></td>
                      <td>
                        <a href="<?= site_url('materials/delete/' . $mat['id']) ?>" 
                          class="btn btn-danger btn-sm" 
                          onclick="return confirm('Delete this file?')">
                          <i class="bi bi-trash"></i> Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>

        <!-- ================= STUDENT DASHBOARD ================= -->
        <?php else: ?>
          <h4 class="mb-3"><i class="bi bi-book me-2"></i>Student Dashboard</h4>

          <!-- Student Profile -->
          <h5><i class="bi bi-person-badge me-2"></i>My Profile</h5>
          <?php if (!empty($data['profile'])): ?>
            <div class="row g-3 mb-4 mt-2">
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Name: <strong><?= esc($data['profile']['name']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Email: <strong><?= esc($data['profile']['email']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Course: <strong><?= esc($data['profile']['course_name'] ?? 'N/A') ?></strong></div>
              </div>
            </div>
          <?php endif; ?>

          <hr>

          <!-- Course Materials -->
          <h5><i class="bi bi-file-earmark-arrow-down me-2"></i>Course Materials</h5>

          <?php if (!empty($data['materials']) && !empty($data['courses'])): ?>
            <div class="accordion mt-3" id="materialsAccordion">
              <?php foreach ($data['courses'] as $course): ?>
                <?php
                  $courseMaterials = array_filter($data['materials'], function($mat) use ($course) {
                    return $mat['course_id'] == $course['id'];
                  });
                ?>

                <?php if (!empty($courseMaterials)): ?>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $course['id'] ?>">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $course['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $course['id'] ?>">
                        <?= esc($course['title']); ?>
                      </button>
                    </h2>
                    <div id="collapse<?= $course['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $course['id'] ?>" data-bs-parent="#materialsAccordion">
                      <div class="accordion-body">
                        <table class="table table-bordered table-hover bg-white mt-2">
                          <thead class="table-primary">
                            <tr><th>#</th><th>File Name</th><th>Action</th></tr>
                          </thead>
                          <tbody>
                            <?php $index = 1; foreach ($courseMaterials as $mat): ?>
                              <tr>
                                <td><?= $index++ ?></td>
                                <td><?= esc($mat['file_name']) ?></td>
                                <td>
                                  <a href="<?= site_url('materials/download/' . $mat['id']) ?>" 
                                    class="btn btn-primary btn-sm">
                                    <i class="bi bi-download"></i> Download
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted mt-3">No materials available yet.</p>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
