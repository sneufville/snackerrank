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

require_once ('db_connect.php');
require_once('auth_helpers.php');
require_once('helpers/format_helpers.php');

session_start();

$flash_msg = new FlashMessages();
$loggedIn = array_key_exists('current_user', $_SESSION);
$is_admin = has_admin_session();

global $db;
$query_string = "SELECT s.id, category_id, snack_name, snack_description, s.last_updated, category_name FROM snacks s INNER JOIN snack_categories sc ON s.category_id = sc.id ORDER BY s.last_updated LIMIT 3";
$statement = $db->prepare($query_string);
$statement->execute();
$recently_updated_snacks = $statement->fetchAll();

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
  <h2>Most Recently Updated Snacks</h2>
  <?php if(is_array($recently_updated_snacks)): ?>
  <div class="row">
  <?php foreach($recently_updated_snacks as $snack): ?>
      <div class="col s4">
        <h2 class="itemTitle">
          <a href="snack_detail.php?snack_id=<?= $snack['id'] ?>"><?= $snack['snack_name'] ?></a>
        </h2>
        <p>Last Updated: <?= format_timestamp($snack['last_updated']) ?></p>
        <span class="itemBadge"><i class="material-icons">label</i> <?= $snack['category_name']; ?></span>
      </div>
  <?php endforeach; ?>
  </div>
  <?php else: ?>
  <div class="noResultsContainer">
    <p>Nothing to see here yet...</p>
  </div>
  <?php endif; ?>
</div>
<?php require_once ('support/body_script.php') ?>
</body>
</html>
