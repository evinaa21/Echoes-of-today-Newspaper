<?php
// ─── bootstrap / includes ───────────────────────────────────────────
session_start();
include('../includes/db_connection.php');
include('../admin/admin_sidebar.php');
include('../admin/admin_header.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Categories</title>

  <!-- styles & libs -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .main-content{
      margin-left:255px;
      margin-top:60px;
      padding:30px;
      background:#f8f9fa;
      min-height:100vh;
    }
  </style>
</head>
<body>
  <div class="main-content">

    <?php if (isset($_SESSION['success'])): ?>
      <div class="alert alert-success">
        <?= $_SESSION['success']; unset($_SESSION['success']); ?>
      </div>
    <?php endif; ?>

    <!-- header row -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h4>All Categories</h4>
      <a href="add_category.php" class="btn btn-primary">+ Add Category</a>
    </div>

    <?php
    $result = mysqli_query($conn, "SELECT * FROM categories ORDER BY display_order ASC");
    ?>

    <table class="table table-bordered table-hover">
      <thead class="table-primary">
        <tr>
          <th>Name</th>
          <th>Description</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <tr>
          <td><?= htmlspecialchars($row['name']) ?></td>
          <td><?= htmlspecialchars($row['description']) ?></td>
          <td>
            <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
              <?= $row['is_active'] ? 'Enabled' : 'Disabled' ?>
            </span>
          </td>
          <td>
            <div class="dropdown">
              <button class="btn btn-outline-primary btn-sm dropdown-toggle"
                      type="button" data-bs-toggle="dropdown">
                <i class="fas fa-ellipsis-v"></i> More
              </button>
              <ul class="dropdown-menu">
                <!-- edit -->
                <li>
                  <a class="dropdown-item"
                     href="edit_category.php?id=<?= $row['id'] ?>">
                     <i class="fas fa-pen"></i> Edit
                  </a>
                </li>

                <!-- enable / disable -->
                <li>
                  <a class="dropdown-item"
                     href="category_status.php?id=<?= $row['id'] ?>&status=<?= $row['is_active'] ? '0' : '1' ?>">
                     <i class="fas fa-eye<?= $row['is_active'] ? '-slash' : '' ?>"></i>
                     <?= $row['is_active'] ? 'Disable' : 'Enable' ?>
                  </a>
                </li>
              </ul>
            </div>
          </td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
