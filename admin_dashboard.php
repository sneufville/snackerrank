<?php

require_once (__DIR__ . '/vendor/autoload.php');
use Plasticbrain\FlashMessages\FlashMessages;

require_once ('auth_helpers.php');
require_once ('search_helpers.php');
session_start();

$flash_msg = new FlashMessages();

if (!array_key_exists('current_user', $_SESSION)) {
//  header("Location: index.php");
  $flash_msg->error('You were not authorized to access this area', 'index.php');
  exit;
}

if (!array_key_exists('user_role', $_SESSION)) {
  header("Location: index.php");
  exit;
}

if ($_SESSION['user_role'] != 'admin') {
  header("Location: index.php");
  exit;
}

// on load
//$has_admin = has_admin_session();
//
//if (!$has_admin) {
//  user_session_check();
//}

$recently_added_snacks = get_recently_added_snacks();
$categories = get_categories();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>
        Admin Dashboard
    </title>
</head>
<body>
  <a href="index.php">SnackerRank Home</a>
  <a href="logout.php">Logout</a>
  <h1>SnackerRank Admin Dashboard</h1>
  <div>
    <a href="snack_form.php">New Snack Entry</a>
  </div>
  <div>
    <h2>Recently Added Snacks</h2>
    <?php if (is_array($recently_added_snacks)): ?>
    <?php foreach($recently_added_snacks as $snack): ?>
        <div>
          <h3><?= $snack['snack_name'] ?></h3>
          <div>
            <a href="edit_snack.php?snack_id=<?= $snack['id'] ?>">Edit Snack</a>
            <a href="manage_snack_data.php?snack_id=<?= $snack['id'] ?>">Manage Related Snack Data</a>
          </div>
        </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p>No recently added snacks found</p>
    <?php endif; ?>
  </div>
  <div>
    <h2>Categories</h2>
    <a href="category_form.php">+ New Snack Category</a>
    <?php if (is_array($categories)): ?>
    <?php foreach($categories as $category): ?>
    <div>
      <h3><?= $category['category_name'] ?></h3>
      <div>
        <a href="edit_category.php?cat_id=<?= $category['id'] ?>">Edit Category</a>
      </div>
    </div>
    <?php endforeach; ?>
    <?php else: ?>
    <p>No categories were found</p>
    <?php endif; ?>
  </div>
</body>
</html>
