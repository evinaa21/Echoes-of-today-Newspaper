<?php
session_start();
include_once '../includes/db_connection.php';

$article_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : 7;

$query = "SELECT a.*, c.name AS category_name, u.username AS author_name
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id
          LEFT JOIN users u ON a.author_id = u.id
          WHERE a.id = $article_id";

$result = mysqli_query($conn, $query);
if (!$result || mysqli_num_rows($result) === 0) {
  echo "<div class='alert alert-danger text-center mt-5'>❌ Article not found.</div>";
  exit;
}

$article = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($article['title']) ?> | Echoes of Today</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link
    href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@400;600&display=swap"
    rel="stylesheet">
  <link href="css/journalist_style.css" rel="stylesheet">
  <style>
    
    body {
      font-family: 'Inter', sans-serif;
      background-color: #f9fafc;
      color: #333;
    }

    .title-header {
      font-family: 'Playfair Display', serif;
      font-size: 2.5rem;
      font-weight: 700;
      line-height: 1.3;
    }

    .meta-info {
      font-size: 0.95rem;
      color: #777;
    }

    .featured-img {
      width: 100%;
      height: auto;
      border-radius: 16px;
      margin: 20px 0;
      object-fit: cover;
      max-height: 420px;
    }

    .tag-badge {
      font-size: 0.85rem;
      margin-right: 6px;
      padding: 6px 12px;
      border-radius: 20px;
      background-color: #e9ecef;
      color: #555;
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

    .article-content {
      white-space: pre-line;
      line-height: 1.8;
      font-size: 1.05rem;
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

        <div class="content-card">
          <h1 class="title-header"><?= htmlspecialchars($article['title']) ?></h1>

          <div class="meta-info mb-2">
            By <strong><?= htmlspecialchars($article['author_name']) ?></strong> |
            <?= htmlspecialchars($article['category_name']) ?> 
            <?= $article['published_at'] ? date("M d, Y", strtotime($article['published_at'])) : ' ' ?>
          </div>

          <?php if (!empty($article['featured_image'])): ?>
            <img src="../<?= htmlspecialchars($article['featured_image']) ?>" class="featured-img" alt="Featured Image">
          <?php endif; ?>

          <div class="mb-4">
            <span class="tag-badge"><i class="fas fa-eye me-1"></i><?= number_format($article['view_count']) ?>
              views</span>
            <span class="tag-badge"><?= $article['tags'] ?: 'No tags' ?></span>
            <span class="tag-badge"><?= ucfirst($article['status']) ?></span>
            <?php if ($article['is_featured'])
              echo '<span class="tag-badge bg-primary text-white">🌟 Featured</span>'; ?>
            <?php if ($article['trending'])
              echo '<span class="tag-badge bg-danger text-white">🔥 Trending</span>'; ?>
          </div>

          <?php if (!empty($article['excerpt'])): ?>
            <p class="lead fw-normal"><?= htmlspecialchars($article['excerpt']) ?></p>
          <?php endif; ?>

          <?php if (!empty($article['youtube_link']) && $article['has_video']): ?>
            <iframe class="w-100 mb-4" height="360" src="<?= htmlspecialchars($article['youtube_link']) ?>"
              frameborder="0" allowfullscreen style="border-radius: 10px;"></iframe>
          <?php endif; ?>

          <div class="article-content text-muted"><?= nl2br(htmlspecialchars($article['content'])) ?></div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>