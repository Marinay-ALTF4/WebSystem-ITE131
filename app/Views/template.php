<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top py-2">
  <div class="container-sm">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left Side -->
      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link border border-white rounded px-3 py-2 me-2 text-white bg-transparent hover-gray" 
             href="<?= base_url('/') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border border-white rounded px-3 py-2 me-2 text-white bg-transparent hover-gray" 
             href="<?= base_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border border-white rounded px-3 py-2 text-white bg-transparent hover-gray" 
             href="<?= base_url('contact') ?>">Contact</a>
        </li>
      </ul>

      <!-- Right Side -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link border border-white rounded px-3 py-2 me-2 text-white bg-transparent hover-gray" 
             href="<?= base_url('login') ?>">Login</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border border-white rounded px-3 py-2 text-white bg-transparent hover-gray" 
             href="<?= base_url('register') ?>">Register</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<style>
/* Custom hover effect */
.hover-gray:hover {
  background-color: #6c757d !important; /* Bootstrap gray */
  color: white !important;
}
</style>
