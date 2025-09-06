<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

  <div class="col-md-4 col-lg-4"> 
    <div class="card shadow-sm border-0">
      <div class="card-body p-4">
        <h3 class="text-center mb-4 fw-bold text-dark">Register</h3>

        <?php if(session()->getFlashdata('register_success')): ?>
          <div class="alert alert-success text-center">
            <?= session()->getFlashdata('register_success') ?>
          </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('register_error')): ?>
          <div class="alert alert-danger text-center">
            <?= session()->getFlashdata('register_error') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('register') ?>" method="post">
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control form-control-sm" placeholder="Enter username" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-sm" placeholder="Enter email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-sm" placeholder="Enter password" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="password_confirm" class="form-control form-control-sm" placeholder="Confirm password" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select form-select-sm" required>
              <option value="" disabled selected>Select Role</option>
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-dark btn-sm">Register</button>
          </div>
        </form>

        <p class="mt-3 text-center">
          <a href="<?= base_url('login') ?>" class="text-dark">Already have an account? <span class="fw-bold">Login</span></a>
        </p>
      </div>
    </div>
  </div>

</body>
</html>
