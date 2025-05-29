<?php
session_start();
include __DIR__ . '/connection.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    if (!empty($content)) {
        $stmt = $connection->prepare("INSERT INTO posts (user_id, content, created_at) VALUES (?, ?, NOW())");
        $stmt->bind_param("is", $_SESSION['user_id'], $content);
        $stmt->execute();
        $stmt->close();
    }
}

header("Location: ../index.php");
exit();
