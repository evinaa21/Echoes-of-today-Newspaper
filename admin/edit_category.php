<?php
session_start();
include('../includes/db_connection.php');
include('../admin/admin_header.php');
include('../admin/admin_sidebar.php');

// // Check if ID is provided
// if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
//     header('Location: category.php');
//     exit;
// }

// $id = (int)$_GET['id'];
// $error = $success = '';

// // Fetch category
// $query = "SELECT * FROM categories WHERE id = $id";
// $result = mysqli_query($conn, $query);
// $category = mysqli_fetch_assoc($result);

// if (!$category) {
//     $error = "Category not found.";
// }

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    if ($name && $slug && $description) {
        $update = "UPDATE categories 
                   SET name = '$name', slug = '$slug', description = '$description', is_active = $is_active 
                   WHERE id = $id";
        if (mysqli_query($conn, $update)) {
            header("Location: category.php");
            exit;
        } else {
            $error = "Failed to update category: " . mysqli_error($conn);
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Category</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
</head>
<body>

<div class="main-content" style="margin-left: 250px; margin-top: 70px; padding: 30px;">
  <h4 class="mb-4">Edit Category</h4>

  <?php if ($error): ?>
    <div class="alert alert-danger"><?= $error ?></div>
  <?php endif; ?>

  <?php if ($category): ?>
  <form method="POST" class="row g-3">
    <div class="col-md-6">
      <label for="name" class="form-label">Category Name</label>
      <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>

    <div class="col-md-6">
      <label for="slug" class="form-label">Slug</label>
      <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($category['slug']) ?>" required>
    </div>

    <div class="col-md-12">
      <label for="description" class="form-label">Description</label>
      <textarea name="description" class="form-control" rows="3" required><?= htmlspecialchars($category['description']) ?></textarea>
    </div>

    <div class="col-md-3">
      <label for="is_active" class="form-label">Status</label><br>
      <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" <?= $category['is_active'] ? 'checked' : '' ?>>
        <label class="form-check-label" for="is_active"><?= $category['is_active'] ? 'Enabled' : 'Disabled' ?></label>
      </div>
    </div>

    <div class="col-12">
      <button type="submit" class="btn btn-success">Update Category</button>
      <a href="category.php" class="btn btn-secondary">Cancel</a>
    </div>
  </form>
  <?php endif; ?>
</div>

</body>
</html>
