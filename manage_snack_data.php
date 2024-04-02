<?php
require_once (__DIR__ . '/vendor/autoload.php');
require_once ('db_connect.php');
require_once ('search_helpers.php');

use Plasticbrain\FlashMessages\FlashMessages;

session_start();

$flash_msg = new FlashMessages();

if (!array_key_exists('current_user', $_SESSION)) {
//  header("Location: index.php");
    $flash_msg->error('You were not authorized to access this area', 'index.php');
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

$snack_id = filter_input(INPUT_GET, 'snack_id', FILTER_VALIDATE_INT);
$snack = get_snack($snack_id);

if (is_null($snack)) {
    $flash_msg->error('Snack not found', 'admin_dashboard.php');
}

$comments = get_comments($snack_id, true);
$related_images = get_snack_images($snack_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage Snack</title>
</head>
<body>
    <?php if($flash_msg->hasMessages()): ?>
    <?= $flash_msg->display() ?>
    <?php endif; ?>
    <?php if (!is_null($snack)): ?>
    <div>
      <h1><?= $snack['snack_name'] ?></h1>
      <div>
        <?= htmlspecialchars_decode($snack['snack_description']) ?>
      </div>
      <h2>Comments</h2>
      <?php if(count($comments) > 0): ?>
      <?php foreach($comments as $comment): ?>
          <div>
            <blockquote><?= $comment['comment_text'] ?></blockquote>
            <label>Comment Status: <?= $comment['approved'] ? 'Approved' : 'Unapproved (Hidden)' ?></label>
            <form action="moderate_comment.php" method="post">
              <input type="hidden" name="snack_id" value="<?= $comment['related_snack_id'] ?>">
              <input type="hidden" name="comment_id" value="<?= $comment['id'] ?>">
              <div class="formInline">
                <label for="comment_action">Comment Action</label>
                <select name="comment_action" id="comment_action" class="formInput">
                  <option value="">-- choose action --</option>
                  <?php if($comment['approved']): ?>
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
      <h2>Images</h2>
      <p>no images have been uploaded yet</p>
    </div>
    <?php else: ?>
    <?php endif; ?>
</body>
</html>
