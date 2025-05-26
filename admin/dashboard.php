<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');
include('admin_header.php');
include('admin_sidebar.php');

// Dashboard stats
$total_articles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM articles"))['count'];
$published_articles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM articles WHERE status = 'published'"))['count'];
$pending_articles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM articles WHERE status = 'pending_review'"))['count'];
$rejected_articles = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM articles WHERE status = 'rejected'"))['count'];
$total_views = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(view_count) AS total FROM articles"))['total'];
$total_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users WHERE role = 'journalist'"))['count'];
$active_staff = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM users WHERE role = 'journalist' AND status = 'active'"))['count'];

// Chart data
$chartData = [];
$res = mysqli_query($conn, "
    SELECT DATE(published_at) AS date, SUM(view_count) AS views
    FROM articles
    WHERE published_at IS NOT NULL
    GROUP BY DATE(published_at)
    ORDER BY date DESC
    LIMIT 7
");
while ($row = mysqli_fetch_assoc($res)) {
  $chartData[] = $row;
}
$chartData = array_reverse($chartData);

// Pie chart data
$categoryViewsData = [];
$res2 = mysqli_query($conn, "
  SELECT categories.name AS category, SUM(view_count) AS views
  FROM articles
  JOIN categories ON articles.category_id = categories.id
  GROUP BY categories.name
  ORDER BY views DESC
  LIMIT 5
");
while ($row = mysqli_fetch_assoc($res2)) {
  $categoryViewsData[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard | EchoToday</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
  <div class="main-content" style="padding-top: 80px;">
    <div class="container-fluid">
      <!-- First row: 3 cards -->
      <div class="row g-4 mb-2">
        <div class="col-12 col-md-6 col-lg-4">
          <!-- Total Staffs -->
          <a href="manage_staff.php" class="info-box blue-outline">
            <div class="info-left">
              <div class="info-icon bg-blue-light"><i class="fas fa-users"></i></div>
              <div class="info-content">
                <div class="info-title">Total Staffs</div>
                <div class="info-number"><?= $total_staff ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
          <!-- Active Staffs -->
          <a href="manage_staff.php?filter=active" class="info-box green-outline">
            <div class="info-left">
              <div class="info-icon bg-green-light"><i class="fas fa-user-check"></i></div>
              <div class="info-content">
                <div class="info-title">Active Staffs</div>
                <div class="info-number"><?= $active_staff ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
          <!-- Total News -->
          <a href="manage_articles.php?status=all" class="info-box gray-outline">
            <div class="info-left">
              <div class="info-icon bg-blue-light"><i class="fas fa-newspaper"></i></div>
              <div class="info-content">
                <div class="info-title">Total News</div>
                <div class="info-number"><?= $total_articles ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>
      </div>

      <!-- Second row: 3 cards -->
      <div class="row g-4">
        <div class="col-12 col-md-6 col-lg-4">
          <!-- Approved News -->
          <a href="manage_articles.php?status=published" class="info-box green-outline">
            <div class="info-left">
              <div class="info-icon bg-green-light"><i class="fas fa-check-circle"></i></div>
              <div class="info-content">
                <div class="info-title">Approved News</div>
                <div class="info-number"><?= $published_articles ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
          <!-- Pending News -->
          <a href="manage_articles.php?status=pending_review" class="info-box orange-outline">
            <div class="info-left">
              <div class="info-icon bg-orange-light"><i class="fas fa-spinner"></i></div>
              <div class="info-content">
                <div class="info-title">Pending News</div>
                <div class="info-number"><?= $pending_articles ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>

        <div class="col-12 col-md-6 col-lg-4">
          <!-- Rejected News -->
          <a href="manage_articles.php?status=rejected" class="info-box red-outline">
            <div class="info-left">
              <div class="info-icon bg-red-light"><i class="fas fa-times-circle"></i></div>
              <div class="info-content">
                <div class="info-title">Rejected News</div>
                <div class="info-number"><?= $rejected_articles ?></div>
              </div>
            </div>
            <div class="info-right"><i class="fas fa-chevron-right"></i></div>
          </a>
        </div>
      </div>


      <!-- Charts Row -->
      <div class="row mt-5 align-items-stretch">
        <!-- Line Chart -->
        <div class="col-md-6 d-flex">
          <div class="card flex-fill" style="min-height: 360px;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Article Views (Last 7 Days)</h5>
              <div class="flex-grow-1">
                <canvas id="viewsChart" style="width: 100%; height: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>

        <!-- Pie Chart -->
        <div class="col-md-6 d-flex">
          <div class="card flex-fill" style="min-height: 360px;">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title">Most Viewed Categories</h5>
              <div class="flex-grow-1">
                <canvas id="categoryPieChart" style="width: 100%; height: 100%;"></canvas>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart Scripts -->
  <script>
    const ctx = document.getElementById('viewsChart').getContext('2d');
    new Chart(ctx, {
      type: 'line',
      data: {
        labels: <?= json_encode(array_column($chartData, 'date')) ?>,
        datasets: [{
          label: 'Views',
          data: <?= json_encode(array_map('intval', array_column($chartData, 'views'))) ?>,
          fill: true,
          borderColor: 'rgba(0, 123, 255, 1)',
          backgroundColor: 'rgba(0, 123, 255, 0.1)',
          tension: 0.3
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: {
            display: false
          }
        },
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

    const pieCtx = document.getElementById('categoryPieChart').getContext('2d');
    new Chart(pieCtx, {
      type: 'pie',
      data: {
        labels: <?= json_encode(array_column($categoryViewsData, 'category')) ?>,
        datasets: [{
          data: <?= json_encode(array_map('intval', array_column($categoryViewsData, 'views'))) ?>,
          backgroundColor: [
            '#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'bottom'
          }
        }
      }
    });
  </script>
</body>

</html>