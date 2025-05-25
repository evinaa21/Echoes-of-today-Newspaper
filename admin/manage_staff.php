<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['check_duplicate'])) {
    include('../includes/db_connection.php');

    $email = $_POST['email'] ?? '';
    $username = $_POST['username'] ?? '';
    $response = ['exists' => false];

    if ($email || $username) {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
        $stmt->bind_param("ss", $email, $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $response['exists'] = true;
        }

        $stmt->close();
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}
session_start();
include('../includes/db_connection.php');

//Handle fetching single staff info (for filtering)
$staff_id = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : null;
$user = null;
if ($staff_id) {
    $userQuery = "SELECT * FROM users WHERE id = $staff_id";
    $userResult = mysqli_query($conn, $userQuery);
    $user = mysqli_fetch_assoc($userResult);
}



//Filters
$filter = $_GET['filter'] ?? 'active';
$status = $_GET['status'] ?? 'all';
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$date = $_GET['date'] ?? '';

//Load all categories for filters
$categories = mysqli_query($conn, "SELECT id, name FROM categories ORDER BY name");

//Status titles for different views
$pageTitles = [
    'all' => 'All News',
    'published' => 'Approved News',
    'pending_review' => 'Pending News',
    'rejected' => 'Rejected News'
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Manage Staff</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="css/admin_style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <style>
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

<div class="main-content px-4 pt-4">
  <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>

  <div class="card p-4 shadow-sm bg-white rounded-4">

  <?php if ($staff_id): ?>
  <?php
    $query = "
      SELECT a.*, u.first_name, u.last_name, c.name AS category_name
      FROM articles a
      LEFT JOIN users u ON a.author_id = u.id
      LEFT JOIN categories c ON a.category_id = c.id
      WHERE a.author_id = $staff_id
    ";

    if ($status !== 'all') {
        $safeStatus = mysqli_real_escape_string($conn, $status);
        $query .= " AND a.status = '$safeStatus'";
    }

    if (!empty($category)) {
        $safe_category = intval($category);
        $query .= " AND a.category_id = $safe_category";
    }

    if (!empty($date)) {
        $dates = explode('-', $date);
        if (count($dates) === 2) {
            $start = trim($dates[0]);
            $end = trim($dates[1]);
            $query .= " AND DATE(a.created_at) BETWEEN '$start' AND '$end'";
        }
    }

    if (!empty($search)) {
        $safe_search = mysqli_real_escape_string($conn, $search);
        $query .= " AND (
            CONCAT(u.first_name, ' ', u.last_name) LIKE '%$safe_search%' OR 
            u.username LIKE '%$safe_search%' OR 
            u.email LIKE '%$safe_search%' OR 
            a.title LIKE '%$safe_search%'
        )";
    }

    $query .= " ORDER BY a.created_at DESC";
    $result = mysqli_query($conn, $query);

    $staffName = $user ? $user['first_name'] . ' ' . $user['last_name'] : 'Staff';
  ?>

 

  <!--Back to Staff Profile Button -->
<div class="d-flex justify-content-between align-items-center mb-3">
  <h4 class="mb-0"><?= $pageTitles[$status] ?? 'News' ?> by <?= htmlspecialchars($staffName) ?></h4>
  <a href="staff_detail.php?id=<?= $staff_id ?>" class="btn btn-outline-primary">
    <i class="fas fa-arrow-left me-1"></i> Back to Staff Profile
  </a>
</div>



  <!-- Filters -->
  <form method="GET" class="row g-3 align-items-end mb-4">
    <input type="hidden" name="status" value="<?= htmlspecialchars($status) ?>">
    <input type="hidden" name="id" value="<?= $staff_id ?>">
    <div class="col-md-3">
      <input type="text" name="search" class="form-control" placeholder="Search..." 
        value="<?= htmlspecialchars($search ?: $staffName) ?>">
    </div>
    <div class="col-md-3">
      <select name="category" class="form-select">
        <option value="">All Categories</option>
        <?php while ($cat = mysqli_fetch_assoc($categories)) : ?>
          <option value="<?= $cat['id'] ?>" <?= ($cat['id'] == $category) ? 'selected' : '' ?>>
            <?= htmlspecialchars($cat['name']) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>
    <div class="col-md-3">
      <input type="text" name="date" class="form-control" placeholder="Start Date - End Date" value="<?= htmlspecialchars($date) ?>">
    </div>
    <div class="col-md-3">
      <button type="submit" class="btn btn-primary w-100">Filter</button>
    </div>
  </form>

  <!-- Table -->
  <table class="table table-striped">
    <thead class="table-primary">
      <tr>
        <th>Date</th>
        <th>Title</th>
        <th>Category</th>
        <th>Views</th>
        <th>Read</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <td><?= date("M d, Y", strtotime($row['created_at'])) ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['category_name']) ?></td>
          <td><?= $row['view_count'] ?></td>
          <td>
          <a href="view_article.php?id=<?= $row['id'] ?>&from_staff=<?= $staff_id ?>" class="btn btn-sm btn-outline-primary">
          <i class="fas fa-eye"></i> View Article
          </a> 
      </td>
          <td>
            <span class="badge bg-<?= $row['status'] === 'published' ? 'success' : ($row['status'] === 'rejected' ? 'danger' : 'warning') ?>">
              <?= ucfirst($row['status']) ?>
            </span>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>

