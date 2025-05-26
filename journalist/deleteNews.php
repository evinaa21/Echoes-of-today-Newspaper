<?php
require_once '../includes/auth_journalist.php';
include_once '../includes/db_connection.php';

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $article_id = intval($_POST['delete_id']);

    // Fetch image path
    $query = "SELECT featured_image FROM articles WHERE id = $article_id";
    $result = mysqli_query($conn, $query);
    $article = mysqli_fetch_assoc($result);

    if ($article) {
        // Delete article
        $deleteQuery = "DELETE FROM articles WHERE id = $article_id";
        if (mysqli_query($conn, $deleteQuery)) {
            // Delete image file if exists
            if (!empty($article['featured_image'])) {
                $imagePath = '../' . $article['featured_image'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
            $_SESSION['success'] = "✅ Article deleted successfully.";
        } else {
            $_SESSION['error'] = "❌ Failed to delete the article.";
        }
    } else {
        $_SESSION['error'] = "❌ Article not found.";
    }

    header("Location: allNews.php");
    exit();
}

// Fetch articles
$articles = mysqli_query($conn, "SELECT a.*, c.name AS category FROM articles a LEFT JOIN categories c ON a.category_id = c.id ORDER BY a.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>All News | Echoes of Today</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">

  <h2 class="mb-4">📰 All Articles</h2>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>

  <table class="table table-bordered">
    <thead class="table-dark">
      <tr>
        <th>#</th>
        <th>Title</th>
        <th>Category</th>
        <th>Status</th>
        <th>Image</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php if (mysqli_num_rows($articles) > 0): $i = 1; ?>
        <?php while ($row = mysqli_fetch_assoc($articles)): ?>
          <tr>
            <td><?= $i++ ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
              <?php if (!empty($row['featured_image'])): ?>
                <img src="../<?= htmlspecialchars($row['featured_image']) ?>" width="80">
              <?php else: ?>
                <span class="text-muted">No image</span>
              <?php endif; ?>
            </td>
            <td>
              <a href="editNews.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">✏️ Edit</a>
              <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteModal" onclick="setDeleteId(<?= $row['id'] ?>)">🗑 Delete</button>
            </td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="6" class="text-center text-muted">No articles found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <form method="POST" action="">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteModalLabel">⚠️ Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this article? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <input type="hidden" name="delete_id" id="delete_id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setDeleteId(id) {
  document.getElementById('delete_id').value = id;
}
</script>
</body>
</html>
