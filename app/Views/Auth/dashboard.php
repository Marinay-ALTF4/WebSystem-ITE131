<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">


  <nav class="navbar navbar-dark bg-dark">
    <div class="container-fluid">
      <span class="navbar-brand mb-0 h5">
         Welcome Choy!  <strong><?= esc(session()->get('username')) ?></strong>
      </span>
      <a href="<?= base_url('logout') ?>" class="btn btn-outline-light btn-sm">Logout</a>
    </div>
  </nav>

 
  <div class="container mt-5">
    <div class="card shadow-sm border-0">
      <div class="card-body text-center p-5">
        <h4 class="fw-bold text-dark">Dashboard</h4>
        <p class="text-muted">I MISS YOU.</p>
      </div>
    </div>
  </div>

</body>
</html>
