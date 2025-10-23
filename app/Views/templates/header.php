<?php $role = session()->get('role') ? strtolower(session()->get('role')) : ''; ?>

<!-- Bootstrap 5 & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark py-1">
  <div class="container-sm">
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
      aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <!-- Left side -->
      <ul class="navbar-nav me-auto">
        <?php if ($role === 'admin' || $role === 'teacher'): ?>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="#">File Upload</a>
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

          <!-- Notification Bell Dropdown -->
          <li class="nav-item dropdown" id="notificationDropdown">
            <a class="nav-link position-relative border rounded px-3 py-1 ms-2 text-white hover-gray" href="#" id="notifDropdown" role="button" data-bs-toggle="dropdown">
              <i class="bi bi-bell fs-5"></i>
              <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">0</span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 300px;">
              <li class="dropdown-header d-flex justify-content-between align-items-center">
                <span>Notifications</span>
                <button class="btn btn-sm btn-link p-0" id="markAllRead">Mark all as read</button>
              </li>
              <div id="notifList" class="list-group"></div>
            </ul>
          </li>

          <!-- Logout -->
          <li class="nav-item">
            <a class="nav-link border rounded px-3 py-1 ms-2 text-white hover-gray" href="<?= base_url('logout') ?>">Logout</a>
          </li>

        <?php else: ?>
          <!-- Guest links -->
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

<script>
document.addEventListener('DOMContentLoaded', () => {
  const notifList = document.getElementById('notifList');
  const notifBadge = document.getElementById('notifBadge');

  function loadNotifications() {
    // Load notifications
    fetch('/notifications')
      .then(res => res.json())
      .then(data => {
        notifList.innerHTML = '';
        data.forEach(n => {
          notifList.innerHTML += `
            <li class="dropdown-item d-flex justify-content-between align-items-start ${n.is_read ? 'text-muted' : ''}">
              <div>
                <div>${n.message}</div>
                <small class="text-secondary">${n.created_at}</small>
              </div>
              ${!n.is_read ? `<span class='badge bg-primary rounded-pill'>New</span>` : ''}
            </li>
          `;
        });
      });

    // Load unread count
    fetch('/notifications/count')
      .then(res => res.json())
      .then(data => {
        notifBadge.textContent = data.unread_count;
        notifBadge.style.display = data.unread_count > 0 ? 'inline-block' : 'none';
      });
  }

  document.getElementById('markAllRead').addEventListener('click', () => {
    fetch('/notifications/mark_all', { method: 'POST' })
      .then(() => loadNotifications());
  });

  loadNotifications();
  setInterval(loadNotifications, 60000); // Refresh every 60 seconds
});
</script>

<style>
.hover-gray:hover {
    background-color: #6c757d !important;
    color: #fff !important;
}
#notifBadge {
    font-size: 0.7rem;
    padding: 0.25em 0.4em;
}
</style>
