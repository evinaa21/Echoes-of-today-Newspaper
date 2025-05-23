<?php
session_start();
include('../includes/db_connection.php');

// Authenticate user
$journalist_id = $_SESSION['user_id'] ?? 2;
$journalist_id = intval($journalist_id);

// Allowed statuses
$allowed_statuses = [
  'published' => 'Approved News',
  'rejected' => 'Rejected News',
  'pending_review' => 'Pending News'
];

// Validate status parameter
$status_filter = $_GET['status'] ?? '';
if (!array_key_exists($status_filter, $allowed_statuses)) {
  die('<div style="padding: 2rem; color: red;">Invalid status parameter.</div>');
}

$page_title = $allowed_statuses[$status_filter];

// Fetch categories
$categories_result = mysqli_query($conn, "SELECT * FROM categories ORDER BY name");

// Filter values
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? 'all';
$start_date = $_GET['start_date'] ?? '';
$end_date = $_GET['end_date'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link rel="stylesheet" href="css/journalist_style.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />
</head>

<body>
  <div class="d-flex">
    <?php include 'journalistNavBar.php'; ?>

    <div class="container-fluid p-0" style="margin-left: 250px;">
      <?php include 'header.php'; ?>

      <div class="p-4">
        <!-- Title and Create Button -->
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="mb-0"><?= htmlspecialchars($page_title) ?></h2>
          <a href="create_news.php" class="btn btn-primary">+ Create News</a>
        </div>

        <!-- Filter/Search Form -->
        <form method="GET" class="row g-3 mb-4">
          <!-- Retain status in the URL -->
          <input type="hidden" name="status" value="<?= htmlspecialchars($status_filter) ?>">

          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by title">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="all">All</option>
              <?php while ($cat = mysqli_fetch_assoc($categories_result)): ?>
                <option value="<?= $cat['id'] ?>" <?= ($category == $cat['id']) ? 'selected' : '' ?>>
                  <?= htmlspecialchars($cat['name']) ?>
                </option>
              <?php endwhile; ?>
            </select>
          </div>
          <div class="col-md-2">
            <label class="form-label">Start Date</label>
            <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="form-control">
          </div>
          <div class="col-md-2">
            <label class="form-label">End Date</label>
            <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="form-control">
          </div>
          <div class="col-md-2 d-grid align-items-end">
            <button type="submit" class="btn btn-outline-primary">Filter</button>
          </div>
        </form>

        <!-- News Table -->
        <?php
        $query = "
          SELECT a.*, c.name AS category_name
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id
          WHERE a.author_id = $journalist_id
            AND a.status = '$status_filter'
        ";

        if (!empty($search)) {
          $search = mysqli_real_escape_string($conn, $search);
          $query .= " AND a.title LIKE '%$search%'";
        }

        if ($category !== 'all') {
          $category = mysqli_real_escape_string($conn, $category);
          $query .= " AND a.category_id = '$category'";
        }

        if (!empty($start_date) && !empty($end_date)) {
          $query .= " AND DATE(a.created_at) BETWEEN '$start_date' AND '$end_date'";
        }

        $query .= " ORDER BY a.created_at DESC";

        $articles_result = mysqli_query($conn, $query);
        if (!$articles_result) {
          die("Query failed: " . mysqli_error($conn));
        }

        // No edit on status-based pages
        $show_edit_column = true;
        include 'newsTable.php';
        ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
