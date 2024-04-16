<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Plasticbrain\FlashMessages\FlashMessages;

require_once('auth_helpers.php');
session_start();
$flash_msg = new FlashMessages();

if (array_key_exists('current_user', $_SESSION)) {
    header("Location: index.php");
    exit;
}

if (array_key_exists('user_role', $_SESSION)) {
    header("Location: index.php");
    exit;
}

$auth_msg = null;
$authenticated = false;

if ($_POST && !empty($_POST['username']) && !empty($_POST['password'])) {
// try to authenticate
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $password = filter_input(INPUT_POST, 'password');

    if (!is_null($username) && !is_null($password)) {
        $authenticated = authenticate_user($username, $password);
    }

    if (!$authenticated) {
        $auth_msg = "You have not provide correct login credentials";
    } else {
        $flash_msg->success("Logged in");
        user_session_check();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank - Login</title>
    <?php require_once('support/head_includes.php') ?>
</head>
<body>
<div class="container">
    <?php require_once('main_nav.php') ?>
  <div class="row">
    <form action="" class="col offset-l2 s8" method="post">
      <div class="row">
        <h1>SnackerRank Login</h1>
        <p>Use the form below to log into SnackerRank</p>
      </div>
      <div class="row">
        <div class="input-field">
          <label for="username">Username</label>
          <input type="text" class="formInput" id="username" name="username">
        </div>
        <div class="input-field">
          <label for="password">Password</label>
          <input type="password" name="password" id="password" class="formInput">
        </div>
      </div>
      <div class="row">
        <button class="btn-large" type="submit">Login</button>
      </div>
        <?php if (!is_null($auth_msg)): ?>
          <div class="errorRow">
            <p><?= $auth_msg; ?></p>
          </div>
        <?php endif; ?>
    </form>
  </div>
</div>
</body>
</html>

