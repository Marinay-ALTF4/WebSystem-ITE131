<?php $role = session()->get('role') ? strtolower(session()->get('role')) : ''; ?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-1">
  <div class="container-sm">
    
   
    
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    
    <div class="collapse navbar-collapse" id="navbarNav">
    
      <ul class="navbar-nav me-auto">




        <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href= "<?= base_url('Materials/upload')?>">File Upload </a>
          </li>



        <?php elseif ($role === 'teacher'): ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href= "<?= base_url('upload')?>">File Upload </a>
          </li>




        <?php elseif ($role === 'student'): ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('studentCourse') ?>">My Courses</a>
          </li>
          
        <?php endif; ?>
      </ul>

      <!-- Right side -->
      <ul class="navbar-nav">
        <?php if (session()->get('isLoggedIn')): ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('logout') ?>">Logout</a>
          </li>
          <?php if ($role !== 'student'): ?>
          
          <?php endif; ?>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('login') ?>">Login</a>
          </li>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('register') ?>">Register</a>
          </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
  /* Keep text white + add gray hover background */
  .hover-gray:hover {
    background-color: #6c757d !important; /* Bootstrap gray */
    color: #fff !important; /* force text white */
  }
</style>
