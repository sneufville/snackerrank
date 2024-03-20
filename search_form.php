<?php
/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search 'component'
 ****************/

// imports
require_once('db_connect.php');
require_once('search_helpers.php');

$page_limits = [5, 10, 15];

// get the categories to build the options for the select field

$categories = get_categories();

$search_input = filter_input(INPUT_GET, 'search_input');
$selected_category = filter_input(INPUT_GET, 'snack_category', FILTER_VALIDATE_INT);
$limit = filter_input(INPUT_GET, 'result_limit', FILTER_VALIDATE_INT);

?>

<form action="search.php" class="searchForm" method="get">
  <div>
    <label for="searchInput"></label>
    <input id="searchInput" name="search_input" placeholder="Search" type="text" value="<?= $search_input ?>" />
  </div>
  <div>
    <label for="snackCategory">Snack Category</label>
    <select id="snackCategory" name="snack_category">
        <option value="">-- All --</option>
        <?php if (!is_null($categories)): ?>
            <?php foreach ($categories as $category): ?>
            <option <?= intval($selected_category) == $category['id'] ? 'selected' : '' ?> value="<?= $category['id'] ?>">
                <?= $category['category_name'] ?>
            </option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
  </div>
  <div>
    <label for="resultLimit">Results per page</label>
    <select name="result_limit" id="resultLimit">
      <?php foreach($page_limits as $page_limit): ?>
      <option <?= intval($limit) == $page_limit ? 'selected' : '' ?> value="<?= $page_limit ?>"><?= $page_limit ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit">Search</button>
</form>
