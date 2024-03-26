<?php

require_once ('db_connect.php');
session_start();
if (!array_key_exists('current_user', $_SESSION)) {
    header("Location: index.php");
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

$category_name_error = null;
$category_description_error = null;

if ($_POST && !empty($_POST['category_name']) && !empty($_POST['category_description'])) {
  $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $category_description = filter_input(INPUT_POST, 'category_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  if (strlen($category_name) < 3 || strlen($category_description) < 10) {
    header("Location: category_form.php");
    exit;
  }

  global $db;
  $query_string = "INSERT INTO snack_categories (category_name, category_description) VALUES (:category_name, :category_description)";
  $statement = $db->prepare($query_string);
  $statement->bindValue(':category_name', $category_name);
  $statement->bindValue(':category_description', $category_description);
  if ($statement->execute()) {
    header('Location: admin_dashboard.php');
    exit;
  }
} else {
  $category_name = null;
  $category_description = null;
  $category_name_error = empty($_POST['category_name']) || strlen(trim($_POST['category_name'])) == 0 ? 'Must be at least 3 characters': null;
  $category_description_error = empty($_POST['category_description']) || strlen(trim($_POST['category_description'])) == 0 ? 'Must be at least 10 characters' : null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Snack Category</title>
</head>
<body>
<div>
  <a href="index.php">Home</a><br>
  <a href="admin_dashboard.php">Admin Dashboard</a><br>
  <a href="search.php">Snack Listing</a>
</div>
<form action="" method="post">
    <div class="formRow">
      <label for="category_name">Category Name</label><br>
      <input type="text" name="category_name" id="category_name" class="formInput" value="<?= $_POST['category_name'] ?? $category_name ?>">
      <?php if ($category_name_error): ?>
      <p class="formError"><?= $category_name_error ?></p>
      <?php endif; ?>
    </div>
  <div class="formRow">
    <label for="category_description">Category Description</label><br>
    <textarea name="category_description" id="category_description" cols="30" rows="10" class="formInput"><?= $_POST['category_description'] ?? $category_description ?></textarea>
      <?php if ($category_description_error): ?>
        <p class="formError"><?= $category_description_error ?></p>
      <?php endif; ?>
  </div>
  <div class="formRow">
    <button type="submit">Save</button>
  </div>
</form>
</body>
</html>