<?php else: ?>
  <?php
    $whereClause = "u.role != 'admin'";

    if ($filter === 'active') {
    $whereClause .= " AND u.status = 'active'";
} elseif ($filter === 'former') {
    $whereClause .= " AND u.status = 'inactive'";
}


    $query = "
      SELECT u.*, COUNT(a.id) AS article_count
      FROM users u
      LEFT JOIN articles a ON a.author_id = u.id
      WHERE $whereClause
      GROUP BY u.id
      ORDER BY u.created_at DESC
    ";
    $result = mysqli_query($conn, $query);
  ?>

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h4 class="mb-0">
      <?= $filter === 'former' ? 'Former Staff Members' : ($filter === 'all' ? 'All Staff Members' : 'Active Staff Members') ?>
    </h4>
    <?php if ($filter !== 'former'): ?>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">+ Add New Staff</button>
    <?php endif; ?>
  </div>

  <table class="table table-striped">
    <thead class="table-primary">
      <tr>
        <th>Name</th>
        <th>Username</th>
        <th>Email</th>
        <th>Joined At</th>
        <th>News</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <tr>
          <td><?= htmlspecialchars($row['first_name'] . ' ' . $row['last_name']) ?></td>
          <td><?= htmlspecialchars($row['username']) ?></td>
          <td><?= htmlspecialchars($row['email']) ?></td>
          <td><?= date("Y-m-d", strtotime($row['created_at'])) ?></td>
          <td><?= $row['article_count'] ?></td>
          <td>
            <a href="staff_detail.php?id=<?= $row['id'] ?>" class="btn btn-outline-primary btn-sm">Details</a>
            <?php if ($filter !== 'former'): ?>
              <form method="POST" action="delete_staff.php" class="d-inline" data-user="<?= $row['id'] ?>">
                <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                <button type="button" class="btn btn-outline-danger btn-sm btn-delete" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal" data-id="<?= $row['id'] ?>">Delete</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php endif; ?>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form method="POST" action="add_staff_handler.php">
        <div class="modal-header">
          <h5 class="modal-title" id="addStaffModalLabel">Add New Staff</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body row g-3">
          <?php if (!empty($addStaffError)): ?>
          <div class="alert alert-danger text-center">❌ <?= htmlspecialchars($addStaffError) ?></div>
          <?php endif; ?>

          <div class="col-md-6">
            <label class="form-label">First Name *</label>
            <input type="text" name="first_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Last Name *</label>
            <input type="text" name="last_name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email *</label>
            <input type="email" name="email" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Username *</label>
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Password*</label>
            <div class="input-group"> 
            <input type="password" name="password" class="form-control" id="password" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="password">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          </div>
          <div class="col-md-6">
             <label class="form-label">Confirm Password*</label>
            <div class="input-group">
            <input type="password" name="confirm_password" class="form-control" id="confirm_password" required>
            <button class="btn btn-outline-secondary toggle-password" type="button" data-target="confirm_password">
              <i class="fas fa-eye"></i>
            </button>
          </div>
          </div>
          <input type="hidden" name="status" value="1">
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary w-100">Submit</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap Confirm Delete Modal -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="delete_staff.php">
        <div class="modal-header">
          <h5 class="modal-title">Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this staff member?
        </div>
        <div class="modal-footer">
          <input type="hidden" name="delete_id" id="confirmDeleteId">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const deleteModal = document.getElementById("confirmDeleteModal");
  deleteModal.addEventListener("show.bs.modal", function (event) {
    const button = event.relatedTarget;
    const userId = button.getAttribute("data-id");
    document.getElementById("confirmDeleteId").value = userId;

  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector('#addStaffModal form');
  const passwordInput = form.querySelector('input[name="password"]');
  const confirmPasswordInput = form.querySelector('input[name="confirm_password"]');
  const emailInput = form.querySelector('input[name="email"]');
  const usernameInput = form.querySelector('input[name="username"]');

  form.addEventListener('submit', function (e) {
    e.preventDefault(); // Stop default form submission

    // Clear old alerts
    form.querySelectorAll('.input-alert').forEach(el => el.remove());

    const password = passwordInput.value.trim();
    const confirmPassword = confirmPasswordInput.value.trim();
    const email = emailInput.value.trim();
    const username = usernameInput.value.trim();

    let hasError = false;

    // Password match check
    if (password !== confirmPassword) {
      const alert = document.createElement('div');
      alert.className = 'alert alert-danger mt-3 input-alert';
      alert.textContent = '❌ Passwords do not match.';
      confirmPasswordInput.closest('.col-md-6').appendChild(alert);
      hasError = true;
    }

    // AJAX check for duplicate
    fetch('manage_staff.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: `check_duplicate=1&email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}`
    })
    .then(res => res.json())
    .then(data => {
      if (data.exists) {
        const alert = document.createElement('div');
        alert.className = 'alert alert-danger mt-3 input-alert';
        alert.textContent = '❌ Username or email already exists.';
        emailInput.closest('.col-md-6').appendChild(alert);
        hasError = true;
      }

      // Only submit if all is fine
      if (!hasError) {
        form.submit();
      }
    })
    .catch(err => {
      console.error('AJAX Error:', err);
    });
  });
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
  // Toggle password visibility
  document.querySelectorAll(".toggle-password").forEach(button => {
    button.addEventListener("click", function () {
      const targetId = this.getAttribute("data-target");
      const input = document.getElementById(targetId);
      const icon = this.querySelector("i");

      if (input.type === "password") {
        input.type = "text";
        icon.classList.remove("fa-eye");
        icon.classList.add("fa-eye-slash");
      } else {
        input.type = "password";
        icon.classList.remove("fa-eye-slash");
        icon.classList.add("fa-eye");
      }
    });
  });
});
</script>




</body>
</html>
