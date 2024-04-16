<?php
if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}
$current_user = $_SESSION['current_user'] ?? null;
$user_role = $_SESSION['user_role'] ?? null;
?>

<?php if ($user_role == 'admin'): ?>
<ul id="userDropdown" class="dropdown-content">
  <li><a href="index.php">SnackerRank Home</a></li>
  <li class="divider"></li>
  <li><a href="logout.php">Logout</a></li>
</ul>
<nav>
  <div class="nav-wrapper">
    <a href="admin_dashboard.php" class="brand-logo left">SnackerRank Admin</a>
    <ul class="right">
      <li><a href="">Categories</a></li>
      <li><a href="dashboard_list_users.php">Users</a></li>
      <li><a href="#!" class="dropdown-trigger"  data-target="userDropdown"><?= $current_user ?><i class="material-icons right">arrow_drop_down</i></a></li>
    </ul>
  </div>
</nav>
<?php endif; ?>
