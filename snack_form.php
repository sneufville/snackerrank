<?php
require_once(__DIR__ . '/vendor/autoload.php');

use Plasticbrain\FlashMessages\FlashMessages;

require_once('auth_helpers.php');
require_once('search_helpers.php');
require_once('db_connect.php');
require_once('image_helpers.php');

session_start();

$flash_msg = new FlashMessages();

admin_auth_guard();

$allowedTags = '<p><strong><em><u><h1><h2><h3><h4><h5><h6>';
$allowedTags .= '<li><ol><ul><span><div><br><ins><del>';

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

    if (strlen($snack_name) == 0 || strlen($snack_description) == 0) {
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
        $last_insert_id = $db->lastInsertId();
        // check the file to see if we can upload it or nah
        if (isset($_FILES['image'])) {
            // on error, gracefully fail with a warning to the user
            if ($_FILES['image']['error'] > 0) {
                $flash_msg->warning("Warning: Your image was not uploaded for snack: {$snack_name}");
            } else {
                $image_file = $_FILES['image']['name'];
                $temp_image_path = $_FILES['image']['tmp_name'];
                $destination_path = build_upload_path($image_file);
                if (is_image_file($temp_image_path, $destination_path)) {
                    $upload_success = move_uploaded_file($temp_image_path, $destination_path);
                    $image_query_string = "INSERT INTO snack_images (image_path, image_title, related_snack_id) VALUES (:image_path, :image_title, :related_snack_id)";
                    $img_statement = $db->prepare($image_query_string);
                    $img_statement->bindValue(':image_path', "public_images/$image_file");
                    $img_statement->bindValue(':image_title', "image for $snack_name");
                    $img_statement->bindValue(':related_snack_id', $last_insert_id, PDO::PARAM_INT);
                    if ($img_statement->execute()) {
                      $flash_msg->success("Snack added with image successfully", "admin_dashboard.php");
                      exit;
                    } else {
                      $flash_msg->warning("Snack was saved but there was a problem with your image upload", "admin_dashboard.php");
                    }
                } else {
                  $flash_msg->warning("Warning: You attempted to upload an unsupported image. Ignoring");
                }
            }
        } else {
//            header('Location: admin_dashboard.php');
            $flash_msg->success("Snack added successfully", "admin_dashboard.php");
            exit;
        }
    }

} else {
    $snack_name = null;
    $snack_description = null;
    $snack_name_error = empty($_POST['snack_name']) || strlen(trim($_POST['snack_name'])) == 0 ? 'Must be at least 1 character' : null;
    $snack_description_error = empty($_POST['snack_description']) || strlen(trim($_POST['snack_description'])) == 0 ? 'Must be at least 1 character' : null;
}
//print_r($_SERVER);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>SnackerRank - Add Snack</title>
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
  <div>
    <h1>Add a New Snack Entry</h1>
    <small>Use the form below to add a tasty snack entry</small>
  </div>
  <form action="" method="post" enctype="multipart/form-data">
    <div class="row">
      <label for="image">Upload Snack Image</label>
      <input type="file" name="image" id="image" accept="image/*">
    </div>
    <div class="row">
      <label for="category_id">Category *</label><br>
      <select name="category_id" id="category_id" class="browser-default">
        <option disabled>-- Choose A Category --</option>
          <?php if (!is_null($categories)): ?>
              <?php foreach ($categories as $category): ?>
              <option <?= $_POST && intval($_POST['category_id']) == $category['id'] ? 'selected' : '' ?>
                value="<?= $category['id']; ?>"><?= $category['category_name'] ?></option>
              <?php endforeach; ?>
          <?php endif; ?>
      </select>
    </div>
    <div class="input-field">
      <label for="snack_name">Snack Name</label><br>
      <input type="text" placeholder="Type something..." name="snack_name" id="snack_name" class="formInput"
             value="<?= $_POST['snack_name'] ?? $snack_name ?>">
        <?php if ($_POST && !is_null($snack_name_error)): ?>
          <p class="formError"><?= $snack_name_error ?></p>
        <?php endif; ?>
    </div>
    <div class="formRow">
      <label for="snack_description">Snack Description *</label><br>
      <textarea name="snack_description" id="snack_description" cols="30" rows="10"
                class="formInput"><?= $_POST['snack_description'] ?? $snack_description ?></textarea>
        <?php if ($_POST && !is_null($snack_description_error)): ?>
          <p class="formError"><?= $snack_description_error ?></p>
        <?php endif; ?>
    </div>
    <div class="row">
      <button class="btn-large" type="submit">Add Snack Entry</button>
    </div>
  </form>
</div>
<?php require_once('support/body_script.php') ?>
</body>
</html>
