<?php

use JetBrains\PhpStorm\NoReturn;

require_once ('db_connect.php');

/**
 * Function that authenticates a user and starts their session
 *
 * @param string $username
 * @param string $password
 * @return bool
 */
function authenticate_user(string $username, string $password): bool
{
    global $db;

    $user_query = "SELECT username, password, role FROM users WHERE username = :username";
    $statement = $db->prepare($user_query);
    $statement->bindValue(':username', $username);
    $statement->execute();
    $user_record = $statement->fetch();

    if (is_bool($user_record)) return false;

    if (!password_verify($password, $user_record['password'])) return false;

    session_start();
    $_SESSION['current_user'] = $user_record['username'];
    $_SESSION['user_role'] = $user_record['role'];
    return true;
}

function user_session_check(): void
{
    if (isset($_SESSION) && !is_null($_SESSION['current_user'])) {
        echo 'redirect';
        print_r($_SESSION);
        if ($_SESSION['user_role'] == 'admin') {
            echo 'redirect to admin dashboard';
            header('Location: admin_dashboard.php');
            exit;
        }

        header('Location: index.php');
        exit;
    }

    header('Location: index.php');
    exit;
}

function has_admin_session(): bool
{
    if (isset($_SESSION) && !is_null($_SESSION['current_user'])) {
        return $_SESSION['user_role'] == 'admin';
    }

    return false;
}
