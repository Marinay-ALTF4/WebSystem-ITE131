<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-2">
  <div class="container-sm">

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">

      <ul class="navbar-nav me-auto">
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 me-2 text-light bg-dark bg-opacity-50" href="<?= base_url('dashboard') ?>">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 me-2 text-light bg-dark bg-opacity-50" href="<?= base_url('about') ?>">About</a>
        </li>
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 text-light bg-dark bg-opacity-50" href="<?= base_url('contact') ?>">Contact</a>
        </li>
      </ul>


      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link border rounded px-3 py-2 text-light bg-dark bg-opacity-50" href="<?= base_url('logout') ?>">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
