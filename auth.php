<?php

require_once('auth_helpers.php');
session_start();

user_session_check();

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
        user_session_check();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank - Login</title>
</head>
<body>
<div>
  <form action="" method="post">
    <h1>SnackerRank Login</h1>
    <div class="formRow">
      <label for="username">Username</label>
      <input type="text" class="formInput" id="username" name="username">
    </div>
    <div class="formRow">
      <label for="password">Password</label>
      <input type="password" name="password" id="password" class="formInput">
    </div>
    <div class="formRow">
      <button type="submit">Login</button>
    </div>
    <?php if (!is_null($auth_msg)): ?>
    <div class="errorRow">
      <p><?= $auth_msg; ?></p>
    </div>
    <?php endif; ?>
  </form>
</div>
</body>
</html>

