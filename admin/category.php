<?php
session_start();
include('../includes/db_connection.php');
include('../admin/admin_sidebar.php');
include('../admin/admin_header.php');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/admin_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .main-content {
            margin-left: 255px;
            margin-top: 60px;
            padding: 30px;
            background-color: #f8f9fa;
            min-height: 100vh;
        }
    </style>
</head>

<body>

    <div class="main-content">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h4>All Categories</h4>
            <a href="add_category.php" class="btn btn-primary">+ Add Category</a>
        </div>

        <?php
        $query = "SELECT * FROM categories ORDER BY display_order ASC";
        $result = mysqli_query($conn, $query);
        ?>

        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['name']) ?></td>
                        <td><?= htmlspecialchars($row['description']) ?></td>
                        <td>
                            <span class="badge bg-<?= $row['is_active'] ? 'success' : 'secondary' ?>">
                                <?= $row['is_active'] ? 'Enabled' : 'Disabled' ?>
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn btn-outline-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    <i class="fas fa-ellipsis-v"></i> More
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="edit_category.php?id=<?= $row['id'] ?>"><i class="fas fa-pen"></i> Edit</a></li>
                                    <li>
                                        <a class="dropdown-item" href="category_status.php?id=<?= $row['id'] ?>&status=<?= $row['is_active'] ? '0' : '1' ?>">
                                            <i class="fas fa-eye<?= $row['is_active'] ? '-slash' : '' ?>"></i>
                                            <?= $row['is_active'] ? 'Disable' : 'Enable' ?>
                                        </a>
                                    </li>

                                    <!-- Trigger delete modal -->
                                    <a href="#" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteModal<?= $row['id'] ?>">
                                        <i class="fas fa-trash"></i> Delete
                                    </a>

                                    </li>

                                </ul>
                            </div>
                        </td>
                    </tr>
                    <!-- Delete Modal -->
                    <div class="modal fade" id="deleteModal<?= $row['id'] ?>" tabindex="-1" aria-labelledby="deleteModalLabel<?= $row['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title" id="deleteModalLabel<?= $row['id'] ?>">Confirm Delete</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    Are you sure you want to delete the category "<strong><?= htmlspecialchars($row['name']) ?></strong>"?
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                    <a href="delete_category.php?id=<?= $row['id'] ?>" class="btn btn-danger">Yes, Delete</a>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>