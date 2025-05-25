<link rel="stylesheet" href="css/admin_style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="top-header d-flex justify-content-end align-items-center px-4" style="height: 60px;">
  <div class="header-icons d-flex align-items-center gap-4">
    <a href="http://localhost/Echoes-of-today-Newspaper/public/index.php" target="_blank" title="Visit Website">
      <i class="fas fa-globe"></i>
    </a>

    <div class="profile-dropdown position-relative" onclick="toggleDropdown()" style="cursor: pointer;">
      <img src="uploads/1747995328_adminPfp.png" alt="Profile" class="profile-img">
      <span class="username">admin</span>
      <i class="fas fa-chevron-down"></i>

      <!-- Dropdown menu -->
      <div id="dropdown-menu" class="dropdown-menu position-absolute" style="right: 0; display: none;">
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

  window.addEventListener('click', function(e) {
    const dropdown = document.getElementById('dropdown-menu');
    const profile = document.querySelector('.profile-dropdown');
    if (!profile.contains(e.target)) {
      dropdown.style.display = 'none';
    }
  });
</script>
