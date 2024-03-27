<?php
require_once('db_connect.php');
require_once ('search_helpers.php');
session_start();

$snack_id = !empty($_GET['snack_id'])
    ? filter_input(INPUT_GET, 'snack_id', FILTER_VALIDATE_INT)
    : null;;

$snack = get_snack($snack_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Snack Detail - <?= $snack ? $snack['snack_name'] : '' ?></title>
</head>
<body>
<div>
    <a href="index.php">SnackerRank Home</a><br>
    <a href="search.php">Snack Search</a>
</div>
<?php if (!is_null($snack)): ?>
    <div>
        <h1><?= $snack['snack_name'] ?></h1>
        <div>
            <?= htmlspecialchars_decode($snack['snack_description']) ?>
        </div>
        <br>
        <label>Category: <?= $snack['category_name'] ?></label>
    </div>
<?php else: ?>
    <div class="noResultContainer">
        <h1>Uh oh</h1>
        <p>We couldn't find the snack you were looking for</p>
        <a href="search.php">Back to Search</a>
    </div>
<?php endif; ?>
</body>
</html>
