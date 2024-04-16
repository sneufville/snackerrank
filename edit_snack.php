<?php

require_once(__DIR__ . '/vendor/autoload.php');
require_once('db_connect.php');
require_once('search_helpers.php');
require_once('auth_helpers.php');

session_start();

$flash_msg = new \Plasticbrain\FlashMessages\FlashMessages();

admin_auth_guard();

//if (!array_key_exists('current_user', $_SESSION)) {
//    $flash_msg->error('You were not authorized to access this area', 'index.php');
////    header("Location: index.php");
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

$categories = get_categories();

$snack_id = filter_input(INPUT_GET, 'snack_id', FILTER_VALIDATE_INT);

if (!$snack_id) {
    header("Location: admin_dashboard.php");
    exit;
}

$snack = get_snack($snack_id);

if (!$snack) {
    header("Location: admin_dashboard.php");
    exit;
}

$category_id = $snack['category_id'];
$snack_name = $snack['snack_name'];
$snack_description = $snack['snack_description'];
$snack_name_error = null;
$snack_description_error = null;

if ($_POST && !empty($_POST['category_id']) && !empty($_POST['snack_name']) && !empty($_POST['snack_description'])) {
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    $snack_name = filter_input(INPUT_POST, 'snack_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $snack_description = filter_input(INPUT_POST, 'snack_description', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (strlen($snack_name) == 0 || strlen($snack_description) == 0) {
        // redirect to self - the error text should show
        header("Location: edit_snack.php");
        exit;
    }

    global $db;
    $query_string = "UPDATE snacks SET category_id = :category_id, snack_name = :snack_name, snack_description = :snack_description WHERE id = :snack_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $statement->bindValue(':snack_name', $snack_name);
    $statement->bindValue(':snack_description', $snack_description);
    $statement->bindValue(':snack_id', $snack_id);
    if ($statement->execute()) {
        header('Location: admin_dashboard.php');
        exit;
    }
} else {
//    $snack_name = null;
//    $snack_description = null;
    $snack_name_error = empty($_POST['snack_name']) || strlen(trim($_POST['snack_name'])) == 0 ? 'Must be at least 1 character' : null;
    $snack_description_error = empty($_POST['snack_description']) || strlen(trim($_POST['snack_description'])) == 0 ? 'Must be at least 1 character' : null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Snack</title>
    <?php require_once('support/head_includes.php') ?>
  <script language="javascript" type="text/javascript" src="vendor/tinymce/tinymce/tinymce.min.js"></script>
  <script language="javascript" type="text/javascript">

    tinymce.init({
      mode: "exact",
      license_key: "gpl",
      selector: "#snack_description",
      theme_advanced_toolbar_location: "top",
      theme_advanced_buttons1: "bold,italic,underline,strikethrough,separator,"
        + "justifyleft,justifycenter,justifyright,justifyfull,formatselect,"
        + "bullist,numlist,outdent,indent",
      theme_advanced_buttons2: "link,unlink,anchor,image,separator,"
        + "undo,redo,cleanup,code,separator,sub,sup,charmap",
      theme_advanced_buttons3: "",
      height: "350px",
      width: "600px",
    });

  </script>
</head>
<body>
<div class="container">
  <?php require_once('partials/admin_nav.php') ?>
  <form action="" method="post">
    <div class="input-field">
      <label for="category_id">Category *</label><br>
      <select name="category_id" id="category_id" class="browser-default">
        <option disabled>-- Choose A Category --</option>
          <?php if (!is_null($categories)): ?>
              <?php foreach ($categories as $category): ?>
              <option <?= $category_id == $category['id'] ? 'selected' : '' ?>
                value="<?= $category['id']; ?>"><?= $category['category_name'] ?></option>
              <?php endforeach; ?>
          <?php endif; ?>
      </select>
    </div>
    <div class="input-field">
      <label for="snack_name">Snack Name</label><br>
      <input type="text" name="snack_name" id="snack_name" class="formInput"
             value="<?= $_POST['snack_name'] ?? $snack_name ?>">
        <?php if ($_POST && !is_null($snack_name_error)): ?>
          <p class="formError"><?= $snack_name_error ?></p>
        <?php endif; ?>
    </div>
    <div class="input-field">
      <label for="snack_description">Snack Description *</label><br>
      <textarea name="snack_description" id="snack_description" cols="30" rows="10"
                class="formInput"><?= $_POST['snack_description'] ?? $snack_description ?></textarea>
        <?php if ($_POST && !is_null($snack_description_error)): ?>
          <p class="formError"><?= $snack_description_error ?></p>
        <?php endif; ?>
    </div>
    <div class="formRow">
      <button type="submit">Update Snack Entry</button>
    </div>
  </form>
  <br>
  <form action="delete_snack.php" method="post">
    <input type="hidden" name="snack_id" value="<?= $snack_id ?>">
    <button type="submit">Delete Snack</button>
  </form>
</div>
<?php require_once('support/body_script.php') ?>
</body>
</html>
