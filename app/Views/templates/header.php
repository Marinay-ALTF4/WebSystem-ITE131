<?php $role = session()->get('role') ? strtolower(session()->get('role')) : ''; ?>

<!-- Bootstrap 5 & Icons -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<!-- jQuery -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

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
              <span id="notifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $notificationCount ?? 0 ?></span>
            </a>

            <ul class="dropdown-menu dropdown-menu-end shadow" style="width: 350px; max-height: 400px; overflow-y: auto;">
              <li class="dropdown-header d-flex justify-content-between align-items-center border-bottom">
                <strong>Notifications</strong>
                <button class="btn btn-sm btn-link p-0 text-primary" id="markAllRead">Mark all as read</button>
              </li>
              <div id="notifList" class="p-2"></div>
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
$(document).ready(function() {
  const $notifList = $('#notifList');
  const $notifBadge = $('#notifBadge');

  // Function to load notifications using jQuery AJAX
  function loadNotifications() {
    $.get('<?= base_url("notifications") ?>')
      .done(function(data) {
        // Update badge with unread count
        const unreadCount = data.unread_count || 0;
        $notifBadge.text(unreadCount);
        
        if (unreadCount > 0) {
          $notifBadge.show();
        } else {
          $notifBadge.hide();
        }
        
        // Clear existing notifications
        $notifList.empty();
        
        // Check if there are notifications
        if (!data.notifications || data.notifications.length === 0) {
          $notifList.html('<div class="alert alert-info text-center mb-0">No notifications</div>');
        } else {
          // Populate dropdown with Bootstrap alert styling
          $.each(data.notifications, function(index, n) {
            const isRead = n.is_read == 1;
            const createdDate = new Date(n.created_at).toLocaleDateString();
            
            // Use Bootstrap alert classes based on read status
            const alertClass = isRead ? 'alert-secondary' : 'alert-info';
            const textClass = isRead ? 'text-muted' : '';
            
            const notificationHtml = `
              <div class="alert ${alertClass} alert-dismissible mb-2 p-2" role="alert" data-notification-id="${n.id}">
                <div class="d-flex justify-content-between align-items-start">
                  <div class="flex-grow-1 ${textClass}">
                    <strong class="d-block mb-1">${n.message}</strong>
                    <small class="text-muted">${createdDate}</small>
                  </div>
                  ${!isRead ? `
                    <button type="button" class="btn btn-sm btn-outline-primary ms-2 mark-read-btn" data-id="${n.id}">
                      Mark as Read
                    </button>
                  ` : ''}
                </div>
              </div>
            `;
            
            $notifList.append(notificationHtml);
          });
        }
      })
      .fail(function(xhr, status, error) {
        console.error('Error loading notifications:', {
          status: xhr.status,
          statusText: xhr.statusText,
          error: error,
          response: xhr.responseText
        });
        
        let errorMessage = 'Error loading notifications';
        if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMessage = xhr.responseJSON.error;
        }
        
        $notifList.html('<div class="alert alert-danger text-center mb-0">' + errorMessage + '</div>');
        $notifBadge.hide();
      });
  }

  // Function to mark notification as read using jQuery AJAX
  function markAsRead(notificationId) {
    $.post('<?= base_url("notifications/mark_read") ?>/' + notificationId, {
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    })
      .done(function(data) {
        if (data.success) {
          // Remove the notification from the list
          $notifList.find('[data-notification-id="' + notificationId + '"]').fadeOut(300, function() {
            $(this).remove();
            
            // Reload notifications to update badge count
            loadNotifications();
          });
        } else {
          console.error('Mark as read failed:', data);
          alert('Failed to mark notification as read: ' + (data.error || 'Unknown error'));
        }
      })
      .fail(function(xhr, status, error) {
        console.error('Error marking notification as read:', {
          status: xhr.status,
          statusText: xhr.statusText,
          error: error,
          response: xhr.responseText
        });
        
        let errorMessage = 'Failed to mark notification as read';
        if (xhr.responseJSON && xhr.responseJSON.error) {
          errorMessage = xhr.responseJSON.error;
        }
        alert(errorMessage);
      });
  }

  // Event delegation for dynamically added buttons
  $(document).on('click', '.mark-read-btn', function() {
    const notificationId = $(this).data('id');
    markAsRead(notificationId);
  });

  // Mark all as read functionality
  $('#markAllRead').on('click', function() {
    $.post('<?= base_url("notifications/mark_all") ?>', {
      '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
    })
      .done(function() {
        loadNotifications();
      })
      .fail(function() {
        console.error('Error marking all notifications as read');
        alert('Failed to mark all notifications as read');
      });
  });

  // Initialize badge with server-side count
  const initialCount = <?= $notificationCount ?? 0 ?>;
  $notifBadge.text(initialCount);
  
  if (initialCount > 0) {
    $notifBadge.show();
  } else {
    $notifBadge.hide();
  }
  
  // Load notifications and set up auto-refresh
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
    min-width: 18px;
    text-align: center;
}
#notifList .alert {
    font-size: 0.9rem;
    border-radius: 0.375rem;
    cursor: pointer;
    transition: opacity 0.3s ease;
}
#notifList .alert-info {
    border-left: 3px solid #0dcaf0;
}
#notifList .alert-secondary {
    border-left: 3px solid #6c757d;
}
#notifList .alert:hover {
    opacity: 0.9;
}
.mark-read-btn {
    white-space: nowrap;
}
</style>
