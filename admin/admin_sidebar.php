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
          <li><a href="manage_articles.php?status=pending">Pending News</a></li>
          <li><a href="manage_articles.php?status=published">Approved News</a></li>
          <li><a href="manage_articles.php?status=rejected">Rejected News</a></li>
          <li><a href="manage_articles.php?status=live">Live News</a></li>
        </ul>
      </li>

      <li class="dropdown">
  <a href="#"><i class="fas fa-users"></i> Manage Staff ▾</a>
  <ul class="submenu">
    <li><a href="manage_staff.php?filter=active">Active Staffs</a></li>
    <li><a href="manage_staff.php?filter=banned">Banned Staffs</a></li>
    <li><a href="manage_staff.php?filter=email_unverified">Email Unverified</a></li>
    <li><a href="manage_staff.php?filter=all">All Staffs</a></li>
    <li><a href="send_notification.php">Send Notification</a></li>
  </ul>
</li>

      <li class="dropdown">
        <a href="#"><i class="fas fa-ticket-alt"></i> Support Ticket ▾</a>
        <ul class="submenu">
          <li><a href="all_tickets.php">All Tickets</a></li>
          <li><a href="pending_tickets.php">Pending</a></li>
          <li><a href="closed_tickets.php">Closed</a></li>
        </ul>
      </li>

      <li><a href="report.php"><i class="fas fa-chart-line"></i> Report</a></li>
      <li><a href="settings.php"><i class="fas fa-cogs"></i> System Setting</a></li>
      <li><a href="extra.php"><i class="fas fa-puzzle-piece"></i> Extra</a></li>
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

