<?php
session_start();
include('../includes/db_connection.php');
include('../admin/admin_header.php');
include('../admin/admin_sidebar.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($name && $slug && $description) {
        $insert = "INSERT INTO categories (name, slug, description, is_active) 
                   VALUES ('$name', '$slug', '$description', $is_active)";
        if (mysqli_query($conn, $insert)) {
            $success = "Category added successfully!";
        } else {
            $error = "Failed to add category: " . mysqli_error($conn);
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Add Category</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<div class="main-content" style="margin-left: 250px; margin-top: 70px; padding: 30px;">
  <h4 class="mb-4">Add New Category</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="name" class="form-label">Category Name</label>
      <input type="text" name="name" class="form-control" required>
    </div>

    <div class="col-md-6">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" name="slug" class="form-control" required>
    </div>

    <div class="col-md-12">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3" required></textarea>
    </div>

    <div class="col-md-3">
      <label for="is_active" class="form-label">Status</label><br>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" checked>
        <label class="form-check-label" for="is_active">Enabled</label>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-primary">Add Category</button>
      <a href="category.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
</div>

</body>
</html>
