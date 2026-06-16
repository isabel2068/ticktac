<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // =========================
    // GET FORM DATA
    // =========================
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // =========================
    // VALIDATION
    // =========================
    if ($password !== $confirm_password) {
        die("Passwords do not match.");
    }

    if (strlen($password) < 6) {
        header("Location: register.php?error=password_length");
        exit;
    }

    if (!isset($_FILES['profileUpload']) || $_FILES['profileUpload']['error'] !== 0) {
        header("Location: register.php?error=profile_required");
        exit();
    }

    // =========================
    // CHECK EXISTING USER
    // =========================
    $stmt = $pdo->prepare("SELECT username, email FROM tbl_users WHERE username = ? OR email = ?");
    $stmt->execute([$username, $email]);
    
    $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($existingUser) {
    
        if ($existingUser['username'] === $username) {
            header("Location: signup.php?error=username_taken");
            exit;
        }
    
        if ($existingUser['email'] === $email) {
            header("Location: signup.php?error=email_taken");
            exit;
        }
    }

// =========================
// HANDLE IMAGE UPLOAD (FIXED)
// =========================
$profileName = "default.png";

if (isset($_FILES['profileUpload']) && $_FILES['profileUpload']['error'] === 0) {

    // 🔥 ALWAYS use absolute path
    $targetDir = __DIR__ . "/uploads/";

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }

    $fileTmp = $_FILES["profileUpload"]["tmp_name"];
    $originalName = $_FILES["profileUpload"]["name"];

    $ext = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    $allowed = ['jpg', 'jpeg', 'png'];

    if (!in_array($ext, $allowed)) {
        die("Invalid file type: " . $ext);
    }

    // unique filename
    $fileName = uniqid("profile_", true) . "." . $ext;

    $targetFile = $targetDir . $fileName;

    // 🔥 DEBUG SAFE MOVE
    if (!move_uploaded_file($fileTmp, $targetFile)) {
        die("Upload FAILED. Check folder permissions or path: " . $targetFile);
    }

    $profileName = $fileName;
}

    // =========================
    // HASH PASSWORD
    // =========================
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // =========================
    // INSERT DATA
    // =========================
    $stmt = $pdo->prepare("INSERT INTO tbl_users 
        (first_name, last_name, username, email, profile_pic, password, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, NOW())");

    $stmt->execute([
        $fname,
        $lname,
        $username,
        $email,
        $profileName,
        $hashedPassword
    ]);

    // =========================
    // SUCCESS REDIRECT
    // =========================
    header("Location: login.php?registered=1");
    exit;
}


?>