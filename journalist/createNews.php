<?php
require_once '../includes/auth_journalist.php';
include('../includes/db_connection.php');

$journalist_id = $_SESSION['user_id'] ?? 2;
$journalist_id = intval($journalist_id);

// Fetch categories
$categories = mysqli_query($conn, "SELECT * FROM categories WHERE is_active = 1 ORDER BY name");

// Show success modal if redirected with ?success=1
$showSuccessModal = isset($_GET['success']) && $_GET['success'] == 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Create News</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="css/journalist_style.css" rel="stylesheet" />
    <style>
        .form-title {
            font-size: 35px;
            font-weight: 500;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: rgb(12, 22, 61);
            margin-bottom: 15px;
        }

        .form-card {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0, 0, 0, 0.05);
        }

        .form-label span {
            color: red;
        }

        .btn-back {
            background-color: #e0e0e0;
            color: #333;
        }

        .btn-back:hover {
            background-color: #d0d0d0;
        }

        .custom-submit-btn {
            background-color: rgb(0, 115, 255);
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 10px;
            transition: all 0.3s ease;
        }

        .custom-submit-btn:hover {
            background-color: rgb(0, 100, 220);
            box-shadow: 0 4px 12px rgba(0, 115, 255, 0.3);
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="d-flex">
        <?php include 'journalistNavBar.php'; ?>

        <div class="container-fluid p-0" style="margin-left: 250px;">
            <?php include 'header.php'; ?>

            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="form-title">Create News</h2>
                    <a href="allNews.php" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
                        <i class="fas fa-arrow-left me-2"></i>Back to All News
                    </a>
                </div>

                <div class="card form-card">
                    <form action="submitNews.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="author_id" value="<?= $journalist_id ?>">
                        <input type="hidden" name="status" value="pending_review">

                        <div class="mb-4">
                            <label class="form-label">Upload Image (900x500)</label>
                            <input type="file" class="form-control" name="featured_image" accept=".jpg,.jpeg,.png">
                            <small class="text-muted">Supported formats: .jpg, .jpeg, .png (optional)</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Category <span>*</span></label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select One</option>
                                <?php while ($cat = mysqli_fetch_assoc($categories)) { ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php } ?>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">News Title <span>*</span></label>
                            <input type="text" name="title" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Slug <span>*</span>
                                <a href="#" onclick="makeSlug()">↳ Make Slug</a>
                            </label>
                            <input type="text" id="slug" name="slug" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Tags</label>
                            <input type="text" name="tags" class="form-control">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Trending</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="trending" value="1" checked>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Must Read (Featured)</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="is_featured" value="1">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Add Video</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="has_video" id="hasVideo"
                                        value="1">
                                </div>
                            </div>
                        </div>

                        <div class="mb-3" id="youtubeLinkSection" style="display: none;">
                            <label class="form-label">YouTube Link</label>
                            <input type="url" name="youtube_link" class="form-control"
                                placeholder="https://www.youtube.com/embed/..." />
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Excerpt <span>*</span></label>
                            <textarea name="excerpt" class="form-control" rows="3" required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Content <span>*</span></label>
                            <textarea name="content" class="form-control" rows="6" required></textarea>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="custom-submit-btn w-100 py-2 fw-semibold shadow-sm">
                                Submit News
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
          <div class="modal-header bg-success text-white">
            <h5 class="modal-title" id="successModalLabel">News Submitted</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body text-center">
            ✅ Your news has been successfully submitted and is pending review.
          </div>
          <div class="modal-footer justify-content-center">
            <a href="allNews.php" class="btn btn-primary">Go to All News</a>
          </div>
        </div>
      </div>
    </div>

    <script>
    function makeSlug() {
        const title = document.querySelector('[name="title"]').value;
        const slugField = document.getElementById('slug');
        const slug = title.toLowerCase().trim().replace(/[^\w ]+/g, '').replace(/ +/g, '-');
        slugField.value = slug;
    }

    const hasVideoCheckbox = document.getElementById('hasVideo');
    const youtubeSection = document.getElementById('youtubeLinkSection');

    function toggleYouTubeInput() {
        youtubeSection.style.display = hasVideoCheckbox.checked ? 'block' : 'none';
    }

    toggleYouTubeInput();
    hasVideoCheckbox.addEventListener('change', toggleYouTubeInput);

    <?php if ($showSuccessModal): ?>
    window.addEventListener('DOMContentLoaded', function () {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
    });
    <?php endif; ?>
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
