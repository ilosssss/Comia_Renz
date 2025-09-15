<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Students List</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5">
    <div class="card shadow-sm">
      <div class="card-header d-flex justify-content-between align-items-center bg-primary text-white">
        <h3 class="mb-0">Students List</h3>
        <a href="<?= site_url('students/create'); ?>" class="btn btn-success btn-sm">
          ➕ Create Record
        </a>
      </div>
      <div class="card-body">

        <!-- ✅ Search Form -->
        <form method="get" action="<?= site_url(); ?>" class="row g-2 mb-3">
          <div class="col-md-8">
            <input type="text" 
                   name="q" 
                   value="<?= isset($_GET['q']) ? html_escape($_GET['q']) : ''; ?>" 
                   class="form-control" 
                   placeholder="🔍 Search by name or email...">
          </div>
          <div class="col-md-2 d-grid">
            <button type="submit" class="btn btn-primary">Search</button>
          </div>
          <?php if (isset($_GET['q']) && $_GET['q'] != ''): ?>
            <div class="col-md-2 d-grid">
              <a href="<?= site_url(); ?>" class="btn btn-secondary">Show All</a>
            </div>
          <?php endif; ?>
        </form>

        <table class="table table-striped table-hover align-middle text-center">
          <thead class="table-primary">
            <tr>
              <th scope="col">ID</th>
              <th scope="col">First Name</th>
              <th scope="col">Last Name</th>
              <th scope="col">Email</th>
              <th scope="col">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php if (!empty($students)): ?>
              <?php foreach(html_escape($students) as $student): ?>
                <tr>
                  <td><?= $student['id']; ?></td>
                  <td><?= $student['first_name']; ?></td>
                  <td><?= $student['last_name']; ?></td>
                  <td><?= $student['email']; ?></td>
                  <td>
                    <a href="<?= site_url('students/update/'.$student['id']); ?>" 
                       class="btn btn-sm btn-warning me-1">
                      ✏️ Update
                    </a>
                    <a href="<?= site_url('students/delete/'.$student['id']); ?>" 
                       class="btn btn-sm btn-danger"
                       onclick="return confirm('Are you sure you want to delete this record?');">
                      🗑️ Delete
                    </a>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="text-muted">No students found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>

        <!-- ✅ Pagination -->
        <?php if (!empty($page)): ?>
          <div class="d-flex justify-content-center mt-3">
            <?= $page; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
