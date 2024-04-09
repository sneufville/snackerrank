<?php

require_once __DIR__ . '/vendor/autoload.php';
use Plasticbrain\FlashMessages\FlashMessages;

require_once 'auth_helpers.php';
require_once 'db_connect.php';

session_start();
$flash_msg = new FlashMessages();

admin_auth_guard();

if ($_POST && !empty($_POST['user_id'])) {
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    if (!is_int($user_id)) {
        $flash_msg->error("Invalid user id given", "dashboard_list_users.php");
        exit;
    }

    // prevent user self-deletion 💀
    if ($user_id == $_SESSION['user_id']) {
        $flash_msg->error("Hey now, you can't delete your own account!", "dashboard_list_users.php");
        exit;
    }

    global $db;
    $query_string = "DELETE FROM users WHERE id = :user_id";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    if ($statement->rowCount() == 1) {
        $flash_msg->success("User account was removed successfully", "dashboard_list_users.php");
        exit;
    } else {
        $flash_msg->error("Something went wrong when trying to delete the user account", "dashboard_list_users.php");
    }
} else {
    $flash_msg->error("This operation is not allowed", "dashboard_list_users.php");
}
