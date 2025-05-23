<?php
session_start();
include_once('../includes/db_connection.php');

// ❌ Temporarily allow access without login check (for testing)
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'journalist') {
//     header('Location: ../login.php');
//     exit;
// }

// ✅ Fallback dummy user ID (for testing only)
$journalist_id = $_SESSION['user_id'] ?? 2; // fallback to user ID 2 if not logged in
$journalist_id = intval($journalist_id);    // secure cast


// Fetch summary counts
$total_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id";
$approved_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'published'";
$pending_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'pending_review'";
$rejected_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'rejected'";

$total = mysqli_fetch_row(mysqli_query($conn, $total_query))[0];
$approved = mysqli_fetch_row(mysqli_query($conn, $approved_query))[0];
$pending = mysqli_fetch_row(mysqli_query($conn, $pending_query))[0];
$rejected = mysqli_fetch_row(mysqli_query($conn, $rejected_query))[0];

// Fetch article data
$articles_query = "
SELECT a.*, c.name AS category_name
FROM articles a
LEFT JOIN categories c ON a.category_id = c.id
WHERE a.author_id = $journalist_id
ORDER BY a.created_at DESC
";
$articles_result = mysqli_query($conn, $articles_query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Journalist Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
  <link href="css/journalist_style.css" rel="stylesheet" />
</head>

<body>
  <div class="d-flex">
    <?php include 'journalistNavBar.php'; ?>

    <div class="container-fluid p-0" style="margin-left: 250px;">
      <?php include 'header.php'; ?>

      <div class="p-4">
        <h2 class="mb-4">Journalist Dashboard</h2>

        <!-- Summary Cards -->
        <div class="row mb-4">
          <div class="col-md-3">
            <a href="allNews.php" class="text-decoration-none">
              <div class="card card-summary p-3 text-center zoom-card">
                <h5>Total News</h5>
                <h3><?= $total ?></h3>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="newsByStatus.php?status=published" class="text-decoration-none">
              <div class="card card-summary p-3 border-success text-center zoom-card">
                <h5>Approved News</h5>
                <h3><?= $approved ?></h3>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="newsByStatus.php?status=pending_review" class="text-decoration-none">
              <div class="card card-summary p-3 border-warning text-center zoom-card">
                <h5>Pending News</h5>
                <h3><?= $pending ?></h3>
              </div>
            </a>
          </div>
          <div class="col-md-3">
            <a href="newsByStatus.php?status=rejected" class="text-decoration-none">
              <div class="card card-summary p-3 border-danger text-center zoom-card">
                <h5>Rejected News</h5>
                <h3><?= $rejected ?></h3>
              </div>
            </a>
          </div>
        </div>


        <!-- News Table -->
        <?php include 'newsTable.php'; ?>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>