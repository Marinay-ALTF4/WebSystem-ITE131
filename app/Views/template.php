<?php $role = session()->get('role') ? strtolower(session()->get('role')) : ''; ?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-1">
  <div class="container-sm">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="btn btn-sm btn-outline-light me-1" href="<?= base_url('dashboard') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-sm btn-outline-light me-1" href="<?= base_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="btn btn-sm btn-outline-light me-1" href="<?= base_url('contact') ?>">Contact</a>
        </li>
        <?php if ($role === 'admin'): ?>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light me-1" href="#">Admin Panel</a></li>
        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light me-1" href="#">My Classes</a></li>
        <?php elseif ($role === 'student'): ?>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light me-1" href="#">My Courses</a></li>
        <?php endif; ?>
      </ul>

      <ul class="navbar-nav ms-auto">
        <?php if (session()->get('isLoggedIn')): ?>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light" href="<?= base_url('logout') ?>">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light me-1" href="<?= base_url('login') ?>">Login</a></li>
          <li class="nav-item"><a class="btn btn-sm btn-outline-light" href="<?= base_url('register') ?>">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
