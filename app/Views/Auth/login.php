<?php include('app\Views\template.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center justify-content-center vh-100">

<div class="card shadow p-4 border border-dark" style="max-width: 400px; width: 100%; background-color: #e9ecef;"> <!-- Gray card with black border -->
    <div class="card-body">
        <h3 class="card-title text-center mb-4" style="color: #000000ff;">Sign In</h3>

        <?php if(session()->getFlashdata('success')): ?>
            <div class="alert alert-success border-0">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif; ?>

        <?php if(session()->getFlashdata('error')): ?>
            <div class="alert alert-danger border-0">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif; ?>

        <?php if(isset($validation)): ?>
            <div class="alert alert-danger border-0">
                <?= $validation->listErrors() ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?= site_url('login') ?>">
            <?= csrf_field() ?>

            <div class="mb-3">
                <label class="form-label" style="color: #000000ff;">Email</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" value="<?= set_value('email') ?>" required>
            </div>

            <div class="mb-4">
                <label class="form-label" style="color: #000000ff;">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>

          

            <button type="submit" class="btn btn-secondary w-100 mb-3">Login</button>
        </form>

        <div class="text-center">
            <small style="color: #000000ff;">Don't have an account? <a href="<?= site_url('register') ?>" class="text-decoration-underline">Register here</a></small>
        </div>
    </div>
</div>

</body>
</html>
