<?php
/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Index / homepage of SnackerRank
 ****************/
// autoload of 3rd-party dependencies
require_once(__DIR__ . '/vendor/autoload.php');

use Plasticbrain\FlashMessages\FlashMessages;

require_once('auth_helpers.php');

session_start();

$flash_msg = new FlashMessages();
$loggedIn = array_key_exists('current_user', $_SESSION);
$is_admin = has_admin_session();


?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank</title>
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">

    <?php if ($flash_msg->hasMessages()): ?>
        <?= $flash_msg->display(); ?>
    <?php endif; ?>
    <?php if ($flash_msg->hasErrors()): ?>
        <?= $flash_msg->display(); ?>
    <?php endif; ?>

<!--    --><?php //if ($loggedIn): ?>
<!--      <a href="logout.php">Logout</a>-->
<!--    --><?php //else: ?>
<!--      <a href="auth.php">Login</a>-->
<!--    --><?php //endif; ?>
    <?php if ($is_admin): ?>
      <a class="brand-logo" href="admin_dashboard.php">Admin Dashboard</a>
    <?php endif; ?>
    <?php require('main_nav.php') ?>
  <h1>Welcome to SnackerRank</h1>
  <small>The ultimate snack ranking website ever made</small>
  <hr>
    <?php require_once('category_nav.php') ?>
  <p>Use the search form below to search for snacks based on keywords and / or category</p>
    <?php require_once('search_form.php') ?>
  <h2>What is SnackerRank?</h2>
  <p>Only the most awesome snack ranking website ever created. If you ever wanted to find more information about the
    best snacks, this is the place</p>
  <hr>
  <h2>Most Popular Snacks</h2>
  <p>Coming soon!</p>
</div>
<?php require_once ('support/body_script.php') ?>
</body>
</html>
