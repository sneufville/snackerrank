<?php

require_once('search_helpers.php');

$categories = get_categories();

?>

<div>
  <p>Snack Search Quick Category Links</p>
    <?php if (!is_null($categories)): ?>
    <ul>
        <li>
            <a href="<?= $_SERVER['PHP_SELF'] ?>">All Snacks</a>
        </li>
        <?php foreach($categories as $category): ?>
        <li>
            <a href="?snack_category=<?=$category['id'];?>">
                <?= $category['category_name']; ?>
            </a>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php else: ?>
    <strong>Something went horribly wrong while trying to retrieve categories. Sorry about that</strong>
    <?php endif; ?>
</div>
