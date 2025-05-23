<?php
session_start();
include_once '../includes/db_connection.php';

// Validate and fetch article
$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;

$query = "SELECT * FROM articles WHERE id = $article_id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<div class='alert alert-danger text-center mt-5'>❌ Article not found.</div>";
    exit;
}
$article = mysqli_fetch_assoc($result);

// Fetch categories for dropdown
$categories = mysqli_query($conn, "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Article</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f4f6fa;
      font-family: 'Segoe UI', sans-serif;
    }
    .form-section {
      max-width: 900px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    }
    .form-section h4 {
      font-weight: bold;
      color: #0f1c49;
    }
    .form-check-label {
      margin-left: 0.4rem;
    }
  </style>
</head>
<body>

<div class="container form-section">
  <h4 class="mb-4">✏️ Edit Article</h4>
  <form action="updateArticle.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?= $article_id ?>">

    <div class="mb-3">
      <label for="title" class="form-label">Title *</label>
      <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($article['title']) ?>" required>
    </div>

    <div class="mb-3">
      <label for="category" class="form-label">Category *</label>
      <select class="form-select" name="category_id" required>
        <option value="">Select category</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
          <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $article['category_id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="mb-3">
      <label for="tags" class="form-label">Tags (comma-separated)</label>
      <input type="text" class="form-control" id="tags" name="tags" value="<?= htmlspecialchars($article['tags']) ?>">
    </div>

    <div class="mb-3">
      <label for="status" class="form-label">Status *</label>
      <select class="form-select" name="status" required>
        <option value="draft" <?= $article['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
        <option value="pending_review" <?= $article['status'] === 'pending_review' ? 'selected' : '' ?>>Pending Review</option>
        <option value="published" <?= $article['status'] === 'published' ? 'selected' : '' ?>>Published</option>
        <option value="rejected" <?= $article['status'] === 'rejected' ? 'selected' : '' ?>>Rejected</option>
      </select>
    </div>

    <div class="mb-3">
      <label for="excerpt" class="form-label">Excerpt</label>
      <textarea class="form-control" name="excerpt" rows="2"><?= htmlspecialchars($article['excerpt']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="content" class="form-label">Content *</label>
      <textarea class="form-control" name="content" rows="8" required><?= htmlspecialchars($article['content']) ?></textarea>
    </div>

    <div class="mb-3">
      <label for="youtube_link" class="form-label">YouTube Video Link</label>
      <input type="url" class="form-control" name="youtube_link" value="<?= htmlspecialchars($article['youtube_link']) ?>">
    </div>

    <div class="mb-3">
      <label class="form-label d-block">Featured Image</label>
      <?php if (!empty($article['featured_image'])): ?>
        <img src="../<?= htmlspecialchars($article['featured_image']) ?>" alt="Featured Image" style="max-width: 150px;" class="mb-2 d-block">
      <?php endif; ?>
      <input type="file" class="form-control" name="featured_image">
    </div>

    <div class="form-check form-switch mb-2">
      <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured" <?= $article['is_featured'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="is_featured">Mark as Featured</label>
    </div>

    <div class="form-check form-switch mb-4">
      <input class="form-check-input" type="checkbox" name="trending" id="trending" <?= $article['trending'] ? 'checked' : '' ?>>
      <label class="form-check-label" for="trending">Mark as Trending</label>
    </div>

    <button type="submit" class="btn btn-primary px-4">💾 Save Changes</button>
    <a href="allNews.php" class="btn btn-secondary ms-2">← Cancel</a>
  </form>
</div>

</body>
</html>
