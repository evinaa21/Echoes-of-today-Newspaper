<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');
include('admin_header.php');
include('admin_sidebar.php');

// 1. Article Views (Last 7 Days)
$viewsByDate = mysqli_query($conn, "
    SELECT DATE(published_at) AS date, SUM(view_count) AS views
    FROM articles
    WHERE published_at >= CURDATE() - INTERVAL 7 DAY
    GROUP BY DATE(published_at)
    ORDER BY date ASC
");
$dates = [];
$views = [];
while ($row = mysqli_fetch_assoc($viewsByDate)) {
    $dates[] = $row['date'];
    $views[] = $row['views'];
}

// 2. Most Viewed Categories
$categoryViews = mysqli_query($conn, "
    SELECT c.name AS category, SUM(a.view_count) AS views
    FROM articles a
    JOIN categories c ON a.category_id = c.id
    GROUP BY a.category_id
    ORDER BY views DESC
");
$catLabels = [];
$catData = [];
while ($row = mysqli_fetch_assoc($categoryViews)) {
    $catLabels[] = $row['category'];
    $catData[] = $row['views'];
}

// 3. Journalist Performance
$journalists = mysqli_query($conn, "
    SELECT u.username, COUNT(a.id) AS articles
    FROM users u
    JOIN articles a ON a.author_id = u.id
    WHERE u.role = 'journalist'
    GROUP BY u.id
");
$journNames = [];
$journCounts = [];
while ($row = mysqli_fetch_assoc($journalists)) {
    $journNames[] = $row['username'];
    $journCounts[] = $row['articles'];
}

// 4. Advertisement Clicks
$adClicks = mysqli_query($conn, "
    SELECT name, clicks FROM advertisements ORDER BY clicks DESC
");
$adNames = [];
$clicks = [];
while ($row = mysqli_fetch_assoc($adClicks)) {
    $adNames[] = $row['name'];
    $clicks[] = $row['clicks'];
}

// 5. Advertisement Count by Type
$adTypes = mysqli_query($conn, "
    SELECT ad_type, COUNT(*) AS count FROM advertisements GROUP BY ad_type
");
$typeLabels = [];
$typeCounts = [];
while ($row = mysqli_fetch_assoc($adTypes)) {
    $typeLabels[] = $row['ad_type'];
    $typeCounts[] = $row['count'];
}

// 6. New Articles Published (Last 7 Days)
$newArticles = mysqli_query($conn, "
    SELECT DATE(published_at) AS date, COUNT(*) AS count
    FROM articles
    WHERE published_at >= CURDATE() - INTERVAL 7 DAY
    GROUP BY DATE(published_at)
    ORDER BY date ASC
");
$newDates = [];
$newCounts = [];
while ($row = mysqli_fetch_assoc($newArticles)) {
    $newDates[] = $row['date'];
    $newCounts[] = $row['count'];
}
?>

<!-- Required for Chart.js and Layout -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
canvas {
    width: 100% !important;
    height: auto !important;
    display: block;
    max-height: 250px;
}
.card {
    border-radius: 10px;
    box-shadow: 0 0 10px rgba(0,0,0,0.05);
    height: 100%;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}
.main-container {
    margin-left: 250px; /* same as your .sidebar width */
    padding-top: 80px;  /* space for header height */
    padding-right: 20px;
    padding-left: 20px;
}

</style>

<div class="main-container">
    <h4 class="mb-4 d-flex justify-content-between align-items-center">
        Analytics Reports
        <button class="btn btn-outline-primary btn-sm" onclick="window.print()">Export to PDF</button>
    </h4>

    <div class="row mb-4">
        <div class="col-md-6">
            <input type="date" id="startDate" class="form-control" placeholder="Start Date">
        </div>
        <div class="col-md-6">
            <input type="date" id="endDate" class="form-control" placeholder="End Date">
        </div>
    </div>

    <!-- ALL CHARTS IN 2-COLUMN LAYOUT -->
    <div class="row row-cols-1 row-cols-md-2 g-4">
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>Article Views (Last 7 Days)</h5>
                <canvas id="viewsChart"></canvas>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>Most Viewed Categories</h5>
                <canvas id="categoriesChart"></canvas>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>Journalist Performance</h5>
                <canvas id="journalistChart"></canvas>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>Advertisement Clicks</h5>
                <canvas id="adClicksChart"></canvas>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>Advertisement Types</h5>
                <canvas id="adTypesChart"></canvas>
            </div>
        </div>
        <div class="col d-flex">
            <div class="card p-3 w-100">
                <h5>New Articles (Last 7 Days)</h5>
                <canvas id="newArticlesChart"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart Initialization -->
<script>
new Chart(document.getElementById('viewsChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($dates) ?>,
        datasets: [{
            label: 'Views',
            data: <?= json_encode($views) ?>,
            borderColor: 'blue',
            backgroundColor: 'rgba(0, 123, 255, 0.2)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('categoriesChart'), {
    type: 'pie',
    data: {
        labels: <?= json_encode($catLabels) ?>,
        datasets: [{
            data: <?= json_encode($catData) ?>,
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545', '#6f42c1']
        }]
    },
    options: { responsive: true }
});

new Chart(document.getElementById('journalistChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($journNames) ?>,
        datasets: [{
            label: 'Articles',
            data: <?= json_encode($journCounts) ?>,
            backgroundColor: '#17a2b8'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('adClicksChart'), {
    type: 'bar',
    data: {
        labels: <?= json_encode($adNames) ?>,
        datasets: [{
            label: 'Clicks',
            data: <?= json_encode($clicks) ?>,
            backgroundColor: '#6610f2'
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});

new Chart(document.getElementById('adTypesChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode($typeLabels) ?>,
        datasets: [{
            data: <?= json_encode($typeCounts) ?>,
            backgroundColor: ['#20c997', '#fd7e14', '#6f42c1', '#e83e8c']
        }]
    },
    options: { responsive: true }
});

new Chart(document.getElementById('newArticlesChart'), {
    type: 'line',
    data: {
        labels: <?= json_encode($newDates) ?>,
        datasets: [{
            label: 'New Articles',
            data: <?= json_encode($newCounts) ?>,
            borderColor: '#198754',
            backgroundColor: 'rgba(25, 135, 84, 0.2)',
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: { beginAtZero: true }
        }
    }
});
</script>
