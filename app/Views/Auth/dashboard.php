<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body class="bg-light">

  <!-- Header -->
  <?php include('app/Views/templates/header.php'); ?>

  <div class="container my-5">
    <div class="card shadow-sm">
      <div class="card-body">

        <?php $role = $role ?? session()->get('role'); ?>

        <!-- Welcome -->
        <h3 class="mb-3"><i class="bi bi-person-circle me-2"></i>Welcome, <?= esc(session()->get('name') ?? 'User') ?>!</h3>
        <p class="text-muted mb-4">Role: <strong><?= esc($role ?? 'User') ?></strong></p>
        <hr>

        <!--  ADMIN DASHBOARD  -->
        <?php if ($role === 'admin'): ?>
          
          <h4 class="mb-3">Admin Dashboard</h4>

          <p>Total Users: <strong><?= isset($data['usersCount']) ? (int)$data['usersCount'] : 0 ?></strong></p>

          <?php if (!empty($data['recentUsers'])): ?>
            <div class="table-responsive mt-3">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-primary">
                  <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                  </tr>
                </thead>
                <tbody>
                  
                  <!-- USER MANAGEMENT SECTION -->
<hr>
<h4 class="mb-3">User Management</h4>

<!-- Add User Button -->
<div class="text-center my-4">
  <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addUserModal">
    <i class="bi bi-person-plus me-1"></i> Add New User
  </button>
</div>

<!-- Add User Modal -->
                    <small class="text-muted">Time: <?= esc($course['class_time'] ?? 'TBA') ?></small>
                    <small class="text-muted">SY: <?= esc($course['school_year'] ?? 'Set school year') ?></small>
  <div class="modal-dialog">
    <div class="modal-content">
      <form action="<?= base_url('admin/user/add') ?>" method="post">
        <?= csrf_field() ?>

        <div class="modal-header">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="name" class="form-label">Full Name</label>
            <input type="text" class="form-control" id="name" name="name" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>

          <div class="mb-3">
            <label for="role" class="form-label">Role</label>
            <select class="form-select" id="role" name="role" required>
              <option value="student">Student</option>
              <option value="teacher">Teacher</option>
            </select>
          </div>

          <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
        </div>

        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-dark">Save User</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- User List -->
<?php if (!empty($data['users'])): ?>
  <div class="table-responsive mt-3">
    <table class="table table-striped table-bordered align-middle">
      <thead class="table-primary">
        <tr>
          <th>ID</th>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($data['users'] as $user): ?>
          <tr>
            <td><?= (int)$user['id'] ?></td>
            <td><?= esc($user['name']) ?></td>
            <td><?= esc($user['email']) ?></td>
            <td><?= esc(ucfirst($user['role'])) ?></td>
            <td>
              <!-- Edit Button -->
              <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $user['id'] ?>">
                <i class="bi bi-pencil"></i> Edit
              </button>

              <!-- Delete Button -->
              <a href="<?= base_url('admin/user/delete/' . $user['id']) ?>" 
                class="btn btn-sm btn-danger" 
                onclick="return confirm('Are you sure you want to delete this user?')">
                <i class="bi bi-trash"></i> Delete
              </a>
            </td>
          </tr>

          <!-- Edit User Modal -->
          <div class="modal fade" id="editUserModal<?= $user['id'] ?>" tabindex="-1" aria-labelledby="editUserModalLabel<?= $user['id'] ?>" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="<?= base_url('admin/user/edit/' . $user['id']) ?>" method="post">
                  <?= csrf_field() ?>

                  <div class="modal-header">
                    <h5 class="modal-title" id="editUserModalLabel<?= $user['id'] ?>">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="name<?= $user['id'] ?>" class="form-label">Full Name</label>
                      <input type="text" class="form-control" id="name<?= $user['id'] ?>" name="name" value="<?= esc($user['name']) ?>" required>
                    </div>

                    <div class="mb-3">
                      <label for="email<?= $user['id'] ?>" class="form-label">Email</label>
                      <input type="email" class="form-control" id="email<?= $user['id'] ?>" name="email" value="<?= esc($user['email']) ?>" required>
                    </div>

                    <div class="mb-3">
                      <label for="role<?= $user['id'] ?>" class="form-label">Role</label>
                      <select class="form-select" id="role<?= $user['id'] ?>" name="role" required>
                        <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                        <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                      </select>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Changes</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
