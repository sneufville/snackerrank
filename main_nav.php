<?php
require_once('db_connect.php');
require_once('auth_helpers.php');

if (session_status() != PHP_SESSION_ACTIVE) {
    session_start();
}

$current_user = $_SESSION['current_user'];
$user_role = $_SESSION['user_role'];

?>
<?php if (!is_null($current_user)): ?>
<ul id="userDropdown" class="dropdown-content">
  <?php if ($user_role == 'admin'): ?>
    <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
  <li class="divider"></li>
  <?php endif; ?>
  <li><a href="logout.php">Logout</a></li>
</ul>
<?php endif; ?>
<nav>
  <div class="nav-wrapper">
    <a class="brand-logo left" href="index.php">SnackerRank</a>
    <ul id="nav-mobile" class="right">
      <li><a href="snacks.php">All Snacks</a></li>
      <li><a href="search.php">Snack Search</a></li>
      <?php if (!is_null($current_user)): ?>
        <li><a href="#!" class="dropdown-trigger" data-target="userDropdown"><?= $current_user ?><i class="material-icons right">arrow_drop_down</i></a></li>
      <?php endif; ?>
    </ul>
  </div>
</nav>
