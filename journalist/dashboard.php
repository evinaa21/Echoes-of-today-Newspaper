<?php
session_start();
include_once('../includes/db_connection.php');

$journalist_id = $_SESSION['user_id'] ?? 2;
$journalist_id = intval($journalist_id);

// Counts
$total_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id";
$approved_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'published'";
$pending_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'pending_review'";
$rejected_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND status = 'rejected'";
$views_query = "SELECT SUM(view_count) FROM articles WHERE author_id = $journalist_id";
$featured_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND is_featured = 1";
$trending_query = "SELECT COUNT(*) FROM articles WHERE author_id = $journalist_id AND trending = 1";

$total = mysqli_fetch_row(mysqli_query($conn, $total_query))[0];
$approved = mysqli_fetch_row(mysqli_query($conn, $approved_query))[0];
$pending = mysqli_fetch_row(mysqli_query($conn, $pending_query))[0];
$rejected = mysqli_fetch_row(mysqli_query($conn, $rejected_query))[0];
$total_views = mysqli_fetch_row(mysqli_query($conn, $views_query))[0] ?? 0;
$featured = mysqli_fetch_row(mysqli_query($conn, $featured_query))[0];
$trending = mysqli_fetch_row(mysqli_query($conn, $trending_query))[0];

// Articles per month (ensure all 12 months show)
$months = [
  'January' => 0, 'February' => 0, 'March' => 0, 'April' => 0,
  'May' => 0, 'June' => 0, 'July' => 0, 'August' => 0,
  'September' => 0, 'October' => 0, 'November' => 0, 'December' => 0
];
$monthly_query = "SELECT MONTH(created_at) AS month_num, COUNT(*) AS count 
                  FROM articles 
                  WHERE author_id = $journalist_id 
                  GROUP BY month_num";
$monthly_result = mysqli_query($conn, $monthly_query);
while ($row = mysqli_fetch_assoc($monthly_result)) {
  $monthIndex = intval($row['month_num']) - 1;
  $monthNames = array_keys($months);
  $months[$monthNames[$monthIndex]] = $row['count'];
}
$month_labels = array_keys($months);
$month_counts = array_values($months);

// Articles per category
$categories = [];
$category_counts = [];
$category_query = "SELECT c.name AS category, COUNT(*) AS count 
                   FROM articles a 
                   JOIN categories c ON a.category_id = c.id 
                   WHERE a.author_id = $journalist_id 
                   GROUP BY category";
$category_result = mysqli_query($conn, $category_query);
while ($row = mysqli_fetch_assoc($category_result)) {
  $categories[] = $row['category'];
  $category_counts[] = $row['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Journalist Dashboard</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    .chart-container {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
    }
    .chart-card {
      flex: 1;
      min-width: 300px;
      max-width: 48%;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #dee2e6;
      border-radius: 8px;
      box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    canvas {
      width: 100% !important;
      height: auto !important;
    }
    .dashboard-card {
      font-size: 0.9rem;
      padding: 15px;
      border-left: 4px solid #0d47a1 !important;
      border-radius: 8px;
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .dashboard-card:hover {
      transform: scale(1.05);
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
    }
    .dashboard-card .fs-4 {
      font-size: 1.5rem !important;
    }
    .dashboard-card .fs-4.total { color:rgb(5, 20, 44); }
    .dashboard-card .fs-4.featured { color: (5, 20, 44); }
    .dashboard-card .fs-4.trending { color: (5, 20, 44); }
    .dashboard-card .fs-4.views { color: (5, 20, 44) }
  </style>
</head>
<body>
  <div class="d-flex">
    <?php include 'journalistNavBar.php'; ?>
    <div class="container-fluid p-0" style="margin-left: 250px;">
      <?php include 'header.php'; ?>
      <div class="p-4">
        <h2 class="mb-4">Journalist Dashboard</h2>
        <div class="row mb-4">
          <div class="col-md-3">
            <div class="card dashboard-card shadow-sm">
              <div class="text-muted small">Total News</div>
              <div class="fs-4 fw-semibold total"><?= $total ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dashboard-card shadow-sm">
              <div class="text-muted small">Featured</div>
              <div class="fs-4 fw-semibold featured"><?= $featured ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dashboard-card shadow-sm">
              <div class="text-muted small">Trending</div>
              <div class="fs-4 fw-semibold trending"><?= $trending ?></div>
            </div>
          </div>
          <div class="col-md-3">
            <div class="card dashboard-card shadow-sm">
              <div class="text-muted small">Total Views</div>
              <div class="fs-4 fw-semibold views"><?= number_format($total_views) ?></div>
            </div>
          </div>
        </div>
        <div class="chart-container">
          <div class="chart-card">
            <h5 class="mb-3">Status Distribution</h5>
            <canvas id="statusChart"></canvas>
          </div>
          <div class="chart-card">
            <h5 class="mb-3">Articles by Month</h5>
            <canvas id="monthlyChart"></canvas>
          </div>
          <div class="chart-card">
            <h5 class="mb-3">Articles by Category</h5>
            <canvas id="categoryChart"></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    new Chart(document.getElementById('statusChart').getContext('2d'), {
      type: 'pie',
      data: {
        labels: ['Approved', 'Pending', 'Rejected'],
        datasets: [{
          data: [<?= $approved ?>, <?= $pending ?>, <?= $rejected ?>],
          backgroundColor: ['#198754', '#ffc107', '#dc3545'],
          borderColor: ['#fff'],
          borderWidth: 1
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' },
          tooltip: {
            callbacks: {
              label: function(context) {
                const total = <?= $approved + $pending + $rejected ?>;
                const val = context.raw;
                const pct = ((val / total) * 100).toFixed(1);
                return `${context.label}: ${val} (${pct}%)`;
              }
            }
          }
        }
      }
    });

    new Chart(document.getElementById('monthlyChart').getContext('2d'), {
      type: 'bar',
      data: {
        labels: <?= json_encode($month_labels) ?>,
        datasets: [{
          label: 'Articles Created',
          data: <?= json_encode($month_counts) ?>,
          backgroundColor: '#0d6efd'
        }]
      },
      options: {
        responsive: true,
        scales: {
          y: {
            beginAtZero: true,
            precision: 0
          }
        }
      }
    });

    new Chart(document.getElementById('categoryChart').getContext('2d'), {
      type: 'doughnut',
      data: {
        labels: <?= json_encode($categories) ?>,
        datasets: [{
          data: <?= json_encode($category_counts) ?>,
          backgroundColor: ['#6610f2', '#0d6efd', '#20c997', '#ffc107', '#fd7e14', '#dc3545', '#6c757d', '#198754', '#0dcaf0']
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { position: 'bottom' }
        }
      }
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
