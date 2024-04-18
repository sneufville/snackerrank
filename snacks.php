<?php
require_once (__DIR__ . '/vendor/autoload.php');
use Plasticbrain\FlashMessages\FlashMessages;
require_once('db_connect.php');
require_once('search_helpers.php');

session_start();
$flash_msg = new FlashMessages();

$categories = get_categories();
$current_user = $_SESSION['current_user'] ?? null;
const VALID_SORT_OPTIONS = ['category_id', 'snack_name', 'last_updated'];
const VALID_SORT_DIRECTIONS = ['asc', 'desc'];

global $db;

$category_id = filter_input(INPUT_GET, 'snack_category', FILTER_VALIDATE_INT);
$sort_by = filter_input(INPUT_GET, 'sort_by', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$sort_direction = filter_input(INPUT_GET, 'sort_dir', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$query_string = "SELECT s.id, category_id, snack_name, snack_description, s.last_updated, category_name FROM snacks s INNER JOIN snack_categories sc ON s.category_id = sc.id";

if (is_int($category_id)) {
    if (!is_null($current_user)) {
      // check the sort by field and direction
        if (!in_array($sort_by, VALID_SORT_OPTIONS)) {
//          $flash_msg->error("Invalid sort by option given", "snacks.php");
            $sort_by = 'snack_name';
        }
        if (!in_array($sort_direction, VALID_SORT_DIRECTIONS)) {
          $sort_direction = 'asc';
        }
        $query_string .= " WHERE category_id = :category_id ORDER BY $sort_by $sort_direction";
        //        $statement->bindParam(':sort_field', $sort_by);
//        $statement->bindParam(':sort_direction', $sort_direction);
    } else {
        $query_string .= " WHERE category_id = :category_id";
    }
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id);
} else {
    if (!is_null($current_user)) {
        // check the sort by field and direction
        if (!in_array($sort_by, VALID_SORT_OPTIONS)) {
            $sort_by = 'snack_name';
        }
        if (!in_array($sort_direction, VALID_SORT_DIRECTIONS)) {
            $sort_direction = 'asc';
        }
        // we are only allowing specific values
        $query_string .= " ORDER BY $sort_by $sort_direction";
    }
    $statement = $db->prepare($query_string);
}

$statement->execute();
$snacks = $statement->fetchAll();
$active_category = ['id' => null, 'category_name' => null];

foreach ($categories as $category) {
    if ($category['id'] == $category_id) {
        $active_category = $category;
        break;
    }
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Snacks</title>
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">
    <?php require('main_nav.php') ?>
  <?php if($flash_msg->hasErrors()): ?>
  <?= $flash_msg->display() ?>
  <?php endif; ?>
    <?php if($flash_msg->hasMessages()): ?>
        <?= $flash_msg->display() ?>
    <?php endif; ?>
  <div>
    <form action="" id="snack_filter_form" method="get">
      <label for="category_id">Snack Category</label>
      <select class="browser-default" name="snack_category" id="category_id">
        <option value="">All Snacks</option>
        <?php if (!is_null($categories)): ?>
        <?php foreach($categories as $category): ?>
          <option <?= $active_category['id'] == $category['id'] ? 'selected' : '' ?> value="<?= $category['id'] ?>"><?= $category['category_name'] ?></option>
        <?php endforeach; ?>
        <?php endif; ?>
      </select>
      <br>
        <?php if(!is_null($current_user)): ?>
          <label for="sort_by">Sort By</label>
          <select name="sort_by" id="sort_by" class="browser-default">
            <option value="" disabled>--</option>
            <option value="snack_name" <?= !empty($_GET['sort_by']) && $_GET['sort_by'] == 'snack_name' ? 'selected' : '' ?> >Snack Name</option>
            <option value="category_id" <?= !empty($_GET['sort_by']) && $_GET['sort_by'] == 'category_id' ? 'selected' : '' ?>>Snack Category</option>
            <option value="last_updated" <?= !empty($_GET['sort_by']) && $_GET['sort_by'] == 'last_updated' ? 'selected' : '' ?>>Last Updated</option>
          </select>
          <label for="sort_dir">Sorting Order</label>
          <select name="sort_dir" id="sort_dir" class="browser-default">
            <option value="asc"  <?= !empty($_GET['sort_dir']) && $_GET['sort_dir'] == 'asc' ? 'selected' : '' ?>>Ascending Order</option>
            <option value="desc" <?= !empty($_GET['sort_dir']) && $_GET['sort_dir'] == 'desc' ? 'selected' : '' ?>>Descending Order</option>
          </select>
          <button type="submit">Apply Sort & Filter</button>
        <?php endif; ?>
    </form>
      <?php if (count($snacks) > 0): ?>
          <?php foreach ($snacks as $snack): ?>
          <div class="card-panel">
            <h2 class="itemTitle">
              <a href="snack_detail.php?snack_id=<?= $snack['id'] ?>"><?= $snack['snack_name'] ?></a>
            </h2>
            <div class="flow-text"><?= htmlspecialchars_decode($snack['snack_description']); ?></div>
            <span class="itemBadge"><i class="material-icons">label</i> <?= $snack['category_name']; ?></span>
          </div>
          <?php endforeach; ?>
      <?php else: ?>
        <div class="noResultsContainer">
          <p>No Snacks were found</p>
        </div>
      <?php endif; ?>
  </div>
</div>
<?php require_once('support/body_script.php') ?>
<script>
  const snackFilterForm = document.querySelector('#snack_filter_form');
  const categorySelect = document.querySelector('#category_id');
  categorySelect.addEventListener('change', event => {
    console.log('event: ', event.target.value);
    snackFilterForm.submit();
  })
</script>
</body>
</html>
