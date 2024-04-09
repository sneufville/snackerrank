<?php

require_once(__DIR__ . '/vendor/autoload.php');

use Plasticbrain\FlashMessages\FlashMessages;

require_once('auth_helpers.php');
require_once('search_helpers.php');
require_once('db_connect.php');

session_start();
$flash_msg = new FlashMessages();

admin_auth_guard();

$user_id = filter_input(INPUT_GET, 'user_id', FILTER_VALIDATE_INT);
$user = get_user($user_id);

if ($_POST && !empty($_POST['user_id']) && !empty($_POST['role'])) {
    $role = filter_input(INPUT_POST, 'role');
    $user_id = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

    global $db;
    // optional password update
    $should_update_password = filter_input(INPUT_POST, 'update_password') == 'on';

    if ($should_update_password) {
        $new_pw_str = empty($_POST['new_password']) ? '' : trim($_POST['new_password']);
        if (strlen($new_pw_str) < 6) {
          $flash_msg->error("Passwords must be at least 6 characters", "dashboard_edit_user.php?user_id={$user_id}");
          exit;
        }
        // generate a new hash
        $hashed_pw = password_hash($new_pw_str, PASSWORD_BCRYPT);
        $query_str = "UPDATE users SET role = :role, password = :password WHERE id = :user_id";
        $statement = $db->prepare($query_str);
        $statement->bindValue(':role', $role);
        $statement->bindValue(':password', $hashed_pw);
//        $statement->bindValue(':user_id', $user_id);
    } else {
        $query_str = "UPDATE users SET role = :role WHERE id = :user_id";
        $statement = $db->prepare($query_str);

    }
    $statement->bindValue(':role', $role);
    $statement->bindValue(':user_id', $user_id);
    $statement->execute();
    if ($statement->rowCount() == 1) {
        $flash_msg->success("User updated successfully", "dashboard_list_users.php");
        exit;
    } else {
        $flash_msg->error("Something went wrong when updating user", "dashboard_list_users.php");
        exit;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit User</title>
</head>
<body>
<div>
    <?php if ($flash_msg->hasErrors()): ?>
    <?= $flash_msg->display() ?>
    <?php endif; ?>
    <?php if (is_array($user)): ?>
      <h2>Edit User: <?= $user['username'] ?></h2>
      <form action="" method="post">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <div class="formRow">
          <label for="user_id">User Id</label><br>
          <input type="text" id="user_id" value="<?= $user['id'] ?>" disabled>
        </div>
        <div class="formRow">
          <label for="username">Username</label><br>
          <input type="text" id="username" value="<?= $user['username'] ?>" disabled>
        </div>
        <hr>
        <div class="formRow">
          <label><input type="checkbox" name="update_password" id="updatePassword"> Update Password</label>
        </div>
        <div class="formRow">
          <label for="newPassword">Update Password</label><br>
          <input type="password" name="new_password" id="newPassword" disabled>
        </div>
        <div class="formRow">
          <label for="user_role">Role</label><br>
          <select name="role" id="user_role">
            <option value="">Select Role</option>
            <option value="user" <?= $user['role'] == 'user' ? 'selected' : '' ?>>User</option>
            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
          </select>
        </div>
        <div class="formRow">
          <button type="submit">Update User</button>
        </div>
      </form>
      <br>
      <form action="dashboard_delete_user.php" method="post">
        <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
        <button type="submit">Delete User</button>
      </form>
    <?php endif; ?>
</div>
<script type="application/javascript">
  const changePWCheckbox = document.querySelector("input[id=updatePassword]");
  const newPasswordInput = document.querySelector("input[id=newPassword]");
  changePWCheckbox.addEventListener("change", (e) => {
    e.currentTarget.checked ? newPasswordInput.removeAttribute("disabled") : newPasswordInput.setAttribute("disabled", "disabled");
  });
</script>
</body>
</html>
