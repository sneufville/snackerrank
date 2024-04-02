<?php

require_once (__DIR__ . '/vendor/autoload.php');
use Plasticbrain\FlashMessages\FlashMessages;

require_once ('auth_helpers.php');
require_once ('search_helpers.php');
session_start();

$flash_msg = new FlashMessages();

if (!array_key_exists('current_user', $_SESSION)) {
//  header("Location: index.php");
    $flash_msg->error('You were not authorized to access this area', 'index.php');
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

const ALLOWED_COMMENT_ACTIONS = ['approve', 'unapprove', 'remove'];

// redirect on get
if ($_POST && !empty($_POST['snack_id']) && !empty($_POST['comment_id']) && !empty($_POST['comment_action'])) {
    $snack_id = filter_input(INPUT_POST, 'snack_id', FILTER_VALIDATE_INT);
    $comment_id = filter_input(INPUT_POST, 'comment_id', FILTER_VALIDATE_INT);
    $comment_action = filter_input(INPUT_POST, 'comment_action', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (!in_array($comment_action, ALLOWED_COMMENT_ACTIONS) ) {
        $flash_msg->error('Invalid action', 'admin_dashboard.php');
        exit;
    }
    global $db;

    if ($comment_action == 'approve') {
        $query_string = "UPDATE snack_comments SET approved = TRUE WHERE id = :comment_id AND related_snack_id = :related_snack_id";
    } elseif ($comment_action == 'unapprove') {
        $query_string = "UPDATE snack_comments SET approved = FALSE WHERE id = :comment_id AND related_snack_id = :related_snack_id";
    } else {
        // fall back to delete
        $query_string = "DELETE FROM snack_comments WHERE id = :comment_id AND related_snack_id = :related_snack_id";
    }

    $statement = $db->prepare($query_string);
    $statement->bindValue(':comment_id', $comment_id);
    $statement->bindValue(':related_snack_id', $snack_id);
    $statement->execute();
    $rows_affected = $statement->rowCount();

    if ($rows_affected == 0) {
        $flash_msg->error('Failed to moderate comment due to an error', "manage_snack_data.php?snack_id={$snack_id}");
        exit;
    }

    $flash_msg->success('Comment moderated', "manage_snack_data.php?snack_id={$snack_id}");
} else {
    header('Location: admin_dashboard.php');
}
exit;
