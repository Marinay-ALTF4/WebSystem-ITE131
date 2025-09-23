<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-sm">
  
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 me-2 text-light" href="<?= base_url('dashboard') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 me-2 text-light" href="<?= base_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 text-light" href="<?= base_url('contact') ?>">Contact</a>
        </li>
      </ul>

      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 text-light" href="<?= base_url('logout') ?>">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="d-flex justify-content-center align-items-start mt-5">
  <div class="card shadow p-4 border border-dark" style="max-width: 800px; width: 100%; background-color: #e9ecef;">
    <div class="card-body">
      <h3 class="card-title mb-4" style="color: #000000ff;">Welcome to ITE311-MARINAY</h3>

      <p style="color: #000000ff;">
        Hello, <?= session()->get('name') ?? 'User' ?>! Welcome to your dashboard. Here you can get an overview of the platform, learn how to manage your projects efficiently, and explore the features designed to help you collaborate and track your progress.
      </p>

      <p style="color: #000000ff;">
        ITE311-MARINAY provides a professional and user-friendly interface to make your workflow seamless. Navigate through the top menu to access About, Contact, or manage your profile.
      </p>

      <p style="color: #000000ff;">
        Stay organized, stay productive, and enjoy a smooth experience while using ITE311-MARINAY website.
      </p>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
