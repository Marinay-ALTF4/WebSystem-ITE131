<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Courses</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!--  Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!--  Include header template -->
  <?php include('app/Views/templates/header.php'); ?>

  <!--  Main courses container -->
  <div class="d-flex justify-content-center align-items-start mt-5">
    <div class="card shadow p-4 border border-dark" style="max-width: 1000px; width: 100%; background-color: #e9ecef;">
      <div class="card-body">

        <!--  Page title -->
        <h3 class="card-title mb-4 text-dark">
          My Courses
        </h3>

        <p class="text-dark">
          Hello, <?= session()->get('name') ?? 'User' ?>! Here you can view your enrolled courses and enroll in new ones.
        </p>

        <hr>

        <!--  Enrollment Alert -->
        <div id="enroll-alert" class="mb-3"></div>

        <div class="row g-4">
          <!--  Enrolled Courses -->
          <div class="col-12 col-lg-6">
            <div class="card shadow-sm">
              <div class="card-header fw-bold">Enrolled Courses</div>
              <ul id="enrolled-courses" class="list-group list-group-flush">
                <?php if (!empty($data['enrolledCourses'])): ?>
                  <?php foreach ($data['enrolledCourses'] as $course): ?>
                    <li class="list-group-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="fw-semibold"><?= esc($course['title']) ?></div>
                          <small class="text-muted"><?= esc($course['description']) ?></small>
                        </div>
                        <span class="badge text-bg-success">Enrolled</span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="list-group-item text-muted empty-state">You have not enrolled in any course yet.</li>
                <?php endif; ?>
              </ul>
            </div>
          </div>

          <!--  Pending + Available Courses -->
          <div class="col-12 col-lg-6">
            <div class="card shadow-sm mb-3">
              <div class="card-header fw-bold">Pending Approval</div>
              <ul id="pending-courses" class="list-group list-group-flush">
                <?php if (!empty($data['pendingCourses'])): ?>
                  <?php foreach ($data['pendingCourses'] as $course): ?>
                    <li class="list-group-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="fw-semibold"><?= esc($course['title']) ?></div>
                          <small class="text-muted"><?= esc($course['description']) ?></small>
                        </div>
                        <span class="badge text-bg-warning">Pending</span>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="list-group-item text-muted empty-state">No pending requests.</li>
                <?php endif; ?>
              </ul>
            </div>

            <div class="card shadow-sm">
              <div class="card-header fw-bold">Available Courses</div>
              <ul id="available-courses" class="list-group list-group-flush">
                <?php if (!empty($data['availableCourses'])): ?>
                  <?php foreach ($data['availableCourses'] as $course): ?>
                    <li class="list-group-item">
                      <div class="d-flex justify-content-between align-items-start">
                        <div>
                          <div class="fw-semibold"><?= esc($course['title']) ?></div>
                          <small class="text-muted"><?= esc($course['description']) ?></small>
                        </div>
                        <!--  Enroll button with course ID -->
                        <button class="btn btn-dark btn-sm enroll-btn"
                                data-course-id="<?= (int)$course['id'] ?>">Enroll</button>
                      </div>
                    </li>
                  <?php endforeach; ?>
                <?php else: ?>
                  <li class="list-group-item text-muted">No courses available.</li>
                <?php endif; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

          <!--  jQuery and Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  
       <!-- AJAX SCRIPT FOR ENROLLMENT-->
  
  <script>
  $(function () {
        //  Listen for clicks on any "Enroll" button
    $('#available-courses').on('click', '.enroll-btn', function (e) {
      e.preventDefault(); // stop page reload

      var $btn = $(this);
      var courseId = $btn.data('course-id'); // get the course ID

      //  Send AJAX POST request to enroll endpoint
      $.post('<?= base_url('courses/enroll') ?>', { 
        course_id: courseId,
        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
      })
        .done(function (data) {
          //  Prepare alert message
          var message = (data && data.message) ? data.message :
                        (data.success ? 'Enrolled successfully.' : 'Enrollment failed.');
          var alertClass = (data && data.success) ? 'alert-success' : 'alert-danger';

          //  Show alert message
          $('#enroll-alert').html(
            '<div class="alert ' + alertClass + ' alert-dismissible fade show" role="alert">' +
              message +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
          );

          //  If enrollment was successful
          if (data && data.success) {
            var status = data.status || 'pending';
            var $item = $btn.closest('li');
            var title = $item.find('.fw-semibold').text();
            var desc = $item.find('small.text-muted').text();
            var targetList = status === 'accepted' ? '#enrolled-courses' : '#pending-courses';
            var badgeClass = status === 'accepted' ? 'text-bg-success' : 'text-bg-warning';
            var badgeLabel = status === 'accepted' ? 'Enrolled' : 'Pending';

            $(targetList + ' .empty-state').remove();

            $(targetList).prepend(
              '<li class="list-group-item">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                  '<div>' +
                    '<div class="fw-semibold">' + $('<div>').text(title).html() + '</div>' +
                    '<small class="text-muted">' + $('<div>').text(desc).html() + '</small>' +
                  '</div>' +
                  '<span class="badge ' + badgeClass + '">' + badgeLabel + '</span>' +
                '</div>' +
              '</li>'
            );

            // Disable the button to avoid duplicate requests
            $btn.prop('disabled', true).text(badgeLabel);

            // Remove the course from available courses list
            $item.remove();
          }
        })
        .fail(function (xhr, status, error) {
          console.log('AJAX Error:', xhr.status, xhr.statusText, error);
          var errorMessage = 'Network error.';
          
          if (xhr.status === 404) {
            errorMessage = 'Enrollment endpoint not found.';
          } else if (xhr.status === 403) {
            errorMessage = 'Access forbidden. Please refresh the page and try again.';
          } else if (xhr.status === 500) {
            errorMessage = 'Server error. Please try again later.';
          }
          
          $('#enroll-alert').html(
            '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
              errorMessage + ' (Status: ' + xhr.status + ')' +
              '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
            '</div>'
          );
        });
    });
  });
  </script>
</body>
</html>
