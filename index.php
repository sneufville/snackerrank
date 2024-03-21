<?php
/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Index / homepage of SnackerRank
 ****************/

session_start();
print_r($_SESSION);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank</title>
</head>
<body>
<h1>Welcome to SnackerRank</h1>
<small>The ultimate snack ranking website ever made</small>
<?php require_once('search_form.php') ?>
</body>
</html>
