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

// get the categories to build the options for the select field

//function get_categories(): ?array
//{
//    global $db;
//    $query_string = "SELECT id, category_name, category_description FROM snack_categories ORDER BY category_name";
//    $statement = $db->prepare($query_string);
//    $statement->execute();
//    return $statement->fetchAll();
//}

$categories = get_categories();
//print_r($categories);

$search_input = filter_input(INPUT_GET, 'search_input');
$selected_category = filter_input(INPUT_GET, 'snack_category');
$limit = filter_input(INPUT_GET, 'limit');

?>

<form action="search.php" class="searchForm" method="get">
  <div>
    <label for="searchInput"></label>
    <input id="searchInput" name="search_input" placeholder="Search" type="text" value="<?= $search_input ?>" />
  </div>
  <div>
    <label for="snackCategory">Snack Category</label>
    <select id="snackCategory" name="snack_category">
        <?php if (!is_null($categories)): ?>
            <?php foreach ($categories as $category): ?>
            <option selected="<?= $selected_category == $category['id'] ? 'selected' : '' ?>" value="<?= $category['id'] ?>"><?= $category['category_name'] ?></option>
            <?php endforeach; ?>
        <?php endif; ?>
    </select>
  </div>
  <button type="submit">Search</button>
</form>
