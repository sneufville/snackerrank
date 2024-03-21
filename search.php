<?php

/*******w********
 *
 * Name: Simon Neufville
 * Date: March 18, 2024,
 * Description: Search 'component'
 ****************/

require_once('search_helpers.php');
session_start();

// get variables
$search_text = filter_input(INPUT_GET, 'search_input', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$search_category = filter_input(INPUT_GET, 'search_category', FILTER_VALIDATE_INT);
if ($search_category == 0) $search_category = null;
$limit = filter_input(INPUT_GET, 'result_limit', FILTER_VALIDATE_INT);
$page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT);

$search_results = exec_search($search_text, $search_category, $limit, $page);

/**
 * Builds a url for pagination based on a given page number and the results returned
 * @param int $page_number
 * @return string
 */
function build_pagination_url(int $page_number): string
{
    $query_string = $_SERVER['QUERY_STRING'];

    parse_str($query_string, $parsed_data);
    $parsed_data["page"] = $page_number;

    return $_SERVER['PHP_SELF'] . '?' . http_build_query($parsed_data);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Search Results</title>
</head>
<body>
<?php require('category_nav.php') ?>
<?php require('search_form.php') ?>
<?php if (!is_null($search_results)): ?>
    <?php foreach ($search_results['data'] as $result): ?>
    <div>
      <h2>
        <a href="snack_detail.php?snack_id=<?= $result['id'] ?>"><?= $result['snack_name'] ?></a>
      </h2>
      <div><?= $result['snack_description']; ?></div>
      <label><i><?= $result['category_name']; ?></i></label>
    </div>
    <?php endforeach; ?>
<?php endif; ?>
<div>
  <?php if ($search_results['total_results'] > $limit): ?>
  <ul>
      <?php if (!is_null($search_results['prev_page'])): ?>
      <li>
        <a href="<?= build_pagination_url($search_results['prev_page']) ?>">Prev</a>
      </li>
      <?php endif; ?>
      <?php for ($i = 0; $i < $search_results['page_count']; $i++): ?>
        <li>
          <a href="<?= build_pagination_url($i + 1) ?>">Page <?= $i + 1 ?></a>
        </li>
      <?php endfor; ?>
    <?php if (!is_null($search_results['next_page'])): ?>
    <li>
      <a href="<?= build_pagination_url($search_results['next_page']) ?>">Next</a>
    </li>
    <?php endif; ?>
  </ul>
  <?php endif; ?>
</div>
</body>
</html>
