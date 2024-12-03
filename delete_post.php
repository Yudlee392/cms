<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['post_id'])) {
    $postId = $_GET['post_id'];

    // Check if the user is the owner of the post
    $stmt = $pdo->prepare("SELECT user_id FROM posts WHERE post_id = ?");
    $stmt->execute([$postId]);
    $post = $stmt->fetch();

    if ($post['user_id'] !== $_SESSION['user_id']) {
        echo "You do not have permission to delete this post.";
        exit();
    }

    // Delete the post if the user is the owner
    $deleteStmt = $pdo->prepare("DELETE FROM posts WHERE post_id = ? AND user_id = ?");
    $deleteStmt->execute([$postId, $_SESSION['user_id']]);
    header("Location: index.php");
    exit();
}

echo "Post not found.";
?>
