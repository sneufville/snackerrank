<?php
require_once('db_connect.php');
require_once('search_helpers.php');

session_start();

$categories = get_categories();

global $db;

$category_id = filter_input(INPUT_GET, 'snack_category', FILTER_VALIDATE_INT);

$query_string = "SELECT s.id, category_id, snack_name, snack_description, s.last_updated FROM snacks s INNER JOIN snack_categories sc ON s.category_id = sc.id";

if (is_int($category_id)) {
    $query_string .= " WHERE category_id = :category_id ORDER BY snack_name";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id);
} else {
    $query_string .= " ORDER BY snack_name";
    $statement = $db->prepare($query_string);
}

$statement->execute();
$snacks = $statement->fetchAll();
$active_category = null;

foreach($categories as $category) {
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
    </head>
    <body>
    <?php require('main_nav.php') ?>
    <?php require('category_nav.php') ?>
    <div>
        <?php if(!is_null($active_category)): ?>
            <p>Showing <strong><?= $active_category['category_name'] ?></strong> snacks</p>
        <?php else: ?>
            <p>Showing <strong>All</strong> Snacks</p>
        <?php endif; ?>
        <?php if(count($snacks) > 0): ?>
        <?php foreach($snacks as $snack): ?>
            <div class="snack">
                <h2>
                    <a href="snack_detail.php?snack_id=<?= $snack['id'] ?>"><?= $snack['snack_name'] ?></a>
                </h2>
            </div>
        <?php endforeach; ?>
        <?php else: ?>
        <div class="noResultsContainer">
            <p>No Snacks were found</p>
        </div>
        <?php endif; ?>
    </div>
    </body>
</html>
