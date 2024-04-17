<?php

require_once ('search_helpers.php');
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


$category_id = filter_input(INPUT_GET, 'cat_id', FILTER_VALIDATE_INT);

if (!$category_id) {
    header("Location: admin_dashboard.php");
    exit;
}

$category = get_category($category_id);

if (!$category) {
    header("Location: admin_dashboard.php");
    exit;
}

$category_name = $category['category_name'];
$category_description = $category['category_description'];
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
    $query_string = "UPDATE snack_categories SET category_name = :category_name, category_description = :category_description WHERE id = :category_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_name', $category_name);
    $statement->bindValue(':category_description', $category_description);
    $statement->bindValue(':category_id', $category_id);
    if ($statement->execute()) {
        header("Location: admin_dashboard.php");
        exit;
    }
} else {
    $category_name_error = empty($_POST['category_name']) || strlen(trim($_POST['category_name'])) == 0 ? 'Must be at least 3 character': null;
    $category_description_error = empty($_POST['category_description']) || strlen(trim($_POST['category_description'])) == 0 ? 'Must be at least 1 character' : null;
}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Edit Snack Category</title>
        <?php require_once('support/head_includes.php') ?>
    </head>
    <body>
    <div class="container">
    <?php require_once('partials/admin_nav.php') ?>
    <form action="" method="post">
        <div class="formRow">
            <label for="category_id">Category Id</label><br>
            <input type="text" disabled id="category_id" value="<?= $category_id ?>">
        </div>
        <div class="input-field">
            <label for="category_name">Category Name</label><br>
            <input type="text" class="formInput" id="category_name" name="category_name" value="<?= $category['category_name'] ?>">
            <?php if ($_POST && !is_null($category_name_error)): ?>
              <p class="formError"><?= $category_name_error ?></p>
            <?php endif; ?>
        </div>
        <div class="input-field">
            <label for="category_description">Category Description</label><br>
            <textarea name="category_description" id="category_description" cols="30" rows="10"
                      class="formInput"><?= $category['category_description'] ?></textarea>
            <?php if ($_POST && !is_null($category_description_error)): ?>
              <p class="formError"><?= $category_description_error ?></p>
            <?php endif; ?>
        </div>
        <div class="formRow">
            <button class="btn" type="submit">Update Category</button>
        </div>
    </form>
    <br>
    <form action="delete_category.php" method="post">
        <input type="hidden" name="category_id" value="<?= $category_id ?>">
        <button class="btn red" type="submit">Delete Category</button>
    </form>
    </div>
    <?php require_once('support/body_script.php') ?>
    </body>
</html>
