<!-- header.php -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet" />

<style>
  .top-header {
    background-color: #0f1c49;
    color: white;
    padding: 10px 20px;
    display: flex;
    justify-content: flex-end;
    align-items: center;
    position: sticky;
    top: 0;
    z-index: 1001;
  }

  .user-info {
    display: flex;
    align-items: center;
    gap: 20px;
  }

  .username {
    font-size: 14px;
    color: #e0e0e0;
  }

  .user-info i {
    font-size: 18px;
    color: #5be8ff;
    cursor: pointer;
  }

  .current-datetime {
    font-size: 14px;
    color: #a0c7ff;
  }

  @media (max-width: 768px) {
    .top-header {
      flex-direction: column;
      align-items: flex-end;
      gap: 10px;
    }

    .user-info {
      flex-direction: column;
      align-items: flex-end;
      gap: 5px;
    }
  }
</style>

<div class="top-header">
  <!-- Right Side: Date + Icons -->
  <div class="user-info">
    <span class="current-datetime" id="currentDateTime"></span>
    <a href="../public/index.php" title="Visit Website" target="_blank"><i class="bi bi-globe"></i></a>
    <a href="profile.php" title="My Profile"><i class="bi bi-person-circle"></i></a>
    <a href="../logout.php" title="Log Out"><i class="bi bi-box-arrow-right"></i></a>
  </div>
</div>

<!-- Live Date/Time Script -->
<script>
  function updateDateTime() {
    const now = new Date();
    const options = {
      weekday: 'short',
      year: 'numeric',
      month: 'short',
      day: 'numeric',
      hour: '2-digit',
      minute: '2-digit'
    };
    document.getElementById('currentDateTime').innerText = now.toLocaleString('en-US', options);
  }

  updateDateTime();
  setInterval(updateDateTime, 60000); // Update every minute
</script>
