<?php 
session_start();
include('../includes/db_connection.php');

// Defaults
$journalist_id = $_SESSION['user_id'] ?? 2;
$journalist_id = intval($journalist_id);

// Pagination
$limit = 10;
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

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
  <title>All News</title>
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
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2 class="mb-0">All News</h2>
          <a href="createNews.php" class="btn btn-primary">+ Create News</a>
        </div>

        <!-- Filter/Search Form -->
        <form method="GET" class="row g-3 mb-4">
          <div class="col-md-4">
            <label class="form-label">Search</label>
            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" class="form-control" placeholder="Search by title">
          </div>
          <div class="col-md-2">
            <label class="form-label">Category</label>
            <select name="category" class="form-select">
              <option value="all">All</option>
              <?php mysqli_data_seek($categories_result, 0); while ($cat = mysqli_fetch_assoc($categories_result)): ?>
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
        $base_query = "
          FROM articles a
          LEFT JOIN categories c ON a.category_id = c.id
          WHERE a.author_id = $journalist_id
        ";

        if (!empty($search)) {
          $search_safe = mysqli_real_escape_string($conn, $search);
          $base_query .= " AND a.title LIKE '%$search_safe%'";
        }

        if ($category !== 'all') {
          $category_safe = mysqli_real_escape_string($conn, $category);
          $base_query .= " AND a.category_id = '$category_safe'";
        }

        if (!empty($start_date) && !empty($end_date)) {
          $base_query .= " AND DATE(a.created_at) BETWEEN '$start_date' AND '$end_date'";
        }

        // Get total for pagination
        $count_result = mysqli_query($conn, "SELECT COUNT(*) AS total " . $base_query);
        $total_rows = mysqli_fetch_assoc($count_result)['total'];
        $total_pages = ceil($total_rows / $limit);

        // Fetch paginated results
        $final_query = "SELECT a.*, c.name AS category_name $base_query ORDER BY a.created_at DESC LIMIT $limit OFFSET $offset";
        $articles_result = mysqli_query($conn, $final_query);
        if (!$articles_result) {
          die("Query failed: " . mysqli_error($conn));
        }

        $show_edit_column = true;
        include 'newsTable.php';
        ?>

        <!-- Pagination Links -->
        <?php if ($total_pages > 1): ?>
          <nav>
            <ul class="pagination">
              <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                  <a class="page-link" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                </li>
              <?php endfor; ?>
            </ul>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
