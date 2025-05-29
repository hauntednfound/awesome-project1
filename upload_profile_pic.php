<?php
session_start();
include __DIR__ . '/db/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    $user_id = $_SESSION['user_id'];

    $file = $_FILES['profile_pic'];
    $upload_dir = __DIR__ . '/uploads/profile_pics/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }


    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        die("Unsupported file type. Only JPG, PNG, and GIF allowed.");
    }

    if ($file['size'] > 2 * 1024 * 1024) {
        die("File too large. Max 2MB.");
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = 'user_' . $user_id . '_' . time() . '.' . $ext;
    $destination = $upload_dir . $filename;

    if (move_uploaded_file($file['tmp_name'], $destination)) {
        $stmt = $connection->prepare("UPDATE users SET profile_pic = ? WHERE user_id = ?");
        $profile_pic_path = 'uploads/profile_pics/' . $filename; 
        $stmt->bind_param("si", $profile_pic_path, $user_id);
        $stmt->execute();
        $stmt->close();

        $_SESSION['profile_pic'] = $profile_pic_path;

        header("Location: index.php");
        exit();
    } else {
        die("Failed to upload file.");
    }
}
?>
