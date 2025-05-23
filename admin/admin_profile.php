<?php
session_start();
include('../includes/db_connection.php');

// Simulate admin session (for development)
$admin_id = $_SESSION['admin_id'] ?? 1;

// Handle profile update submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $bio = mysqli_real_escape_string($conn, $_POST['bio']);

    $image_sql = "";
    if (!empty($_FILES['profile_image']['name'])) {
        $target_dir = "uploads/";
        $image_name = time() . "_" . basename($_FILES["profile_image"]["name"]);
        $target_file = $target_dir . $image_name;
        move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file);
        $image_sql = ", profile_image = '$target_file'";
    }

    $update_query = "
        UPDATE users 
        SET first_name = '$first_name',
            last_name = '$last_name',
            email = '$email',
            bio = '$bio'
            $image_sql
        WHERE id = $admin_id
    ";
    mysqli_query($conn, $update_query);
    header("Location: admin_profile.php?success=1");
    exit();
}

// Fetch admin data
$query = "SELECT * FROM users WHERE id = $admin_id LIMIT 1";
$result = mysqli_query($conn, $query);
$admin = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<?php include('admin_header.php'); ?>
<?php include('admin_sidebar.php'); ?>

<div class="main-content px-4 pt-4">
  <h4 class="mb-4 fw-bold">Profile</h4>

  <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">Profile updated successfully.</div>
  <?php endif; ?>

  <div class="row g-4">
    <!-- Left Profile Card -->
    <div class="col-md-4">
      <div class="card shadow-sm rounded-4 text-center bg-primary text-white">
        <div class="card-body">
          <img src="<?= htmlspecialchars($admin['profile_image'] ?? 'assets/img/default.png') ?>" class="rounded-circle mb-3" width="100" height="100" style="object-fit: cover;">
          <h5 class="fw-bold"><?= htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']) ?></h5>
          <p class="mb-2">@<?= htmlspecialchars($admin['username']) ?></p>
          <p class="mb-0"><?= htmlspecialchars($admin['email']) ?></p>
        </div>
      </div>
    </div>

    <!-- Right Profile Update Form -->
    <div class="col-md-8">
      <div class="card shadow-sm rounded-4">
        <div class="card-body">
          <form method="POST" action="admin_profile.php" enctype="multipart/form-data">
            <div class="mb-3">
              <label class="form-label">Image</label><br>
              <img src="<?= htmlspecialchars($admin['profile_image'] ?? 'assets/img/default.png') ?>" class="rounded mb-2" width="150"><br>
              <input type="file" name="profile_image" accept=".png,.jpg,.jpeg" class="form-control">
              <small class="text-muted">Supported formats: .png, .jpg. Will be resized to 400x400px.</small>
            </div>

            <div class="mb-3">
              <label class="form-label">First Name *</label>
              <input type="text" name="first_name" class="form-control" value="<?= htmlspecialchars($admin['first_name']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Last Name *</label>
              <input type="text" name="last_name" class="form-control" value="<?= htmlspecialchars($admin['last_name']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($admin['email']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Bio</label>
              <textarea name="bio" rows="4" class="form-control"><?= htmlspecialchars($admin['bio']) ?></textarea>
            </div>

            <button type="submit" class="btn btn-primary w-100">Submit</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
