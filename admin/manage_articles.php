<?php
require_once '../includes/auth_admin.php'; 
include('../includes/db_connection.php');

// Handle status update
if (isset($_GET['action']) && in_array($_GET['action'], ['approve', 'reject']) && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $article_id = intval($_GET['id']);
    $new_status = ($_GET['action'] === 'approve') ? 'published' : 'rejected';

    $current_status = $_GET['status'] ?? 'all';
    $update_query = "UPDATE articles SET status = '$new_status' WHERE id = $article_id";
    mysqli_query($conn, $update_query);

    header("Location: manage_articles.php?status=$current_status&updated=1");
    exit();
}

// Detect status type from URL
$status = $_GET['status'] ?? 'all';

// Base query
$query = "
SELECT a.*, 
       u.first_name, u.last_name, u.username, 
       c.name AS category_name
FROM articles a
LEFT JOIN users u ON a.author_id = u.id
LEFT JOIN categories c ON a.category_id = c.id
WHERE 1 = 1
";

// Add status filter (only if not "all")
if ($status !== 'all') {
    $safeStatus = mysqli_real_escape_string($conn, $status);
    $query .= " AND a.status = '$safeStatus'";
}

// Filters
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$date = $_GET['date'] ?? '';

if (!empty($search)) {
    $search = mysqli_real_escape_string($conn, $search);
    $query .= " AND a.title LIKE '%$search%'";
}

if (!empty($category)) {
    $category = (int)$category;
    $query .= " AND a.category_id = $category";
}

if (!empty($date)) {
    $dates = explode('-', $date);
    if (count($dates) === 2) {
        $startDate = trim($dates[0]);
        $endDate = trim($dates[1]);
        $query .= " AND a.created_at BETWEEN '$startDate' AND '$endDate'";
    }
}

$query .= " ORDER BY a.created_at DESC";
$result = mysqli_query($conn, $query);

// Get categories
$categoryQuery = "SELECT id, name FROM categories ORDER BY display_order ASC";
$categories = mysqli_query($conn, $categoryQuery);

// Page titles
$pageTitles = [
    'all' => 'All News',
    'pending' => 'Pending News',
    'published' => 'Approved News',
    'rejected' => 'Rejected News',
    'live' => 'Live News'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Articles</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
    .custom-news-table thead {
      background-color: #0f1c49;
      color: white;
      font-size: 14px;
    }
    .custom-news-table th {
      padding: 12px;
      font-weight: bold;
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

<?php include('admin_header.php'); ?>
<?php include('admin_sidebar.php'); ?>

<!--Main Content -->
<div class="main-content px-4">
  <div class="card p-4 shadow-sm bg-white rounded-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
      <h4 class="mb-0"><?= $pageTitles[$status] ?? 'All News' ?></h4>
    </div>

    <!-- Filters -->
    <form method="GET" class="row g-3 align-items-end mb-4">
      <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
      <div class="col-md-4">
        <input type="text" name="search" class="form-control" placeholder="Search..." value="<?= htmlspecialchars($search ?? '') ?>">
      </div>
      <div class="col-md-3">
        <select name="category" class="form-select">
          <option value="">All Categories</option>
          <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
            <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == ($category ?? '')) ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['name']) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </div>
      <div class="col-md-3">
        <input type="text" name="date" class="form-control" placeholder="Start Date - End Date" value="<?= htmlspecialchars($date ?? '') ?>">
      </div>
      <div class="col-md-2">
        <button type="submit" class="btn btn-primary w-100">Filter</button>
      </div>
    </form>

    <!-- Table -->
    <table class="table table-striped custom-news-table">
      <thead class="table-primary">
        <tr>
          <th>Date</th>
          <th>Staff</th>
          <th>Category</th>
          <th>Title</th>
          <th>Read</th>
          <th>Views</th>
          <th>Status</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)) : ?>
          <tr>
            <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
            <td>
              <?php if ($row['first_name']): ?>
                <?= $row['first_name'] . ' ' . $row['last_name'] ?><br>
                <a href="#">@<?= htmlspecialchars($row['username']) ?></a>
              <?php else: ?>
                N/A
              <?php endif; ?>
            </td>
            <td><strong><?= htmlspecialchars($row['category_name']) ?></strong></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><a href="view_article.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-outline-info">View Article</a></td>
            <td><?= $row['view_count'] ?></td>
            <td>
              <?php
                $statusLabel = $row['status'] ?? 'pending';
                $badgeColor = $statusLabel === 'published' ? 'success' : ($statusLabel === 'rejected' ? 'danger' : 'warning');
              ?>
              <span class="badge bg-<?= $badgeColor ?>"><?= ucfirst($statusLabel) ?></span>
            </td>
            <td>
              <div class="dropdown">
                <button class="btn btn-outline-primary dropdown-toggle btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="fas fa-ellipsis-v"></i> More
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item" href="edit_article.php?id=<?= $row['id'] ?>"><i class="fas fa-pen"></i> Edit</a></li>
                  <li>
                    <a class="dropdown-item" href="manage_articles.php?action=approve&id=<?= $row['id'] ?>&status=<?= $status ?>" >
                      <i class="fas fa-check"></i> Approve
                    </a>
                  </li>
                  <li>
                    <a class="dropdown-item text-danger" href="manage_articles.php?action=reject&id=<?= $row['id'] ?>&status=<?= $status ?>" >
                      <i class="fas fa-times"></i> Reject
                    </a>
                  </li>
                </ul>
              </div>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin/js/admin_script.js"></script>
</body>
</html>
