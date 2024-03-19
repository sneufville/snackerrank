<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search 'component'
 ****************/

require_once ('search_helpers.php');

// get variables
$search_text = filter_input(INPUT_GET, 'search_input', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$search_category = $_GET['snack_category'];

$search_results = exec_search($search_text, $search_category);
//print_r($search_results);
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Search Results</title>
    </head>
    <body>
    <?php if (!is_null($search_results)): ?>
    <?php foreach ($search_results as $result): ?>
        <div>
            <h2><?= $result['snack_name'] ?></h2>
            <div><?= $result['snack_description']; ?></div>
            <label><?= $result['category_name']; ?></label>
        </div>
    <?php endforeach; ?>
    <?php endif; ?>
    </body>
</html>
