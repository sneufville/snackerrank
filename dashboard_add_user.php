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

    $submission_errors = [];

    if (strlen($username) < 3 || strlen($username) > 100) {
      $submission_errors[] = "Username length requirements not met. Must be between 3 and 100 characters";
    }

    if (strlen($password) < 6) {
      $submission_errors[] = "Password requirements not met. Must be at least 6 characters";
    }
    if (!in_array($role, ["user", "admin"])) {
      $submission_errors[] = "An invalid role was given";
    }

    if (count($submission_errors) > 0) {

      foreach ($submission_errors as $error) {
        $flash_msg->error($error);
      }
      header('Location: dashboard_add_user.php');
      exit;
    }

    $hashed_pw = password_hash($password, PASSWORD_BCRYPT);
    global $db;
    $query_string = "INSERT INTO users (username, password, role) VALUES (:username, :hashed_password, :role)";
    $statement = $db->prepare($query_string);
    $statement->bindValue(':username', $username);
    $statement->bindValue(':hashed_password', $hashed_pw);
    $statement->bindValue(':role', $role);
    if ($statement->execute()) {
      $flash_msg->success("User account created: {$username}", "dashboard_list_users.php");
    } else {
      $flash_msg->error("An error occurred while trying to add a user. Please contact the admin", "dashboard_add_user.php");
    }

} elseif ($_POST) {
  // post only
    $flash_msg->error("The required information for adding a user was not submitted", "dashboard_add_user.php");
} else {

}

?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Add SnackerRank User</title>
        <?php require_once('support/head_includes.php') ?>
    </head>
    <body>
        <div class="container">
        <?php require_once('partials/admin_nav.php') ?>
        <?php if ($flash_msg->hasMessages()): ?>
        <?= $flash_msg->display(); ?>
        <?php endif; ?>
        <?php if ($flash_msg->hasErrors()): ?>
        <?= $flash_msg->display(); ?>
        <?php endif; ?>
            <h2>Add A User</h2>
            <form action="" method="post">
                <div class="formRow">
                    <label for="username">Username</label><br>
                    <input type="text" name="username" id="username">
                </div>
                <div class="formRow">
                    <label for="password">Password</label><br>
                    <input type="password" name="password" id="password">
                </div>
                <div class="formRow">
                    <label for="role">Role</label><br>
                    <select class="browser-default" name="role" id="role">
                        <option value="">Select A Role</option>
                        <option value="user">User</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <br>
                <div class="formRow">
                    <button class="btn" type="submit">Add User</button>
                </div>
            </form>
        </div>
    <?php require_once ('support/body_script.php') ?>
    </body>
</html>
