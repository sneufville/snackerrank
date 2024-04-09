<?php

require_once(__DIR__ . '/vendor/autoload.php');
use Plasticbrain\FlashMessages\FlashMessages;

require_once('db_connect.php');
require_once('auth_helpers.php');

session_start();
$flash_msg = new FlashMessages();

admin_auth_guard();

$username_error = null;
$password_error = null;
$role_error = null;

if ($_POST && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['role'])) {
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = trim($_POST['password']);
    $role = filter_input(INPUT_POST, 'role', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

    if (strlen($password) < 6) {
        $flash_msg->error("Password requirements not met", "dashboard_add_user.php");
        exit;
    }
} else {

}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add SnackerRank User</title>
    </head>
    <body>
        <?php if ($flash_msg->hasErrors()): ?>
        <?= $flash_msg->display(); ?>
        <?php endif; ?>
        <div>
            <form action="" method="post">
                <div class="formRow">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username">
                </div>
                <div class="formRow">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password">
                </div>
                <div class="formRow">
                    <label for="role">Role</label>
                    <select name="role" id="role">
                        <option value="">Select A Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="formRow">
                    <button type="submit">Add User</button>
                </div>
            </form>
        </div>
    </body>
</html>
