<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

// Fetch all advertisements hfeuygeuyygds
$query = "SELECT * FROM advertisements ORDER BY created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Advertisements</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <style>
    .ad-image {
      width: 80px;
      height: auto;
    }
    .badge-inactive {
      background-color: #6c757d;
    }
  </style>
</head>
<body>

<!-- Sidebar and Header included properly -->
<?php include('../admin/admin_sidebar.php'); ?>
<?php include('../admin/admin_header.php'); ?>

<div class="main-content" style="margin-left: 250px; margin-top: 70px; padding: 30px;">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h4>All Advertisements</h4>
    <a href="add_advertisement.php" class="btn btn-primary">+ Add New</a>
  </div>

  <!-- Optional: Debug row count -->
  <?php if (mysqli_num_rows($result) === 0): ?>
    <div class="alert alert-info">No advertisements found.</div>
  <?php endif; ?>

  <table class="table table-striped table-bordered align-middle">
    <thead class="table-primary">
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Type</th>
        <th>Size</th>
        <th>URL</th>
        <th>Status</th>
        <th>Start - End</th>
        <th>Impr.</th>
        <th>Clicks</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($ad = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <td><img src="../uploads/<?= htmlspecialchars($ad['image_path']) ?>" class="ad-image" alt="Ad"></td>
          <td><?= htmlspecialchars($ad['name']) ?></td>
          <td><span class="badge bg-info text-dark"><?= htmlspecialchars($ad['ad_type']) ?></span></td>
          <td><?= $ad['width'] ?>×<?= $ad['height'] ?> px</td>
          <td><a href="<?= htmlspecialchars($ad['redirect_url']) ?>" target="_blank">Visit</a></td>
          <td>
            <span class="badge <?= $ad['is_active'] ? 'bg-success' : 'badge-inactive' ?>">
              <?= $ad['is_active'] ? 'Enabled' : 'Disabled' ?>
            </span>
          </td>
          <td><?= $ad['start_date'] ?> → <?= $ad['end_date'] ?></td>
          <td><?= $ad['impressions'] ?></td>
          <td><?= $ad['clicks'] ?></td>
          <td>
            <div class="dropdown">
              <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                More
              </button>
              <ul class="dropdown-menu">
                <li>
                  <a class="dropdown-item" href="edit_advertisement.php?id=<?= $ad['id'] ?>">
                    <i class="fas fa-pen"></i> Edit
                  </a>
                </li>
                <li>
                  <a class="dropdown-item text-danger" href="delete_advertisement.php?id=<?= $ad['id'] ?>" onclick="return confirm('Delete this ad?')">
                    <i class="fas fa-trash"></i> Delete
                  </a>
                </li>
                <li>
                  <a class="dropdown-item" href="toggle_advertisement.php?id=<?= $ad['id'] ?>&status=<?= $ad['is_active'] ? '0' : '1' ?>">
                    <i class="fas fa-eye<?= $ad['is_active'] ? '-slash' : '' ?>"></i>
                    <?= $ad['is_active'] ? 'Disable' : 'Enable' ?>
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
