<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once('db_connect.php');
require_once('search_helpers.php');
require_once('image_helpers.php');
require_once('auth_helpers.php');

use Plasticbrain\FlashMessages\FlashMessages;

session_start();

$flash_msg = new FlashMessages();

admin_auth_guard();

$snack_id = filter_input(INPUT_GET, 'snack_id', FILTER_VALIDATE_INT);
$snack = get_snack($snack_id);

if (is_null($snack)) {
    $flash_msg->error('Snack not found', 'admin_dashboard.php');
}

$comments = get_comments($snack_id, true);
$related_images = get_snack_images($snack_id);
$upload_error = null;
$upload_result = "";

if (isset($_FILES['image']) && $_FILES['image']['error'] > 0) {
    $upload_error = $_FILES['image']['error'];
    $flash_msg->error("Error uploading your image: {$upload_error}");
} elseif (isset($_FILES['image']) && ($_FILES['image']['error'] == 0)) {
    $image_file = $_FILES['image']['name'];
    $temp_image_path = $_FILES['image']['tmp_name'];
    $destination_path = build_upload_path($image_file);

    if (is_image_file($temp_image_path, $destination_path)) {
        $upload_success = move_uploaded_file($temp_image_path, $destination_path);
        if ($upload_success) {
            // write to database
            global $db;
            $query_string = "INSERT INTO snack_images (image_path, image_title, related_snack_id) VALUES (:image_path, :image_title, :related_snack_id)";
            $statement = $db->prepare($query_string);
            $statement->bindValue(':image_path', $destination_path);
            $statement->bindValue(':image_title', '');
            $statement->bindValue(':related_snack_id', $snack_id, PDO::PARAM_INT);
            if ($statement->execute()) {
                $flash_msg->success("Image uploaded and linked");
            } else {
                $flash_msg->error("Failed to upload image and link to snack");
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Manage Snack</title>
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">
    <?php if ($flash_msg->hasErrors()): ?>
        <?= $flash_msg->display(); ?>
    <?php endif; ?>
    <?php if ($flash_msg->hasMessages()): ?>
        <?= $flash_msg->display() ?>
    <?php endif; ?>
    <?php require_once('partials/admin_nav.php') ?>
    <?php if (!is_null($snack)): ?>
      <div>
        <h1><?= $snack['snack_name'] ?></h1>
        <div>
            <?= htmlspecialchars_decode($snack['snack_description']) ?>
        </div>
        <h2>Comments</h2>
          <?php if (count($comments) > 0): ?>
              <?php foreach ($comments as $comment): ?>
              <div>
                <blockquote><?= $comment['comment_text'] ?></blockquote>
                <label class="itemBadge">Comment
                  Status: <?= $comment['approved'] ? 'Approved' : 'Unapproved (Hidden)' ?></label>
                <form action="moderate_comment.php" method="post">
                  <input type="hidden" name="snack_id" value="<?= $comment['related_snack_id'] ?>">
                  <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
                  <div class="row">
                    <label for="comment_action">Comment Action</label>
                    <select name="comment_action" id="comment_action" class="browser-default">
                      <option value="">-- choose action --</option>
                        <?php if ($comment['approved']): ?>
                          <option value="unapprove">Un-Approve</option>
                        <?php else: ?>
                          <option value="approve">Approve</option>
                        <?php endif; ?>
                      <option value="remove">Remove</option>
                    </select>
                  </div>
                  <button type="submit">Moderate Comment</button>
                </form>
              </div>
              <?php endforeach; ?>
          <?php else: ?>
            <p>No comments to moderate yet</p>
          <?php endif; ?>
        <hr>
        <h2>Images</h2>
          <?php if (count($related_images) > 0): ?>
              <?php foreach ($related_images as $image): ?>
              <div>
                <img alt="snack image" src="<?= $image['image_path'] ?>">
              </div>
              <?php endforeach; ?>
          <?php else: ?>
            <div class="noResultsContainer noResultsContainer_Row">
              <i class="material-icons fitContent">info</i>
              <p class="text-flow">Looks like there are no images for this snack yet.</p>
            </div>
          <?php endif; ?>
        <form action="" method="post" enctype="multipart/form-data">
          <div class="row">
            <div class="browser-default">
              <label for="snack_image">Image to Upload</label>
              <input type="file" name="image" id="snack_image" accept="image/*">
            </div>
          </div>
          <div class="row">
            <button class="btn" type="submit">Upload Image</button>
          </div>
        </form>
      </div>
    <?php else: ?>
    <?php endif; ?>
</div>
<?php require_once('support/body_script.php') ?>
</body>
</html>
