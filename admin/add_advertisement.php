<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

$success = $error = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $ad_type = mysqli_real_escape_string($conn, $_POST['ad_type']);
    $redirect_url = mysqli_real_escape_string($conn, $_POST['redirect_url']);
    $width = (int)$_POST['width'];
    $height = (int)$_POST['height'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // File upload
    $targetDir = "../ads/";
    $fileName = time() . "_" . basename($_FILES["image"]["name"]);
    $targetPath = $targetDir . $fileName;
    $dbPath = "ads/" . $fileName;
    $fileType = strtolower(pathinfo($targetPath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($fileType, $allowedTypes)) {
        $error = "Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.";
    } else {
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        if (move_uploaded_file($_FILES["image"]["tmp_name"], $targetPath)) {
            $query = "INSERT INTO advertisements 
                (name, ad_type, image_path, redirect_url, width, height, is_active, start_date, end_date, impressions, clicks)
                VALUES 
                ('$name', '$ad_type', '$dbPath', '$redirect_url', $width, $height, 1, '$start_date', '$end_date', 0, 0)";

            if (mysqli_query($conn, $query)) {
                $_SESSION['success'] = "Advertisement added successfully.";
                header('Location: advertisement.php');
                exit;
            } else {
                $error = "Failed to save advertisement.";
            }
        } else {
            $error = "Image upload failed.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Advertisement</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <style>
    .preview-image {
      width: 100%;
      max-width: 400px;
      height: auto;
      border: 1px dashed #ccc;
      padding: 10px;
    }
  </style>
</head>
<body>

<?php include('../admin/admin_header.php'); ?>
<?php include('../admin/admin_sidebar.php'); ?>

<div class="main-content" style="margin-left: 250px; margin-top: 70px; padding: 30px;">
  <h4 class="mb-4">Add New Advertisement</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" enctype="multipart/form-data" class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Ad Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label class="form-label">Ad Type</label>
      <select name="ad_type" class="form-select" required>
        <option value="banner">Banner</option>
        <option value="sidebar">Sidebar</option>
        <option value="popup">Popup</option>
      </select>
    </div>

    <div class="col-md-12">
      <label class="form-label">Upload Image</label>
      <input type="file" name="image" class="form-control" accept=".png,.jpg,.jpeg,.gif" required>
      <div class="form-text">Supported formats: PNG, JPG, JPEG, GIF</div>
    </div>

    <div class="col-md-12">
      <label class="form-label">Redirect URL</label>
      <input type="url" name="redirect_url" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Width (px)</label>
      <input type="number" name="width" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Height (px)</label>
      <input type="number" name="height" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">Start Date</label>
      <input type="date" name="start_date" class="form-control" required>
    </div>

    <div class="col-md-3">
      <label class="form-label">End Date</label>
      <input type="date" name="end_date" class="form-control" required>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">Save Advertisement</button>
      <a href="advertisement.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
