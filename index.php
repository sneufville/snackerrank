<?php
/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Index / homepage of SnackerRank
 ****************/

session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank</title>
</head>
<body>
<a href="auth.php">Login</a>
<h1>Welcome to SnackerRank</h1>
<small>The ultimate snack ranking website ever made</small>
<hr>
<?php require_once ('category_nav.php')?>
<p>Use the search form below to search for snacks based on keywords and / or category</p>
<?php require_once('search_form.php') ?>
<h2>What is SnackerRank?</h2>
<p>Only the most awesome snack ranking website ever created. If you ever wanted to find more information about the best snacks, this is the place</p>
<hr>
<h2>Most Popular Snacks</h2>
<p>Coming soon!</p>
</body>
</html>
