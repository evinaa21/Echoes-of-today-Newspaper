<?php
session_start();
include('../includes/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $category_id = intval($_POST['category_id']);
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $slug = mysqli_real_escape_string($conn, $_POST['slug']);
    $excerpt = mysqli_real_escape_string($conn, $_POST['excerpt']);
    $content = mysqli_real_escape_string($conn, $_POST['content']);

    $upload_dir = '../uploads/';
    $image_url = null;

    if (!empty($_FILES['featured_image']['name'])) {
        $file_name = basename($_FILES['featured_image']['name']);
        $target_file = $upload_dir . time() . '_' . $file_name;
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $allowed = ['jpg', 'jpeg', 'png'];

        if (in_array($file_type, $allowed)) {
            if (move_uploaded_file($_FILES['featured_image']['tmp_name'], $target_file)) {
                $image_url = $target_file;
            } else {
                echo "Image upload failed.";
                exit();
            }
        } else {
            echo "Unsupported file format.";
            exit();
        }
    }

    if ($image_url) {
        $stmt = $conn->prepare("UPDATE articles SET category_id=?, title=?, slug=?, excerpt=?, content=?, featured_image=? WHERE id=?");
        $stmt->bind_param("isssssi", $category_id, $title, $slug, $excerpt, $content, $image_url, $id);
    } else {
        $stmt = $conn->prepare("UPDATE articles SET category_id=?, title=?, slug=?, excerpt=?, content=? WHERE id=?");
        $stmt->bind_param("issssi", $category_id, $title, $slug, $excerpt, $content, $id);
    }

    if ($stmt->execute()) {
        header("Location: manage_articles.php?success=1");
        exit();
    } else {
        echo "Update failed: " . $stmt->error;
        exit();
    }
}

// Regular GET mode: fetch article to display
$id = $_GET['id'] ?? null;

if (!$id || !is_numeric($id)) {
    header('Location: manage_articles.php');
    exit();
}

$stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    echo "Article not found.";
    exit();
}

$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY display_order ASC");
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Article</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/o_style.css">
  <style>
    .main-content {
      margin-left: 250px;
      margin-top: 60px;
      padding: 30px;
      background-color: #f8f9fa;
      min-height: 100vh;
    }
  </style>
</head>
<body>

<?php include('admin_header.php'); ?>
<?php include('admin_sidebar.php'); ?>

<div class="d-flex justify-content-end align-items-center" style="margin: 20px 30px 0 0;">
  <a href="manage_articles.php" class="btn btn-outline-primary">
    <i class="fas fa-arrow-left me-2"></i> Back
  </a>
</div>


<div class="form-section">
  <div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="mb-0">Edit News Article</h4>
  <a href="manage_articles.php" class="btn btn-outline-primary">
    <i class="fas fa-arrow-left me-1"></i> Back
  </a>
</div>

  <form action="edit_article.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $article['id'] ?>">

    <!-- Featured Image -->
    <div class="mb-4 text-center">
      <img src="<?= htmlspecialchars($article['featured_image']) ?>" class="img-fluid rounded" style="max-height: 300px;" alt="Current Image">
      <p class="mt-2 text-muted">Supported: <strong>.png, .jpg, .jpeg</strong>. Max 900x500px</p>
      <input type="file" name="featured_image" class="form-control mt-2">
    </div>

    <!-- Category -->
    <div class="mb-3">
      <label class="form-label">Category *</label>
      <select name="category_id" class="form-select" required>
        <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
          <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $article['category_id']) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <!-- Title -->
    <div class="mb-3">
      <label class="form-label">News Title *</label>
      <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($article['title']) ?>" required>
    </div>

    <!-- Slug -->
    <div class="mb-3">
      <label class="form-label">Slug *</label>
      <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($article['slug']) ?>" required>
    </div>

    <!-- Short Description (excerpt) -->
    <div class="mb-3">
      <label class="form-label">Excerpt *</label>
      <textarea name="excerpt" class="form-control" rows="3" required><?= htmlspecialchars($article['excerpt']) ?></textarea>
    </div>

    <!-- Full Description -->
    <div class="mb-3">
      <label class="form-label">Content</label>
      <textarea name="content" class="form-control" rows="10"><?= htmlspecialchars($article['content']) ?></textarea>
    </div>

    <!-- Submit -->
  <div class="d-grid gap-3 mt-4">
  <button type="submit" class="btn btn-primary">Update Article</button>
  <a href="manage_articles.php" class="btn btn-secondary">Cancel</a>
</div>

  </form>
</div>

</body>
</html>
