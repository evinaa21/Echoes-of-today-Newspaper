<!-- journalistNavBar.php -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
  .sidebar {
    width: 250px;
    background-color: #0f1c49;
    color: white;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    overflow-y: auto;
    scrollbar-width: thin;
    scrollbar-color: #5a5aff #0f1c49;
  }

  .sidebar::-webkit-scrollbar {
    width: 6px;
  }

  .sidebar::-webkit-scrollbar-track {
    background: #0f1c49;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background-color: #5a5aff;
    border-radius: 10px;
  }

  .nav-menu ul {
    list-style: none;
    margin: 0;
    padding: 0;
  }

  .nav-menu ul li a {
    display: block;
    color: white;
    text-decoration: none;
    padding: 12px 20px;
    transition: background 0.3s;
  }

  .nav-menu ul li a:hover {
    background-color: #1a2b6c;
  }

  .submenu {
    display: none;
    flex-direction: column;
    background-color: #1c2651;
  }

  .dropdown.open .submenu {
    display: flex;
  }

  .submenu li a {
    padding: 10px 30px;
    font-size: 14px;
  }

  .sidebar .version {
    text-align: center;
    padding: 15px;
    font-size: 12px;
    color: #00f0ff;
  }

  .logo {
    text-align: center;
    padding: 20px 10px 10px;
    border-bottom: 1px solid #1a2b6c;
  }

  .logo h2 {
    margin: 0;
    font-size: 22px;
    font-weight: bold;
  }

  .brand-primary {
    color: #00f0ff; /* Cyan */
  }

  .brand-middle {
    color: #5be8ff; /* Soft sky blue (between cyan and lavender) */
  }

  .brand-secondary {
    color: #b1b5ff; /* Lavender */
  }

  .welcome-text {
    margin-top: 5px;
    font-size: 13px;
    color: #ccc;
  }
</style>

<div class="sidebar">
  <div class="logo">
    <h2>
      <span class="brand-primary">Echoes</span>
      <span class="brand-middle">Of</span>
      <span class="brand-secondary">Today</span>
    </h2>
    <p class="welcome-text">Welcome, Journalist</p>
  </div>

  <nav class="nav-menu">
    <ul>
      <li><a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
      <li class="dropdown">
        <a href="#"><i class="fas fa-newspaper"></i> News ▾</a>
        <ul class="submenu">
          <li><a href="all_news.php">All News</a></li>
          <li><a href="pending_news.php">Pending News</a></li>
          <li><a href="approved_news.php">Approved News</a></li>
          <li><a href="rejected_news.php">Rejected News</a></li>
        </ul>
      </li>
      <li><a href="my_profile.php"><i class="fas fa-user"></i> My Profile</a></li>
      <li><a href="edit_password.php"><i class="fas fa-key"></i> Change Password</a></li>
      <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Log Out</a></li>
    </ul>
  </nav>

  <div class="version">EchoesOfToday <span class="text-success">V1.0</span></div>
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