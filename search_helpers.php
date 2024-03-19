<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search helpers functions
 ****************/

require_once('db_connect.php');

function exec_search(?string $search_text, ?int $category_id, int $limit = 10): ?array
{
    global $db;

    if ($limit < 5) $limit = 5;

    if (!is_null($search_text) && is_null($category_id)) {
        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id AND snack_name LIKE :search_text OR snack_description LIKE :search_text ORDER BY s.snack_name LIMIT :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $statement->bindValue(':search_text', '%'. $search_text . '%');
    } elseif (!is_null($search_text) && is_null($category_id)) {
        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE snack_name LIKE :search_text OR snack_description LIKE :search_text ORDER BY s.snack_name LIMIT :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':search_text', '%'. $search_text . '%');
    } elseif (!is_null($category_id) && is_null($search_text)) {
        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id ORDER BY s.snack_name LIMIT :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    } else {
        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id ORDER BY s.snack_name LIMIT :limit";
        $statement = $db->prepare($query_string);
    }

    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
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
