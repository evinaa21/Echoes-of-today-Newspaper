<?php
require_once '../includes/auth_journalist.php';
include_once '../includes/db_connection.php';

$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 0;
$query = "SELECT * FROM articles WHERE id = $article_id";
$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
    echo "<div class='alert alert-danger text-center mt-5'>❌ Article not found.</div>";
    exit;
}
$article = mysqli_fetch_assoc($result);

if (!in_array($article['status'], ['pending_review', 'rejected'])) {
    echo "<div class='alert alert-warning text-center mt-5'>⚠️ You can only edit articles that are pending or rejected.</div>";
    exit;
}

$categories = mysqli_query($conn, "SELECT id, name FROM categories WHERE is_active = 1 ORDER BY name");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Edit Article | Echoes of Today</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@400;600&display=swap"
        rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f9fafc;
            color: #333;
        }

        .content-card-wrapper {
            background-color: #f0f2f5;
            padding: 30px;
            border-radius: 12px;
        }

        .content-card {
            background-color: white;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
            max-width: 950px;
            margin: 0 auto;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 25px;
            border: 1px solid #dee2e6;
            padding: 10px 22px;
            font-weight: 500;
            border-radius: 50px;
            background-color: #ffffff;
            color: #0d6efd;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
            transition: 0.2s ease;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: #0d6efd;
            color: #ffffff;
        }

        .form-label {
            font-weight: 600;
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include 'journalistNavBar.php'; ?>

        <div class="container-fluid p-0" style="margin-left: 250px;">
            <?php include 'header.php'; ?>

            <div class="p-4 content-card-wrapper">
                <a href="allNews.php" class="back-btn">
                    <i class="fas fa-arrow-left me-2"></i>Back to News
                </a>

                <?php if (isset($message))
                    echo $message; ?>
                <div class="content-card">
                    <h2 class="mb-4 title-header" style="font-family: 'Playfair Display', serif;">✏️ Edit Article</h2>

                    <form action="updateNews.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $article_id ?>">
                        <input type="hidden" name="current_image"
                            value="<?= htmlspecialchars($article['featured_image']) ?>">

                        <div class="mb-3">
                            <label class="form-label">Title *</label>
                            <input type="text" name="title" class="form-control"
                                value="<?= htmlspecialchars($article['title']) ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category *</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select category</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $article['category_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags (comma-separated)</label>
                            <input type="text" name="tags" class="form-control"
                                value="<?= htmlspecialchars($article['tags']) ?>">
                        </div>



                        <div class="mb-3">
                            <label class="form-label">Excerpt</label>
                            <textarea name="excerpt" class="form-control"
                                rows="2"><?= htmlspecialchars($article['excerpt']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Content *</label>
                            <textarea name="content" class="form-control" rows="8"
                                required><?= htmlspecialchars($article['content']) ?></textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">YouTube Video Link</label>
                            <input type="url" name="youtube_link" class="form-control"
                                value="<?= htmlspecialchars($article['youtube_link']) ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Featured Image</label>
                            <?php if (!empty($article['featured_image'])): ?>
                                <div class="mb-3">
                                    <label class="form-label">Current Image:</label><br>
                                    <img src="../uploads/<?= htmlspecialchars($article['featured_image']) ?>" alt="News Image"
                                        style="max-width: 200px;">
                                    <div class="form-check mt-2">
                                        <input class="form-check-input" type="checkbox" name="delete_image"
                                            id="delete_image">
                                        <label class="form-check-label" for="delete_image">Delete Current Image</label>
                                    </div>
                                </div>
                            <?php endif; ?>

                            <input type="file" name="featured_image" class="form-control">
                        </div>

                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_featured" id="is_featured"
                                <?= $article['is_featured'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_featured">Mark as Featured</label>
                        </div>

                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input" type="checkbox" name="trending" id="trending"
                                <?= $article['trending'] ? 'checked' : '' ?>>
                            <label class="form-check-label" for="trending">Mark as Trending</label>
                        </div>

                        <button type="submit" class="btn btn-primary px-4">💾 Save Changes</button>
                        <a href="allNews.php" class="btn btn-secondary ms-2">← Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
        <!-- Success Modal -->
        <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content text-success border-success">
                    <div class="modal-header">
                        <h5 class="modal-title" id="successModalLabel">✅ Success</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        News updated successfully!
                    </div>
                </div>
            </div>
        </div>
        <script>
            var successModal = new bootstrap.Modal(document.getElementById('successModal'));
            successModal.show();
        </script>
    <?php endif; ?>

</body>

</html>