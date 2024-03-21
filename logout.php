<?php
session_start();

if (isset($_SESSION) && !is_null($_SESSION['current_user'])) {
    $_SESSION = [];
    header('Location: index.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>SnackerRank - Logged Out</title>
</head>
<body>
  <h1>Logged Out</h1>
  <a href="index.php">SnackerRank Home</a>
</body>
</html>
