<link rel="stylesheet" href="css/admin_style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="top-header">
 <div class="search-bar">
  <i class="fas fa-search"></i>
  <input type="text" placeholder="Search here...">
</div>


  <div class="header-icons">
    <a href="https://your-newspaper-site.com" target="_blank" title="Visit Website">
      <i class="fas fa-globe"></i>
    </a>
    <a href="notifications.php" title="Notifications">
      <i class="fas fa-bell"></i>
    </a>
    <a href="settings.php" title="Settings">
      <i class="fas fa-wrench"></i>
    </a>

    <div class="profile-dropdown" onclick="toggleDropdown()">
      <img src="assets/profile.jpg" alt="Profile" class="profile-img">
      <span class="username">admin</span>
      <i class="fas fa-chevron-down"></i>

      <!-- Dropdown menu -->
      <div id="dropdown-menu" class="dropdown-menu">
        <a href="admin_profile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="change_password.php"><i class="fas fa-key"></i> Password</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
      </div>
    </div>
  </div>
</div>
<script>
  function toggleDropdown() {
    const menu = document.getElementById('dropdown-menu');
    menu.style.display = (menu.style.display === 'block') ? 'none' : 'block';
  }

  // Optional: click outside to close dropdown
  window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdown-menu');
    const profile = document.querySelector('.profile-dropdown');
    if (!profile.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
</script>
