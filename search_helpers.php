<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search helpers functions
 ****************/

require_once('db_connect.php');

function get_recently_added_snacks(): ?array
{
    global $db;

    $query_string = "SELECT s.id, s.category_id, s.snack_name, s.snack_description, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id ORDER BY s.last_updated DESC LIMIT 5";
    $statement = $db->prepare($query_string);
    $statement->execute();
    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

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

    if (!is_null($search_text) && !is_null($category_id)) {
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

function get_snack($snack_id): ?array
{
    global $db;
    $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, s.last_updated, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE s.id = :snack_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':snack_id', $snack_id);
    $statement->execute();
    $snack = $statement->fetch();
    return is_bool($snack) ? null : $snack;
}

function get_snacks_with_category($category_id): ?array
{
    global $db;
    $query_string = "SELECT s.id, s.snack_name, s.snack_description, s.category_id, s.last_updated, sc.category_name FROM snacks s INNER JOIN snackerrank.snack_categories sc on s.category_id = sc.id WHERE s.category_id = :category_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id);
    $statement->execute();
    return $statement->fetchAll();
}

function get_category($category_id): ?array
{
    global $db;
    $query_string = "SELECT id, category_name, category_description FROM snack_categories WHERE id = :category_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':category_id', $category_id);
    $statement->execute();
    $category = $statement->fetch();
    return is_bool($category) ? null : $category;
}

function get_comments($snack_id, $is_admin = false): ?array
{
    global $db;
    $where_condition = $is_admin ? "related_snack_id = :related_snack_id" : "related_snack_id = :related_snack_id AND approved = TRUE";
    $query_string = "SELECT id, commenter_name, commenter_email_address, comment_text, related_snack_id, approved, last_updated FROM snack_comments WHERE " . $where_condition . " ORDER BY last_updated DESC";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':related_snack_id', $snack_id);
    $statement->execute();
    return $statement->fetchAll();
}

function get_snack_images($snack_id): ?array
{
    global $db;
    $query_string = "SELECT id, image_path, image_title, related_snack_id, last_updated FROM snack_images WHERE related_snack_id = :related_snack_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':related_snack_id', $snack_id);
    $statement->execute();
    return $statement->fetchAll();
}

function get_comment_for_mod($snack_id, $comment_id): ?array
{
    global $db;
    $query_string = "SELECT id, commenter_name, commenter_email_address, comment_text, related_snack_id FROM snack_comments WHERE id = :comment_id AND related_snack_id = :related_snack_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':comment_id', $comment_id, PDO::PARAM_INT);
    $statement->bindValue(':related_snack_id', $snack_id, PDO::PARAM_INT);
    $statement->execute();
    $comment = $statement->fetch();
    return is_bool($comment) ? null : $comment;
}
