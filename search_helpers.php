<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search helpers functions
 ****************/

require_once('db_connect.php');

/**
 * Function that executes the search function and returns either an associative array with data or null
 *
 * @param string|null $search_text
 * @param int|null $category_id
 * @param int|null $limit
 * @param int|null $page
 * @return array|null
 */
function exec_search(?string $search_text, ?int $category_id, ?int $limit, ?int $page): ?array
{
    global $db;

    if (is_null($limit) || $limit < 5) $limit = 5;
    $start = 0;

    if (!is_null($page) && $page > 0)  $start = ($page - 1) * $limit;

    if (!is_null($search_text) && is_null($category_id)) {
        $count_query = "SELECT count(*) as total_results FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id AND snack_name LIKE :search_text OR snack_description LIKE :search_text";
        $count_statement = $db->prepare($count_query);
        $count_statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $count_statement->bindValue(':search_text', '%' . $search_text . '%');

        // query for results
        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id AND snack_name LIKE :search_text OR snack_description LIKE :search_text ORDER BY s.snack_name LIMIT :start, :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
        $statement->bindValue(':search_text', '%' . $search_text . '%');
    } elseif (!is_null($search_text) && is_null($category_id)) {
        $count_query = "SELECT count(*) as total_results FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE snack_name LIKE :search_text OR snack_description LIKE :search_text";
        $count_statement = $db->prepare($count_query);
        $count_statement->bindValue(':search_text', '%' . $search_text . '%');

        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE snack_name LIKE :search_text OR snack_description LIKE :search_text ORDER BY s.snack_name LIMIT :start, :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':search_text', '%' . $search_text . '%');
    } elseif (!is_null($category_id) && is_null($search_text)) {
        $count_query = "SELECT count(*) as total_results FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id";
        $count_statement = $db->prepare($count_query);
        $count_statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);

        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE category_id = :category_id ORDER BY s.snack_name LIMIT :start, :limit";
        $statement = $db->prepare($query_string);
        $statement->bindValue(':category_id', $category_id, PDO::PARAM_INT);
    } else {
        $count_query = "SELECT count(*) as total_results FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id";
        $count_statement = $db->prepare($count_query);

        $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id ORDER BY s.snack_name LIMIT :start, :limit";
        $statement = $db->prepare($query_string);
    }

    $count_statement->execute();
    $result_count = $count_statement->fetchAll()[0]['total_results'];
    $num_pages = ceil($result_count/$limit);

    $statement->bindValue(':start', $start, PDO::PARAM_INT);
    $statement->bindValue(':limit', $limit, PDO::PARAM_INT);
    $statement->execute();

    return [
        "data" => $statement->fetchAll(),
        "next_page" => $page < $num_pages ? $page + 1 : null,
        "page_count" => $num_pages,
        "prev_page" => ($page - 1) >= 1 ? $page - 1 : null,
        "total_results" => $result_count,
        "limit" => $limit
    ];
}

/**
 * Helper function that retrieves the list of categories from the database
 * @return array|null
 */
function get_categories(): ?array
{
    global $db;
    $query_string = "SELECT id, category_name, category_description FROM snack_categories ORDER BY category_name";
    $statement = $db->prepare($query_string);
    $statement->execute();
    return $statement->fetchAll();
}
