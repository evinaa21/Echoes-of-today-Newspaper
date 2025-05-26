<?php 
$show_edit_column = true; // ✅ Ensure Actions column is shown

if (!isset($articles_result) || !is_object($articles_result)) {
  echo "<div class='alert alert-danger'>⚠️ No article data available. \$articles_result is not defined or invalid.</div>";
  return;
}

if (mysqli_num_rows($articles_result) === 0) {
  echo "<div class='alert alert-warning text-center p-4'>No articles found.</div>";
  return;
}
?>

<div class="card shadow-sm rounded-4">
  <div class="card-header bg-primary text-white rounded-top-4">
    <strong>My News</strong>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light text-center">
        <tr>
          <th scope="col">Date</th>
          <th scope="col">Category</th>
          <th scope="col">Title</th>
          <th scope="col">Views</th>
          <th scope="col">Status</th>
          <th scope="col">Admin Check</th>
          <?php if ($show_edit_column): ?>
            <th scope="col">Actions</th>
          <?php endif; ?>
        </tr>
      </thead>
      <tbody>
        <?php while ($article = mysqli_fetch_assoc($articles_result)): ?>
          <tr class="text-center">
            <td><?= date("M d, Y", strtotime($article['created_at'])) ?></td>
            <td><?= htmlspecialchars($article['category_name']) ?></td>
            <td class="text-start"><?= htmlspecialchars($article['title']) ?></td>
            <td><?= intval($article['view_count']) ?></td>
            <td>
              <?php
              switch ($article['status']) {
                case 'published':
                  echo '<span class="badge bg-success">Published</span>';
                  break;
                case 'pending_review':
                  echo '<span class="badge bg-warning text-dark">Pending</span>';
                  break;
                case 'rejected':
                  echo '<span class="badge bg-danger">Rejected</span>';
                  break;
                default:
                  echo '<span class="badge bg-secondary">Draft</span>';
              }
              ?>
            </td>
            <td>
              <?php
              switch ($article['status']) {
                case 'published':
                  echo '<span class="badge bg-success">Approved</span>';
                  break;
                case 'pending_review':
                  echo '<span class="badge bg-warning text-dark">Pending</span>';
                  break;
                case 'rejected':
                  echo '<span class="badge bg-danger">Rejected</span>';
                  break;
                default:
                  echo '<span class="badge bg-secondary">Unknown</span>';
              }
              ?>
            </td>
            <?php if ($show_edit_column): ?>
              <td>
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                  <a href="viewNews.php?id=<?= $article['id'] ?>" class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill">
                    <i class="bi bi-eye-fill"></i>
                  </a>

                  <?php if (in_array($article['status'], ['pending_review', 'rejected'])): ?>
                    <a href="editNews.php?id=<?= $article['id'] ?>" 
                      class="btn btn-outline-warning btn-sm py-0 px-2 rounded-pill text-dark">
                      <i class="bi bi-pencil-fill"></i>
                      <?= $article['status'] === 'rejected' ? 'Edit & Resubmit' : '' ?>
                    </a>

                    <button type="button"
                      class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill"
                      data-bs-toggle="modal"
                      data-bs-target="#deleteModal"
                      onclick="setDeleteArticleId(<?= $article['id'] ?>)">
                      <i class="bi bi-trash3-fill"></i>
                    </button>
                  <?php endif; ?>
                </div>
              </td>
            <?php endif; ?>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-danger">
      <form method="POST" action="deleteNews.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteModalLabel">⚠️ Confirm Deletion</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          Are you sure you want to delete this article? This action cannot be undone.
        </div>
        <div class="modal-footer">
          <input type="hidden" name="delete_id" id="delete_article_id">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-danger">Yes, Delete</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Bootstrap JS (make sure it's included) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function setDeleteArticleId(id) {
  document.getElementById('delete_article_id').value = id;
}
</script>
