<?php
require_once(__DIR__ . '/vendor/autoload.php');
require_once('db_connect.php');
require_once('search_helpers.php');

use Plasticbrain\FlashMessages\FlashMessages;

session_start();
$flash_msg = new FlashMessages();

$snack_id = !empty($_GET['snack_id'])
    ? filter_input(INPUT_GET, 'snack_id', FILTER_VALIDATE_INT)
    : null;;

$snack = get_snack($snack_id);

if (is_null($snack)) {
    $flash_msg->error('Snack not found', 'index.php');
    exit;
}

$item_images = get_snack_images($snack_id);
$comments = get_comments($snack_id);

$current_user = $_SESSION['current_user'] ?? null;
$current_user_id = $_SESSION['user_id'] ?? null;

$commenter_name_error = null;
$commenter_email_error = null;
$comment_text_error = null;

if ($_POST && !empty($_POST['commenter_name']) && !empty($_POST['commenter_email']) && !empty($_POST['comment_text'])) {
    $commenter_name = filter_input(INPUT_POST, 'commenter_name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $commenter_email = filter_input(INPUT_POST, 'commenter_email', FILTER_VALIDATE_EMAIL);
    $comment_text = filter_input(INPUT_POST, 'comment_text', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    $errors = [];
    if (strlen($commenter_name) < 3) {
        $commenter_name_error = 'At least 3 characters are required for a name';
    }

    if (!$commenter_email) {
        $commenter_email_error = 'A valid email address is required';
    }

    if (strlen($comment_text) < 10) {
        $comment_text_error = 'A comment should be at least 10 characters';
    }

    if (!is_null($commenter_name_error) || !is_null($commenter_email_error) || !is_null($comment_text_error)) {
        $flash_msg->error('There was a problem with submitting your comment', $_SERVER['HTTP_REFERER']);
//        exit;
    }

    // parse out the url
    $url_query = $_SERVER['QUERY_STRING'];
    parse_str($url_query, $parsed_data);
    if (is_null($parsed_data['snack_id'])) {
        $flash_msg->error("Snack not found", "index.php");
        exit;
    }

    $related_snack_id = $parsed_data['snack_id'];

    global $db;
    $query_string = "INSERT INTO snack_comments (commenter_name, commenter_email_address, comment_text, ip_address, related_snack_id, approved) VALUES (:commenter_name, :commenter_email_address, :comment_text, :ip_address, :related_snack_id, :approved)";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':commenter_name', $commenter_name);
    $statement->bindValue(':commenter_email_address', $commenter_email);
    $statement->bindValue(':comment_text', $comment_text);
    $statement->bindValue(':ip_address', $_SERVER['REMOTE_ADDR']);
    $statement->bindParam(':related_snack_id', $related_snack_id, PDO::PARAM_INT);
    $statement->bindValue(':approved', 1, PDO::PARAM_INT);

    if ($statement->execute()) {
        $flash_msg->success('Your comment was added!', $_SERVER['HTTP_REFERER']);
    } else {
        $flash_msg->error("An error occurred while trying to add your comment", $_SERVER['HTTP_REFERER']);
    }

} else {
    $comment_text = null;
    $commenter_email = null;
    $commenter_name = null;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Snack Detail - <?= $snack ? $snack['snack_name'] : '' ?></title>
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">
    <?php require('main_nav.php') ?>
    <?php if ($flash_msg->hasErrors()): ?>
      <p>Errors</p>
        <?= $flash_msg->display() ?>
    <?php endif; ?>
    <?php if ($flash_msg->hasMessages()): ?>
      <p>Notifications</p>
        <?= $flash_msg->display() ?>
    <?php endif; ?>
    <?php if (!is_null($snack)): ?>
      <div class="snackDetail">
        <h1><?= $snack['snack_name'] ?></h1>
        <div class="flow-text">
            <?= htmlspecialchars_decode($snack['snack_description']) ?>
        </div>
        <br>
        <span class="itemBadge"><i class="material-icons">label</i> <?= $snack['category_name'] ?></span>
      </div>
      <h3>Snack Images</h3>
      <?php if (count($item_images) > 0): ?>
      <?php else: ?>
        <div class="noResultsContainer noResultsContainer_Row">
          <i class="material-icons fitContent">info</i>
          <p class="text-flow">Looks like there are no images for this snack yet.</p>
        </div>
      <?php endif; ?>
      <div class="snackImagesContainer">

      </div>
    <?php else: ?>
      <div class="noResultContainer">
        <h1>Uh oh</h1>
        <p>We couldn't find the snack you were looking for</p>
        <a href="search.php">Back to Search</a>
      </div>
    <?php endif; ?>
  <div>
    <h3>Comments</h3>
    <p class="flow-text">Have something to say about <?= $snack['snack_name'] ?>? Share with others</p>
    <form action="" method="post">
      <input type="hidden" name="related_snack_id" value="<?= $snack_id ?>">
      <div class="formRow input-field">
        <label for="commenter_name">Your Name</label><br>
        <input type="text" name="commenter_name" id="commenter_name" placeholder="Ex. John Battman"
               value="<?= $_POST['commenter_name'] ?? $commenter_name ?>">
      </div>
      <div class="formRow input-field">
        <label for="commenter_email">Your Email</label><br>
        <input type="email" name="commenter_email" id="commenter_email" placeholder="john.battman@example.ca"
               value="<?= $_POST['commenter_email'] ?? $commenter_email ?>">
      </div>
      <div class="formRow input-field">
        <label for="comment_text">Your comment</label><br>
        <textarea
          name="comment_text" id="comment_text" cols="30" rows="5" class="formInput materialize-textarea"
          placeholder="Tell us what you think about this snack" data-length="500"><?= $_POST['comment_text'] ?? $comment_text ?></textarea>
          <?php if ($comment_text_error): ?>
            <p class="formError"><?= $comment_text_error ?></p>
          <?php endif; ?>
      </div>
      <div class="formRow">
        <button class="btn" type="submit">Add Comment</button>
        <button class="btn" type="reset">Clear</button>
      </div>
    </form>
    <hr>
      <?php if (count($comments) > 0): ?>
          <?php foreach ($comments as $comment): ?>
          <div class="comment">
            <blockquote class="flow-text"><?= $comment['comment_text'] ?></blockquote>
            <label><?= $comment['commenter_name'] ?> - <?= $comment['commenter_email_address'] ?></label>
            <label><?= $comment['last_updated'] ?></label>
            <hr>
          </div>
          <?php endforeach; ?>
      <?php else: ?>
        <div class="noResultContainer">
          <p>Looks like there haven't been any comments added yet.</p>
        </div>
      <?php endif; ?>
  </div>
</div>
<?php require_once('support/body_script.php') ?>
</body>
</html>
