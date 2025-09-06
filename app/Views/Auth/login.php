<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

  <div class="col-md-5 col-lg-4">
    <div class="card shadow-sm border-0">
      <div class="card-body p-5">
        <h3 class="text-center mb-4 fw-bold text-dark">Login</h3>

        <?php if(session()->getFlashdata('login_error')): ?>
          <div class="alert alert-danger text-center">
            <?= session()->getFlashdata('login_error') ?>
          </div>
        <?php endif; ?>

        <form action="<?= base_url('login') ?>" method="post">
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control form-control-lg" placeholder="Enter your email" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control form-control-lg" placeholder="Enter your password" required>
          </div>

          <div class="mb-4">
            <label class="form-label">Role</label>
            <select name="role" class="form-select form-select-lg" required>
              <option value="" disabled selected>Select Role</option>
              <option value="student">Student</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-dark btn-lg">Login</button>
          </div>
        </form>

        <p class="mt-4 text-center">
          <a href="<?= base_url('register') ?>" class="text-dark">Don’t have an account? <span class="fw-bold">Register</span></a>
        </p>
      </div>
    </div>
  </div>

</body>
</html>
