<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');
include('../admin/admin_header.php');
include('../admin/admin_sidebar.php');

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: advertisement.php');
    exit;
}

$id = (int)$_GET['id'];
$ad = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM advertisements WHERE id = $id"));

if (!$ad) {
    $_SESSION['error'] = "Ad not found.";
    header('Location: advertisement.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $ad_type = mysqli_real_escape_string($conn, $_POST['ad_type']);
    $redirect_url = mysqli_real_escape_string($conn, $_POST['redirect_url']);
    $width = (int)$_POST['width'];
    $height = (int)$_POST['height'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $image_path = $ad['image_path']; // Default to current

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg','jpeg','png','gif'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed)) {
            $newPath = 'uploads/ads/' . time() . '_' . basename($_FILES['image']['name']);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $newPath)) {
                $image_path = $newPath;
            } else {
                $error = "Failed to upload image.";
            }
        } else {
            $error = "Unsupported file format.";
        }
    }

    if (!$error) {
        $update = "
            UPDATE advertisements SET
                name = '$name',
                ad_type = '$ad_type',
                redirect_url = '$redirect_url',
                width = $width,
                height = $height,
                start_date = '$start_date',
                end_date = '$end_date',
                image_path = '$image_path',
                updated_at = NOW()
            WHERE id = $id
        ";

        if (mysqli_query($conn, $update)) {
            $_SESSION['success'] = "Advertisement updated.";
            header('Location: advertisement.php');
            exit;
        } else {
            $error = "Update failed: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit Advertisement</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>
<div class="main-content" style="margin-left: 250px; margin-top: 70px; padding: 30px;">
  <h4>Edit Advertisement</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Ad Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($ad['name']) ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Ad Type</label>
      <select name="ad_type" class="form-select" required>
        <option value="banner" <?= $ad['ad_type']=='banner' ? 'selected':'' ?>>Banner</option>
        <option value="sidebar" <?= $ad['ad_type']=='sidebar' ? 'selected':'' ?>>Sidebar</option>
        <option value="popup" <?= $ad['ad_type']=='popup' ? 'selected':'' ?>>Popup</option>
      </select>
    </div>

    <div class="col-md-12">
      <label class="form-label">Redirect URL</label>
      <input type="url" name="redirect_url" class="form-control" value="<?= htmlspecialchars($ad['redirect_url']) ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Start Date</label>
      <input type="date" name="start_date" class="form-control" value="<?= $ad['start_date'] ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">End Date</label>
      <input type="date" name="end_date" class="form-control" value="<?= $ad['end_date'] ?>" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Width (px)</label>
      <input type="number" name="width" class="form-control" value="<?= $ad['width'] ?>" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Height (px)</label>
      <input type="number" name="height" class="form-control" value="<?= $ad['height'] ?>" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Current Image</label><br>
      <img src="<?= $ad['image_path'] ?>" alt="Ad Image" style="max-width: 200px; border:1px solid #ddd;">
      <div class="mt-2">
        <input type="file" name="image" class="form-control" accept=".jpg,.jpeg,.png,.gif">
        <small class="text-muted">Leave blank to keep existing image.</small>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Update Advertisement</button>
      <a href="advertisement.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>
</body>
</html>
