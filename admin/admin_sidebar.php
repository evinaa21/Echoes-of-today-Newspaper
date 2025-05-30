<div class="sidebar">
  <div class="logo">
    <link rel="stylesheet" href="css/admin_style.css">
    <div class="logo">
  <h2><span class="brand-primary">Echo</span><span class="brand-secondary">Today</span></h2>
  <p class="welcome-text">Welcome, Admin</p>
</div>
  </div>

  <nav class="nav-menu">
    <ul>
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li><a href="category.php"><i class="fas fa-layer-group"></i> Category</a></li>
      <li><a href="advertisement.php"><i class="fas fa-ad"></i> Advertisement</a></li>

      <li class="dropdown">
        <a href=""><i class="fas fa-newspaper"></i> Manage News ▾</a>
        <ul class="submenu">
          <li><a href="manage_articles.php?status=all">All News</a></li>
          <li><a href="manage_articles.php?status=pending_review">Pending News</a></li>
          <li><a href="manage_articles.php?status=published">Approved News</a></li>
          <li><a href="manage_articles.php?status=rejected">Rejected News</a></li>
        </ul>
      </li>

      <li class="dropdown">
  <a href="#"><i class="fas fa-users"></i> Manage Staff ▾</a>
  <ul class="submenu">
    <li><a href="manage_staff.php?filter=active">Active Staffs</a></li>
    <li><a href="manage_staff.php?filter=former">Former Staff</a></li>
  </ul>
</li>

      <li><a href="report.php"><i class="fas fa-chart-line"></i> Report</a></li>
      <li><a href="../logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
  </nav>

  <div class="version">EchoToday <span class="text-success">V1.0</span></div>
</div>

<script>
  document.querySelectorAll('.dropdown > a').forEach(link => {
    link.addEventListener('click', function (e) {
      e.preventDefault();
      document.querySelectorAll('.dropdown').forEach(drop => {
        if (drop !== this.parentElement) {
          drop.classList.remove('open');
        }
      });
      this.parentElement.classList.toggle('open');
    });
  });
</script>


