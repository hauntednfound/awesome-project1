<?php
include __DIR__ . '/connection.php';

function getPosts() {
    global $connection;
    return $connection->query("
        SELECT p.id, p.user_id, p.content, p.created_at, u.first_name, u.last_name, u.profile_pic
        FROM posts p
        JOIN users u ON p.user_id = u.user_id
        ORDER BY p.created_at DESC
    ");
}
?>