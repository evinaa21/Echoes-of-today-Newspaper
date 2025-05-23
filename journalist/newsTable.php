<?php
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
          <?php if (isset($show_edit_column) && $show_edit_column): ?>
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
            <?php if (isset($show_edit_column) && $show_edit_column): ?>
              <td>
                <div class="d-flex justify-content-center gap-1 flex-wrap">
                  <a href="viewNews.php?id=<?= $article['id'] ?>"
                    class="btn btn-outline-primary btn-sm py-0 px-2 rounded-pill">
                    <i class="bi bi-eye-fill"></i>
                  </a>
                  <a href="editNews.php?id=<?= $row['id'] ?>" 
                    class="btn btn-outline-warning btn-sm py-0 px-2 rounded-pill text-dark">
                    <i class="bi bi-pencil-fill"></i>
                  </a>
                  <a href="delete_article.php?id=<?= $article['id'] ?>"
                    class="btn btn-outline-danger btn-sm py-0 px-2 rounded-pill"
                    onclick="return confirm('Are you sure you want to delete this article?');">
                    <i class="bi bi-trash3-fill"></i>
                  </a>
                </div>
              </td>
            <?php endif; ?>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>