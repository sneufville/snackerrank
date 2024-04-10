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

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Manage Users</title>
        <?php require_once('support/head_includes.php') ?>
    </head>
    <body>
        <div class="container">
            <?php require_once('partials/admin_nav.php') ?>
            <?php if($flash_msg->hasMessages()): ?>
            <?= $flash_msg->display() ?>
            <?php endif; ?>
            <?php if($flash_msg->hasErrors()): ?>
            <?= $flash_msg->display() ?>
            <?php endif; ?>
          <div class="flexRow">
              <p class="flow-text">Manage SnackerRank Users</p>
              <a class="btn" href="dashboard_add_user.php">Add User</a>
          </div>
            <?php if (is_array($users)): ?>
            <table class="striped">
                <thead>
                <tr>
                  <th>User Id</th>
                  <th>Username</th>
                  <th>Role</th>
                  <th>Actions</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><?= $user['role'] ?></td>
                    <td>
                      <a class="btn" href="dashboard_edit_user.php?user_id=<?= $user['id'] ?>">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
            <?php endif; ?>
        </div>
    <?php require_once('support/body_script.php'); ?>
    </body>
</html>
