<?php
use Plasticbrain\FlashMessages\FlashMessages;

require_once ('db_connect.php');
require_once ('auth_helpers.php');
session_start();
admin_auth_guard();

$flash_msg = new FlashMessages();

$category_name_error = null;
$category_description_error = null;

if ($_POST && !empty($_POST['category_name']) && !empty($_POST['category_description'])) {
  $category_name = filter_input(INPUT_POST, 'category_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
  $category_description = filter_input(INPUT_POST, 'category_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

  if (strlen($category_name) < 3 || strlen($category_description) < 10) {
//    header("Location: category_form.php");
    $flash_msg->error("There were problems with your submission", "category_form.php");
    exit;
  }

  global $db;
  $query_string = "INSERT INTO snack_categories (category_name, category_description) VALUES (:category_name, :category_description)";
  $statement = $db->prepare($query_string);
  $statement->bindValue(':category_name', $category_name);
  $statement->bindValue(':category_description', $category_description);
  if ($statement->execute()) {
    $flash_msg->success("Category created successfully", "admin_dashboard.php");
    exit;
  }
} else {
  $category_name = null;
  $category_description = null;
//  $category_name_error = empty($_POST['category_name']) || strlen(trim($_POST['category_name'])) == 0 ? 'Must be at least 3 characters': null;
//  $category_description_error = empty($_POST['category_description']) || strlen(trim($_POST['category_description'])) == 0 ? 'Must be at least 10 characters' : null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Snack Category</title>
  <?php require_once ('support/head_includes.php')?>
</head>
<body>
<div class="container">
<?php require_once('partials/admin_nav.php') ?>
<form action="" method="post">
    <div class="input-field">
      <label for="category_name">Category Name</label><br>
      <input type="text" name="category_name" id="category_name" class="formInput" value="<?= $_POST['category_name'] ?? $category_name ?>">
      <?php if ($category_name_error): ?>
      <p class="formError"><?= $category_name_error ?></p>
      <?php endif; ?>
    </div>
  <div class="input-field">
    <label for="category_description">Category Description</label><br>
    <textarea name="category_description" id="category_description" cols="30" rows="10" class="formInput materialize-textarea"><?= $_POST['category_description'] ?? $category_description ?></textarea>
      <?php if ($category_description_error): ?>
        <p class="formError"><?= $category_description_error ?></p>
      <?php endif; ?>
  </div>
  <div class="formRow">
    <button class="btn" type="submit">Save</button>
  </div>
</form>
</div>
<?php require_once ('support/body_script.php')?>
</body>
</html>
