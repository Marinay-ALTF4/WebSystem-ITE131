<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta content="authenticity_token" name="csrf-param" />
<meta content="4sWPhTlJAmt1IcyNq1FCyivsAVhHqjiDCKRXOgOQock=" name="csrf-token" />

  <!--  Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!--  Include header template -->
  <?php include('app/Views/templates/header.php'); ?>

  <!--  Main dashboard container -->
  <div class="d-flex justify-content-center align-items-start mt-5">
    <div class="card shadow p-4 border border-dark" style="max-width: 800px; width: 100%; background-color: #e9ecef;">
      <div class="card-body">

        <!--  Welcome message -->
        <h3 class="card-title mb-4 text-dark">
          Welcome <?= esc($role ?? (session()->get('role') ?? 'User')) ?>!
        </h3>

        <p class="text-dark">
          Hello, <?= session()->get('name') ?? 'User' ?>! Welcome to your dashboard.
          Here you can get an overview of the platform, manage your tasks efficiently,
          and explore features to help you track progress.
        </p>

        <hr>

        <?php if (($role ?? '') === 'admin'): ?>

          <!-- ADMIN SECTION -->
          <h5 class="mb-3 text-dark">Admin Overview</h5>
          <p class="text-dark mb-2">
            Total users:
            <strong><?= isset($data['usersCount']) ? (int) $data['usersCount'] : 0 ?></strong>
          </p>

          <?php if (!empty($data['recentUsers'])): ?>
            <div class="table-responsive">
              <table class="table table-sm table-striped table-bordered">
                <thead>
                  <tr>
                    <th>ID</th><th>Name</th><th>Email</th><th>Role</th>
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
            <p class="text-dark">No recent users found.</p>
          <?php endif; ?>

        <?php elseif (($role ?? '') === 'teacher'): ?>

          <!-- TEACHER SECTION -->
          <h5 class="mb-3 text-dark">My Students</h5>

          <?php if (!empty($data['students'])): ?>
            <ul class="list-group">
              <?php foreach ($data['students'] as $s): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                  <span><?= esc($s['name']) ?> (<?= esc($s['email']) ?>)</span>
                  <span class="badge bg-primary">Student</span>
                </li>
              <?php endforeach; ?>
            </ul>
          <?php else: ?>
            <p class="text-dark">No students to display.</p>
          <?php endif; ?>

        <?php else: ?>

          <!-- STUDENT SECTION -->
          <h5 class="mb-3 text-dark">My Profile</h5>

          <?php if (!empty($data['profile'])): ?>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Name: <strong><?= esc($data['profile']['name']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Email: <strong><?= esc($data['profile']['email']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Role: <strong><?= esc($role ?? 'student') ?></strong></div>
              </div>
            </div>
          <?php else: ?>
            <p class="text-dark">Profile not available.</p>
          <?php endif; ?>

          <!-- STUDENT MATERIALS SECTION -->
          <h5 class="mb-3 text-dark">Course Materials</h5>

          <?php if (!empty($data['materials'])): ?>
            <table class="table table-bordered table-striped bg-white">
              <thead>
                <tr>
                  <th>#</th>
                  <th>File Name</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($data['materials'] as $index => $mat): ?>
                  <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= esc($mat['file_name']) ?></td>
                    <td>
                      <a href="<?= site_url('materials/download/' . $mat['id']) ?>" class="btn btn-success btn-sm">
                        Download
                      </a>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          <?php else: ?>
            <p class="text-dark">No materials available for your course yet.</p>
          <?php endif; ?>

        <?php endif; ?>

      </div>
    </div>
  </div>

  <!--  jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>