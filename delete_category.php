<?php

require_once ('db_connect.php');
session_start();
if (!array_key_exists('current_user', $_SESSION)) {
    header("Location: index.php");
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

if ($_POST && !empty($_POST['category_id'])) {
    $category_id = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
    if (is_null($category_id)) {
        header("Location: admin_dashboard.php");
        exit;
    }

    global $db;
    $query_string = "DELETE FROM snack_categories WHERE id = :category_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id);
    $statement->execute();
    if ($statement->rowCount() == 1) {
        header("Location: admin_dashboard.php");
        exit;
    }
} else {
    header("Location: admin_dashboard.php");
    exit;
}
