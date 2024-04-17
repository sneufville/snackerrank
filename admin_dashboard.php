<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Plasticbrain\FlashMessages\FlashMessages;

require_once('auth_helpers.php');
require_once('search_helpers.php');
session_start();

admin_auth_guard();

$flash_msg = new FlashMessages();

//if (!array_key_exists('current_user', $_SESSION)) {
////  header("Location: index.php");
//    $flash_msg->error('You were not authorized to access this area', 'index.php');
//    exit;
//}
//
//if (!array_key_exists('user_role', $_SESSION)) {
//    header("Location: index.php");
//    exit;
//}
//
//if ($_SESSION['user_role'] != 'admin') {
//    header("Location: index.php");
//    exit;
//}

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
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">
    <?php if ($flash_msg->hasErrors()): ?>
      <?= $flash_msg->display(); ?>
    <?php endif; ?>
    <?php if ($flash_msg->hasMessages()): ?>
        <?= $flash_msg->display(); ?>
    <?php endif; ?>
  <?php require_once('partials/admin_nav.php') ?>
  <h1>SnackerRank Admin Dashboard</h1>
  <div>
    <div class="flexRow">
      <h2>Recently Added Snacks</h2>
      <a class="btn" href="snack_form.php">New Snack Entry</a>
    </div>
    <?php if (is_array($recently_added_snacks)): ?>
      <table class="striped">
          <thead>
          <tr>
            <th>Snack ID</th>
            <th>Snack Name</th>
            <th>Category</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($recently_added_snacks as $snack): ?>
          <tr>
            <td><?= $snack['id'] ?></td>
            <td><?= $snack['snack_name'] ?></td>
            <td><?= $snack['category_name'] ?></td>
            <td>
              <a href="edit_snack.php?snack_id=<?= $snack['id'] ?>">Edit Snack</a>
              |
              <a href="manage_snack_data.php?snack_id=<?= $snack['id'] ?>">Manage Related Snack Data</a>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
      </table>
      <?php else: ?>
        <p>No recently added snacks found</p>
      <?php endif; ?>
  </div>
  <div>
    <h2>Categories</h2>
    <a href="category_form.php">+ New Snack Category</a>
      <?php if (is_array($categories)): ?>
      <table class="striped">
          <thead>
          <tr>
            <th>Category Id</th>
            <th>Category Name</th>
            <th>Actions</th>
          </tr>
          </thead>
          <tbody>
          <?php foreach ($categories as $category): ?>
          <tr>
            <td><?=  $category['id'] ?></td>
            <td><?= $category['category_name'] ?></td>
            <td>
              <a href="edit_category.php?cat_id=<?= $category['id'] ?>">Edit Category</a>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
      </table>
      <?php else: ?>
        <p>No categories were found</p>
      <?php endif; ?>
  </div>
</div>
<?php require_once ('support/body_script.php') ?>
</body>
</html>
