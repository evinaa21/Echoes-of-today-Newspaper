<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

ini_set('display_errors', 1);
error_reporting(E_ALL);

// Handle approve/reject action
if (isset($_GET['action'], $_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE articles SET status = 'published', published_at = NOW(), updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "✅ Article approved successfully.";
        } else {
            $_SESSION['error'] = "❌ Failed to approve article.";
        }
        header("Location: manage_articles.php");
        exit();
    }

    if ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE articles SET status = 'rejected', updated_at = NOW() WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success'] = "🚫 Article rejected successfully.";
        } else {
            $_SESSION['error'] = "❌ Failed to reject article.";
        }
        header("Location: manage_articles.php");
        exit();
    }
}

// Standard article view flow
$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    header("Location: manage_articles.php");
    exit();
}

$stmt = $conn->prepare("SELECT a.*, c.name AS category_name, CONCAT(u.first_name, ' ', u.last_name) AS author_name
                        FROM articles a
                        LEFT JOIN categories c ON a.category_id = c.id
                        LEFT JOIN users u ON a.author_id = u.id
                        WHERE a.id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$article = $result->fetch_assoc();

if (!$article) {
    echo "Article not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($article['title']) ?> | Echoes of Today</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@500;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
  <link href="css/o_style.css" rel="stylesheet">
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
    .content-card {
      background-color: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    }
    .article-content {
  font-size: 1.05rem;
  line-height: 1.5;
  white-space: normal; 
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
    .admin-buttons {
      text-align: center;
      margin-top: 30px;
    }
    .admin-buttons a {
      margin: 0 10px;
    }

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

<div class="d-flex">
  <?php include('admin_sidebar.php'); ?>

  <div class="container-fluid p-0" style="margin-left: 250px;">
    <?php include('admin_header.php'); ?>
    
<div class="p-4 mt-5 pt-4">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-10 col-xl-9">

        <div class="d-flex justify-content-start align-items-center mb-3">
          <?php if (isset($_GET['from_staff']) && is_numeric($_GET['from_staff'])): ?>
          <a href="staff_detail.php?id=<?= intval($_GET['from_staff']) ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Back to Staff Profile
          </a>
        <?php else: ?>
          <a href="manage_articles.php" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-2"></i> Back to Manage Articles
          </a>
        <?php endif; ?>
        </div>

      <div class="content-card">
        <h1 class="title-header"><?= htmlspecialchars($article['title']) ?></h1>

        <div class="meta-info mb-2">
          By <strong><?= htmlspecialchars($article['author_name']) ?></strong> |
          <?= htmlspecialchars($article['category_name']) ?> |
          <?= $article['published_at'] ? date("M d, Y", strtotime($article['published_at'])) : '' ?>
        </div>

        <?php if (!empty($article['featured_image'])): ?>
        <img src="../uploads/<?= htmlspecialchars($article['featured_image']) ?>" class="featured-img" alt="Featured Image">
        <?php endif; ?>


        <div class="d-flex flex-wrap gap-2 mb-4">
        <span class="tag-badge"><i class="fas fa-eye me-1"></i><?= number_format($article['view_count']) ?> views</span>
        <span class="tag-badge"><?= $article['tags'] ?: 'No tags' ?></span>
        <span class="tag-badge"><?= ucfirst($article['status']) ?></span>
        <?php if ($article['is_featured']) echo '<span class="tag-badge bg-primary text-white">🌟 Featured</span>'; ?>
        <?php if ($article['trending']) echo '<span class="tag-badge bg-danger text-white">🔥 Trending</span>'; ?>
        </div>


        <?php if (!empty($article['excerpt'])): ?>
          <p class="lead"><?= htmlspecialchars($article['excerpt']) ?></p>
        <?php endif; ?>

        <?php if (!empty($article['youtube_link']) && $article['has_video']): ?>
          <iframe class="w-100 mb-4" height="360" src="<?= htmlspecialchars($article['youtube_link']) ?>" frameborder="0" allowfullscreen style="border-radius: 10px;"></iframe>
        <?php endif; ?>

        <div class="article-content text-muted"><?= nl2br(htmlspecialchars($article['content'])) ?></div>

        <!-- Admin Buttons -->
        <div class="admin-buttons">
          <a href="edit_article.php?id=<?= $article['id'] ?>" class="btn btn-warning">
            <i class="fas fa-edit me-1"></i> Edit
          </a>
          <a href="view_article.php?id=<?= $article['id'] ?>&action=approve" class="btn btn-success">
            <i class="fas fa-check me-1"></i> Approve
          </a>
          <a href="view_article.php?id=<?= $article['id'] ?>&action=reject" class="btn btn-danger">
            <i class="fas fa-times me-1"></i> Reject
          </a>
        </div>

      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
