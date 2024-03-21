<?php
require_once ('auth_helpers.php');
session_start();

// on load
$has_admin = has_admin_session();

if (!$has_admin) {
  user_session_check();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Admin Dashboard
    </title>
</head>
<body>
<a href="logout.php">Logout</a>
  <h1>SnackerRank Admin Dashboard</h1>
  <div>
    <a href="snack_form.php">New Snack Entry</a>
  </div>
</body>
</html>
