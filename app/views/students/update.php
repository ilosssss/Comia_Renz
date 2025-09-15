<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update Student</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header bg-warning text-dark text-center">
        <h3 class="mb-0">Update Student</h3>
      </div>
      <div class="card-body">
        <form method="post" action="<?= site_url('students/update/'.$student['id']) ?>">
          <div class="mb-3">
            <label for="first_name" class="form-label">First Name</label>
            <input type="text" name="first_name" id="first_name" 
                   class="form-control" 
                   value="<?= html_escape($student['first_name']); ?>" required>
          </div>

          <div class="mb-3">
            <label for="last_name" class="form-label">Last Name</label>
            <input type="text" name="last_name" id="last_name" 
                   class="form-control" 
                   value="<?= html_escape($student['last_name']); ?>" required>
          </div>

          <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" name="email" id="email" 
                   class="form-control" 
                   value="<?= html_escape($student['email']); ?>" required>
          </div>

          <div class="d-grid">
            <button type="submit" class="btn btn-warning text-dark">✏️ Update</button>
          </div>
        </form>


      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
