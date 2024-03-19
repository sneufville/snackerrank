<?php

require_once('search_helpers.php');

$categories = get_categories();

?>

<div>
    <?php if (!is_null($categories)): ?>
    <ul>
        <li>
            <a href="search.php">All Snacks</a>
        </li>
        <?php foreach($categories as $category): ?>
        <li>
            <a href="search.php?snack_category=<?=$category['id'];?>">
                <?= $category['category_name']; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <strong>Something went horribly wrong while trying to retrieve categories. Sorry about that</strong>
    <?php endif; ?>
</div>
