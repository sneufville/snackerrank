<?php

require_once (__DIR__ . '/vendor/autoload.php');
use Plasticbrain\FlashMessages\FlashMessages;

require_once('search_helpers.php');
require_once('auth_helpers.php');

session_start();
$flash_msg = new FlashMessages();

// check if the user is an admin
admin_auth_guard();

// users
$users = get_user_list();

print_r($_SESSION);

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Manage Users</title>
    </head>
    <body>
        <div>
            <?php if($flash_msg->hasMessages()): ?>
            <?= $flash_msg->display() ?>
            <?php endif; ?>
            <?php if($flash_msg->hasErrors()): ?>
            <?= $flash_msg->display() ?>
            <?php endif; ?>
            <a href="dashboard_add_user.php">Add User</a>
            <?php if (is_array($users)): ?>
            <table>
                <caption>Manage SnackerRank Users</caption>
                <tbody>
                <tr>
                    <td>User Id</td>
                    <td>Username</td>
                    <td>Role</td>
                    <td>Actions</td>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td><a href="dashboard_edit_user.php?user_id=<?= $user['id'] ?>">Edit User</a></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <?php endif; ?>
        </div>
    </body>
</html>
