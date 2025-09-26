<?php include('app\Views\template.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="d-flex align-items-center justify-content-center min-vh-100" style="padding-top: 80px; padding-bottom: 40px;">
    <div class="card shadow-sm p-4 border border-dark" style="max-width: 380px; width: 100%; background-color: #e9ecef;">
        <div class="card-body">
            <h4 class="card-title text-center mb-3" style="color: #000000ff;">Sign Up</h4>

            <?php if(session()->getFlashdata('success')): ?>
                <div class="alert alert-success border-0 py-2 px-3">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if(isset($validation)): ?>
                <div class="alert alert-danger border-0 py-2 px-3">
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <form method="post" action="<?= site_url('register') ?>">
                <?= csrf_field() ?>

                <div class="mb-2">
                    <label class="form-label small" style="color: #000000ff;">Name</label>
                    <input type="text" name="name" class="form-control form-control-sm" placeholder="Enter your name" value="<?= set_value('name') ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small" style="color: #000000ff;">Email</label>
                    <input type="email" name="email" class="form-control form-control-sm" placeholder="Enter your email" value="<?= set_value('email') ?>" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small" style="color: #000000ff;">Password</label>
                    <input type="password" name="password" class="form-control form-control-sm" placeholder="Enter your password" required>
                </div>

                <div class="mb-2">
                    <label class="form-label small" style="color: #000000ff;">Confirm Password</label>
                    <input type="password" name="password_confirm" class="form-control form-control-sm" placeholder="Confirm your password" required>
                </div>
                
                <button type="submit" class="btn btn-secondary btn-sm w-100 mb-2">Register</button>
            </form>

            <div class="text-center">
                <small style="color: #000000ff;">Already have an account?
                    <a href="<?= site_url('login') ?>" class="text-decoration-underline">Login here</a>
                </small>
            </div>
        </div>
    </div>
</div>

</body>
</html>
