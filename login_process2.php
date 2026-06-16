<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $login = trim($_POST['login']);
    $password = trim($_POST['password']);

    // ✅ ALWAYS STORE INPUT (IMPORTANT FIX)
    $_SESSION['old_login'] = $login;

    // =========================
    // EMPTY CHECK
    // =========================
    if (empty($login) || empty($password)) {
        $_SESSION['error'] = "missing_fields";
        header("Location: loginv2.php");
        exit();
    }

    // =========================
    // FIND USER
    // =========================
    $stmt = $pdo->prepare("
        SELECT * FROM tbl_users
        WHERE username = :login OR email = :login
        LIMIT 1
    ");

    $stmt->execute(['login' => $login]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // =========================
    // USER FOUND
    // =========================
    if ($user) {

        // CHECK PASSWORD
        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['first_name'] = $user['first_name'];
            $_SESSION['profile_pic'] = $user['profile_pic'];
            $_SESSION['last_name'] = $user['last_name'];
            $_SESSION['login_success'] = true;

            header("Location: loginv2.php");
            exit();

        } else {
            // ❌ WRONG PASSWORD
            $_SESSION['error'] = "wrong_password";
            header("Location: loginv2.php");
            exit();
        }

    } else {
        // ❌ USER NOT FOUND
        $_SESSION['error'] = "user_not_found";
        header("Location: loginv2.php");
        exit();
    }
}
?>