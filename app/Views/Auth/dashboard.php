<!DOCTYPE html>

<html lang="en">
<head>
  <meta charset="UTF-8">
  
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php include('app/Views/templates/header.php'); ?>
<div class="d-flex justify-content-center align-items-start mt-5">
  <div class="card shadow p-4 border border-dark" style="max-width: 800px; width: 100%; background-color: #e9ecef;">
    <div class="card-body">
      <h3 class="card-title mb-4" style="color: #000000ff;">Welcome <?= esc($role ?? (session()->get('role') ?? 'User')) ?>!</h3>


      <p style="color: #000000ff;">
        Hello, <?= session()->get('name') ?? 'User' ?>! Welcome to your dashboard. Here you can get an overview of the platform, learn how to manage your projects efficiently, and explore the features designed to help you collaborate and track your progress.
      </p>

      <hr>
      <?php // Role-conditional sections -- role based sections ?>
      <?php if (($role ?? '') === 'admin'): ?>
        <h5 class="mb-3" style="color: #000000ff;">Admin Overview</h5>
        <!-- Admin section: total users. -->
        <p class="mb-2" style="color: #000000ff;">Total users: <strong><?= isset($data['usersCount']) ? (int) $data['usersCount'] : 0 ?></strong></p>
        <?php if (!empty($data['recentUsers'])): ?>
          <div class="table-responsive">
            <table class="table table-sm table-striped table-bordered">
              <thead>
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
                    <td><?= (int) $u['id'] ?></td>
                    <td><?= esc($u['name']) ?></td>
                    <td><?= esc($u['email']) ?></td>
                    <td><?= esc($u['role']) ?></td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        <?php else: ?>
          <p style="color:#000000ff;">No recent users found.</p>
        <?php endif; ?>
      <?php elseif (($role ?? '') === 'teacher'): ?>
        <h5 class="mb-3" style="color: #000000ff;">My Students</h5>
        <?php if (!empty($data['students'])): ?>
          <!-- Teacher section: listahan sa mga estudyante. -->
          <ul class="list-group">
            <?php foreach ($data['students'] as $s): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <span><?= esc($s['name']) ?> (<?= esc($s['email']) ?>)</span>
                <span class="badge bg-primary">Student</span>
                </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p style="color:#000000ff;">No students to display.</p>
        <?php endif; ?>
      <?php else: ?>
        <h5 class="mb-3" style="color: #000000ff;">My Profile</h5>
        <?php if (!empty($data['profile'])): ?>
          <!-- User/student section: ipakita ang personal nga info sa user. -->
          <div class="row g-3">
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
          <p style="color:#000000ff;">Profile not available.</p>
        <?php endif; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
