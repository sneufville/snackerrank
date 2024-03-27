<?php

require_once ('auth_helpers.php');
require_once ('search_helpers.php');
require_once('db_connect.php');

session_start();

if (!has_admin_session()) {
    user_session_check();
}

$allowedTags='<p><strong><em><u><h1><h2><h3><h4><h5><h6>';
$allowedTags.='<li><ol><ul><span><div><br><ins><del>';

$categories = get_categories();

$snack_name_error = null;
$snack_description_error = null;
$last_category_id = null;

if ($_POST && !empty($_POST['category_id']) && !empty($_POST['snack_name']) && !empty($_POST['snack_description'])) {
    global $db;
    $selected_category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $snack_name = filter_input(INPUT_POST, 'snack_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $snack_description = filter_input(INPUT_POST, 'snack_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $last_category_id = $selected_category_id;

    if(strlen($snack_name) == 0 || strlen($snack_description) == 0) {
        // redirect to self - the error text should show
        header("Location: snack_form.php");
        exit;
    }

    // build query for insert
    $query_string = "INSERT INTO `snacks` (category_id, snack_name, snack_description) VALUES (:category_id, :snack_name, :snack_description)";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $selected_category_id, PDO::PARAM_INT);
    $statement->bindValue(':snack_name', $snack_name);
    $statement->bindValue(':snack_description', $snack_description);
    if ($statement->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    }

} else {
    $snack_name = null;
    $snack_description = null;
    $snack_name_error = empty($_POST['snack_name']) || strlen(trim($_POST['snack_name'])) == 0 ? 'Must be at least 1 character': null;
    $snack_description_error = empty($_POST['snack_description']) || strlen(trim($_POST['snack_description'])) == 0 ? 'Must be at least 1 character' : null;
}
//print_r($_SERVER);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>SnackerRank - Add Snack</title>
        <script language="javascript" type="text/javascript" src="vendor/tinymce/tinymce/tinymce.min.js"></script>
        <script language="javascript" type="text/javascript">

          tinymce.init({
            mode: "exact",
            selector : "#snack_description",
            theme_advanced_toolbar_location : "top",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,separator,"
              + "justifyleft,justifycenter,justifyright,justifyfull,formatselect,"
              + "bullist,numlist,outdent,indent",
            theme_advanced_buttons2 : "link,unlink,anchor,image,separator,"
              +"undo,redo,cleanup,code,separator,sub,sup,charmap",
            theme_advanced_buttons3 : "",
            height:"350px",
            width:"600px"
          });

        </script>
    </head>
    <body>
    <div>
      <a href="index.php">Home</a><br>
      <a href="admin_dashboard.php">Admin Dashboard</a><br>
      <a href="search.php">Snack Listing</a>
    </div>
    <div>
      <h1>Add a New Snack Entry</h1>
      <small>Use the form below to add a tasty snack entry</small>
    </div>
    <form action="" method="post">
        <div class="formRow">
            <label for="category_id">Category *</label><br>
            <select name="category_id" id="category_id" class="formInput">
                <option disabled>-- Choose A Category --</option>
                <?php if (!is_null($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <option <?= $_POST && intval($_POST['category_id']) == $category['id'] ? 'selected' : '' ?> value="<?= $category['id']; ?>"><?= $category['category_name'] ?></option>
                <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
        <div class="formRow">
            <label for="snack_name">Snack Name</label><br>
            <input type="text" name="snack_name" id="snack_name" class="formInput" value="<?= $_POST['snack_name'] ?? $snack_name ?>">
            <?php if ($_POST && !is_null($snack_name_error)): ?>
            <p class="formError"><?= $snack_name_error ?></p>
            <?php endif; ?>
        </div>
        <div class="formRow">
            <label for="snack_description">Snack Description *</label><br>
            <textarea name="snack_description" id="snack_description" cols="30" rows="10" class="formInput"><?= $_POST['snack_description'] ?? $snack_description ?></textarea>
            <?php if ($_POST && !is_null($snack_description_error)): ?>
                <p class="formError"><?= $snack_description_error ?></p>
            <?php endif; ?>
        </div>
        <div class="formRow">
            <button type="submit">Add Snack Entry</button>
        </div>
    </form>
    </body>
</html>
