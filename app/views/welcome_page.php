<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background: #f9fafb;
        }

        h1 {
            margin-bottom: 1rem;
        }

        .actions {
            margin-bottom: 1rem;
        }

        .actions form {
            display: inline-block;
            margin-right: 1rem;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
            background: #fff;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 10px;
            text-align: left;
        }

        th {
            background: #f3f4f6;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 0.5rem;
            list-style: none;
            padding: 0;
            margin: 1.5rem 0;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination li a,
        .pagination li strong {
            display: block;
            padding: 6px 12px;
            border: 1px solid #3b82f6;
            border-radius: 4px;
            text-decoration: none;
            color: #1e293b;
            background: #f8fafc;
            font-size: 14px;
            transition: all 0.2s;
        }

        .pagination li a:hover {
            background: #3b82f6;
            color: #fff;
        }

        .pagination li.active strong {
            background: #3b82f6;
            color: #fff;
        }
    </style>
</head>
<body>
    <h1>Students</h1>

    <div class="actions">
        <a href="<?= site_url('students/create'); ?>" class="btn btn-primary">➕ Add Student</a>

        <!-- Search Form -->
        <form method="get" action="<?= site_url('students/index'); ?>">
            <input type="text" name="q" placeholder="Search..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
            <button type="submit" class="btn btn-warning">Search</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Email</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($students)): ?>
                <?php foreach ($students as $s): ?>
                    <tr>
                        <td><?= $s['id']; ?></td>
                        <td><?= htmlspecialchars($s['first_name']); ?></td>
                        <td><?= htmlspecialchars($s['last_name']); ?></td>
                        <td><?= htmlspecialchars($s['email']); ?></td>
                        <td>
                            <a href="<?= site_url('students/update/' . $s['id']); ?>" class="btn btn-warning">✏ Edit</a>
                            <a href="<?= site_url('students/delete/' . $s['id']); ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">🗑 Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5">No students found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <?php if (!empty($page)): ?>
        <nav class="pagination-container">
            <?= $page; ?>
        </nav>
    <?php endif; ?>
</body>
</html>