<?php endif; ?>

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
            <p class="text-muted">No users found.</p>
          <?php endif; ?>

          <hr>
          <h4 class="card-title mb-3">Available Courses</h4>

          <?php if (!empty($data['courses'])): ?>
            <div class="list-group">
              <?php foreach ($data['courses'] as $course): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-1"><?= esc($course['title']); ?></h5>
                    <p class="text-muted mb-1"><?= esc($course['description']); ?></p>
                    <small class="text-dark">Time: <?= esc($course['class_time'] ?? 'Set time') ?></small>
                    <small class="text-dark">SY: <?= esc($course['school_year'] ?? 'Set school year') ?></small>
                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload'); ?>" 
                      class="btn btn-dark btn-sm rounded-pill">
                      Add Material
                    </a>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted text-center mt-3">No courses found.</p>
          <?php endif; ?>

        <!--  TEACHER DASHBOARD  -->
        <?php elseif ($role === 'teacher'): ?>
          <h4 class="mb-3"><i class="bi bi-journal-text me-2"></i>Teacher Dashboard</h4>

          <div class="d-flex align-items-center justify-content-between mb-2">
            <h5 class="mb-0">My Students</h5>
            <span class="badge bg-dark">Pending: <?= count($data['pendingEnrollments'] ?? []) ?></span>
          </div>

          <?php if (!empty($data['enrollments'])): ?>
            <div class="table-responsive mb-4">
              <table class="table table-striped table-bordered align-middle">
                <thead class="table-dark">
                  <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Status</th>
                    <th class="text-center">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php foreach ($data['enrollments'] as $enrollment): ?>
                    <?php
                      $status = strtolower($enrollment['status'] ?? 'pending');
                      $badgeClass = $status === 'accepted' ? 'success' : ($status === 'declined' ? 'danger' : 'warning');
                    ?>
                    <tr>
                      <td>
                        <div class="fw-semibold"><?= esc($enrollment['student_name'] ?? 'Student') ?></div>
                        <div class="text-muted small"><?= esc($enrollment['student_email'] ?? '') ?></div>
                      </td>
                      <td>
                        <div class="fw-semibold"><?= esc($enrollment['course_title'] ?? 'Course') ?></div>
                        <div class="text-dark small">Time: <?= esc($enrollment['class_time'] ?? 'N/A') ?></div>
                        <div class="text-dark small">SY: <?= esc($enrollment['school_year'] ?? 'N/A') ?></div>
                      </td>
                      <td><span class="badge bg-<?= $badgeClass ?> text-uppercase"><?= esc($status) ?></span></td>
                      <td class="text-center">
                        <?php if ($status === 'pending'): ?>
                          <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-sm btn-success">Accept</button>
                          </form>
                          <form action="<?= base_url('teacher/enrollments/' . $enrollment['id'] . '/status') ?>" method="post" class="d-inline">
                            <?= csrf_field() ?>
                            <input type="hidden" name="status" value="declined">
                            <button type="submit" class="btn btn-sm btn-outline-danger">Decline</button>
                          </form>
                        <?php endif; ?>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php else: ?>
            <p class="text-muted">No enrollment requests yet.</p>
          <?php endif; ?>

          <hr class="my-4">
          <h4 class="card-title mb-3">My Courses</h4>

          <?php if (!empty($data['courses'])): ?>
            <div class="list-group mb-3">
              <?php foreach ($data['courses'] as $course): ?>
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div>
                    <h5 class="mb-1"><?= esc($course['title']); ?></h5>
                    <p class="text-muted mb-1"><?= esc($course['description']); ?></p>
                    <small class="text-dark d-block">Teacher: <?= esc(session()->get('name')) ?></small>
                    <small class="text-dark d-block">Time: <?= esc($course['class_time'] ?? 'TBA') ?></small>
                    <small class="text-dark d-block">SY: <?= esc($course['school_year'] ?? 'Set school year') ?></small>
                  </div>
                  <div class="d-flex gap-2 align-items-center">
                    <a href="<?= base_url('admin/course/' . $course['id'] . '/upload'); ?>" 
                      class="btn btn-dark btn-sm rounded-pill">
                      Add Material
                    </a>
                    <button class="btn btn-sm btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editCourseModal<?= $course['id'] ?>">Edit</button>
                  </div>
                </div>

                <div class="modal fade" id="editCourseModal<?= $course['id'] ?>" tabindex="-1" aria-labelledby="editCourseModalLabel<?= $course['id'] ?>" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <form action="<?= base_url('teacher/course/update/' . $course['id']) ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="modal-header">
                          <h5 class="modal-title" id="editCourseModalLabel<?= $course['id'] ?>">Edit Course</h5>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                          <div class="mb-3">
                            <label for="title<?= $course['id'] ?>" class="form-label">Course Title</label>
                            <input type="text" class="form-control" id="title<?= $course['id'] ?>" name="title" value="<?= esc($course['title']) ?>" required>
                          </div>

                          <div class="mb-3">
                            <label for="description<?= $course['id'] ?>" class="form-label">Course Description</label>
                            <textarea class="form-control" id="description<?= $course['id'] ?>" name="description" rows="3" required><?= esc($course['description']) ?></textarea>
                          </div>

                          <div class="mb-3">
                            <label for="school_year<?= $course['id'] ?>" class="form-label">School Year</label>
                            <input type="text" class="form-control" id="school_year<?= $course['id'] ?>" name="school_year" value="<?= esc($course['school_year'] ?? '2024-2025') ?>" placeholder="e.g., 2024-2025" required>
                          </div>
                              <div class="mb-3">
                                <label for="class_time<?= $course['id'] ?>" class="form-label">Time</label>
                                <input type="text" class="form-control" id="class_time<?= $course['id'] ?>" name="class_time" value="<?= esc($course['class_time'] ?? '') ?>" placeholder="e.g., MWF 9:00-10:00 AM">
                              </div>
                        </div>

                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="submit" class="btn btn-dark">Save Changes</button>
                        </div>
                      </form>
                    </div>
                  </div>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted text-center mt-3">No courses assigned yet.</p>
          <?php endif; ?>

          <div class="text-center my-4">
            <button type="button" class="btn btn-dark" data-bs-toggle="modal" data-bs-target="#addCourseModal">
              <i class="bi bi-plus-circle me-1"></i> Add New Course
            </button>
          </div>

          <div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <form action="<?= base_url('teacher/course/add') ?>" method="post">
                  <?= csrf_field() ?>

                  <div class="modal-header">
                    <h5 class="modal-title" id="addCourseModalLabel">Add New Course</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>

                  <div class="modal-body">
                    <div class="mb-3">
                      <label for="title" class="form-label">Course Title</label>
                      <input type="text" class="form-control" id="title" name="title" required>
                    </div>

                    <div class="mb-3">
                      <label for="description" class="form-label">Course Description</label>
                      <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                    </div>

                    <div class="mb-3">
                      <label for="class_time" class="form-label">Time</label>
                      <input type="text" class="form-control" id="class_time" name="class_time" placeholder="e.g., MWF 9:00-10:00 AM">
                    </div>

                    <div class="mb-3">
                      <label for="school_year" class="form-label">School Year</label>
                      <input type="text" class="form-control" id="school_year" name="school_year" placeholder="e.g., 2024-2025" required>
                    </div>
                  </div>

                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark">Save Course</button>
                  </div>
                </form>
              </div>
            </div>
          </div>


          <?php if (!empty($data['materials'])): ?>
            <div class="mt-4">
              <h5><i class="bi bi-folder2-open me-2"></i>Uploaded Materials</h5>
              <table class="table table-bordered table-striped mt-3">
                <thead class="table-secondary">
                  <tr><th>#</th><th>File Name</th><th>Action</th></tr></thead>
                <tbody>
                  <?php foreach ($data['materials'] as $index => $mat): ?>
                    <tr>
                      <td><?= $index + 1 ?></td>
                      <td><?= esc($mat['file_name']) ?></td>
                      <td>
                        <a href="<?= site_url('materials/delete/' . $mat['id']) ?>" 
                          class="btn btn-danger btn-sm" 
                          onclick="return confirm('Delete this file?')">
                          <i class="bi bi-trash"></i> Delete
                        </a>
                      </td>
                    </tr>
                  <?php endforeach; ?>
                </tbody>
              </table>
            </div>
          <?php endif; ?>

        <!--  STUDENT DASHBOARD  -->
        <?php else: ?>
          <h4 class="mb-3">Student Dashboard</h4>

          <!-- Student Profile -->
          <h5>My Profile</h5>
          <?php if (!empty($data['profile'])): ?>
            <div class="row g-3 mb-4 mt-2">
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Name: <strong><?= esc($data['profile']['name']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Email: <strong><?= esc($data['profile']['email']) ?></strong></div>
              </div>
              <div class="col-md-6">
                <div class="p-3 border rounded bg-white">Course: <strong><?= esc($data['profile']['course_name'] ?? 'N/A') ?></strong></div>
              </div>
            </div>
          <?php endif; ?>

          <hr>

<!-- Lab 9: Search and Filtering put here-->

<!-- LAB 9: SEARCH BAR -->
<div class="row mb-4">
    <div class="col-md-6">
        <form id="searchForm" class="d-flex">
            <div class="input-group">
                <input type="text" id="searchInput" class="form-control"
                    placeholder="Search courses..." name="search_term">
                <button class="btn btn-outline-dark" type="submit">
                    <i class="bi bi-search"></i> Search
                </button>
            </div>
        </form>
    </div>
</div>

<!-- LAB 9: SEARCH RESULTS CONTAINER -->
<div id="coursesContainer" class="row">
    <?php if (!empty($data['courses'])): ?>
        <?php foreach ($data['courses'] as $course): ?>
            <div class="col-md-4 mb-4">
                <div class="card course-card">
                    <div class="card-body">
                        <h5 class="card-title"><?= esc($course['title']) ?></h5>
                        <p class="card-text"><?= esc($course['description']) ?></p>
                        <a href="#" class="btn btn-dark">View Course</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No courses available.</p>
    <?php endif; ?>
</div>

<?php if (!empty($data['pendingEnrollments'])): ?>
  <div class="mt-3">
    <h6>Pending Enrollment Requests</h6>
    <div class="list-group">
      <?php foreach ($data['pendingEnrollments'] as $pending): ?>
        <div class="list-group-item d-flex justify-content-between align-items-center">
          <div>
            <strong><?= esc($pending['title'] ?? 'Course') ?></strong>
            <div class="text-muted small">SY: <?= esc($pending['school_year'] ?? 'N/A') ?></div>
          </div>
          <span class="badge bg-warning text-dark">Pending</span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
<?php endif; ?>

          <hr>

          <!-- Course Materials -->
          <h5><i class="bi bi-file-earmark-arrow-down me-2"></i>Course Materials</h5>

          <?php if (!empty($data['materials']) && !empty($data['courses'])): ?>
            <div class="accordion mt-3" id="materialsAccordion">
              <?php foreach ($data['courses'] as $course): ?>
                <?php
                  $courseMaterials = array_filter($data['materials'], function($mat) use ($course) {
                    return $mat['course_id'] == $course['id'];
                  });
                ?>

                <?php if (!empty($courseMaterials)): ?>
                  <div class="accordion-item">
                    <h2 class="accordion-header" id="heading<?= $course['id'] ?>">
                      <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $course['id'] ?>" aria-expanded="false" aria-controls="collapse<?= $course['id'] ?>">
                        <?= esc($course['title']); ?>
                      </button>
                    </h2>
                    <div id="collapse<?= $course['id'] ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $course['id'] ?>" data-bs-parent="#materialsAccordion">
                      <div class="accordion-body">
                        <table class="table table-bordered table-hover bg-white mt-2">
                          <thead class="table-primary">
                            <tr><th>#</th><th>File Name</th><th>Action</th></tr>
                          </thead>
                          <tbody>
                            <?php $index = 1; foreach ($courseMaterials as $mat): ?>
                              <tr>
                                <td><?= $index++ ?></td>
                                <td><?= esc($mat['file_name']) ?></td>
                                <td>
                                  <a href="<?= site_url('materials/download/' . $mat['id']) ?>" 
                                    class="btn btn-dark btn-sm">
                                    <i class="bi bi-download"></i> Download
                                  </a>
                                </td>
                              </tr>
                            <?php endforeach; ?>
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>
                <?php endif; ?>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-muted mt-3">No materials available yet.</p>
          <?php endif; ?>
        <?php endif; ?>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <!-- LAB 9: SEARCH & FILTERING SCRIPT -->
<script>
$(document).ready(function () {

    // Client-side filtering
    $("#searchInput").on('keyup', function () {
        var value = $(this).val().toLowerCase();
        $('.course-card').filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });

    // Server-side search with AJAX
    $("#searchForm").on('submit', function(e) {
        e.preventDefault();
        var searchTerm = $("#searchInput").val();

        $.get('<?= base_url("courses/search") ?>', { search_term: searchTerm }, function(data) {
          $("#coursesContainer").empty();

            if (data.length > 0) {
                $.each(data, function(index, course) {
                    var courseHtml = `
                        <div class="col-md-4 mb-4">
                            <div class="card course-card">
                                <div class="card-body">
                                    <h5 class="card-title">${course.title}</h5>
                                    <p class="card-text">${course.description}</p>
                                    <a href="#" class="btn btn-dark">View Course</a>
                                </div>
                            </div>
                        </div>
                    `;
                    $("#coursesContainer").append(courseHtml);
                });
            } else {
                $("#coursesContainer").html(
                    '<div class="col-12"><div class="alert alert-info">No courses found matching your search.</div></div>'
                );
            }
        });
    });
});
</script>

</body>
</html>
