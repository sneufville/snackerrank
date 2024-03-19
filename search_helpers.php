<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search helpers functions
 ****************/

require_once('db_connect.php');

function exec_search(string $search_text, int $category_id): ?array
{
    global $db;
    $qs_part1 = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id";

    if (strlen($search_text) > 0 && !empty($category_id)) {
        $filter_condition = ' WHERE category_id = :category_id AND snack_name LIKE :search_text OR snack_description LIKE :search_text';
    } else {
        $filter_condition = ' WHERE category_id = :category_id OR snack_name LIKE :search_text OR snack_description LIKE :search_text';
    }
    $qs_sort = " ORDER BY s.snack_name";
    $query_string = $qs_part1 . $filter_condition . $qs_sort;
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    $statement->bindValue(':search_text', '%'. $search_text . '%');
    $statement->execute();
    return $statement->fetchAll();
}

function get_categories(): ?array
{
    global $db;
    $query_string = "SELECT id, category_name, category_description FROM snack_categories ORDER BY category_name";
    $statement = $db->prepare($query_string);
    $statement->execute();
    return $statement->fetchAll();
}
