<?php
session_start();
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fname = trim($_POST['first_name']);
    $lname = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // =========================
    // EMAIL FORMAT CHECK
    // =========================
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "invalid_email";
        header("Location: registerv2.php");
        exit();
    }

    // =========================
    // REQUIRED FIELDS CHECK
    // =========================
    if (empty($fname) || empty($lname) || empty($email) || empty($username) || empty($password)) {
        $_SESSION['error'] = "missing_fields";
        header("Location: registerv2.php");
        exit();
    }

    // =========================
    // CHECK EMAIL EXIST
    // =========================
    $emailCheck = $pdo->prepare("SELECT id FROM tbl_users WHERE email = ?");
    $emailCheck->execute([$email]);

    if ($emailCheck->rowCount() > 0) {
        $_SESSION['error'] = "email_exists";
        header("Location: registerv2.php");
        exit();
    }

    // =========================
    // CHECK USERNAME EXIST
    // =========================
    $userCheck = $pdo->prepare("SELECT id FROM tbl_users WHERE username = ?");
    $userCheck->execute([$username]);

    if ($userCheck->rowCount() > 0) {
        $_SESSION['error'] = "username_exists";
        header("Location: registerv2.php");
        exit();
    }

    // =========================
    // HASH PASSWORD
    // =========================
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // =========================
    // DEFAULT PROFILE PIC
    // =========================
    $profilePicName = "default.png";

    // =========================
    // HANDLE CROPPED IMAGE
    // =========================
    if (!empty($_POST['cropped_image'])) {

        $imageData = $_POST['cropped_image'];

        // REMOVE BASE64 HEADER
        $imageData = str_replace('data:image/png;base64,', '', $imageData);
        $imageData = str_replace(' ', '+', $imageData);

        // DECODE
        $imageDecoded = base64_decode($imageData);

        // CREATE FOLDER IF NOT EXISTS
        $targetDir = "uploads/";
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // UNIQUE FILE NAME
        $fileName = time() . "_profile.png";
        $filePath = $targetDir . $fileName;

        // SAVE FILE
        if (file_put_contents($filePath, $imageDecoded)) {
            $profilePicName = $fileName;
        }
    }

    // =========================
    // INSERT USER
    // =========================
    $stmt = $pdo->prepare("
        INSERT INTO tbl_users 
        (first_name, last_name, username, email, profile_pic, password, created_at)
        VALUES (?, ?, ?, ?, ?, ?, NOW())
    ");

    $stmt->execute([
        $fname,
        $lname,
        $username,
        $email,
        $profilePicName,
        $hashedPassword
    ]);

    // =========================
    // SUCCESS
    // =========================
    $_SESSION['success'] = "registered";

    header("Location: registerv2.php");
    exit();
}
?>