<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$postId = $_GET['post_id'] ?? null;
if (!$postId) {
    echo "Post not found.";
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE post_id = ?");
$stmt->execute([$postId]);
$post = $stmt->fetch();

if ($post['user_id'] !== $_SESSION['user_id']) {
    echo "You do not have permission to edit this post.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);

    $updateStmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE post_id = ? AND user_id = ?");
    $updateStmt->execute([$title, $content, $postId, $_SESSION['user_id']]);
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        .container {
            margin-top: 50px;
        }
        .form-control {
            border-radius: 0.375rem;
        }
        .btn-custom {
            width: 100%;
            font-size: 16px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Q&A System</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <?php if (isset($_SESSION['username'])): ?>
                <li class="nav-item">
                    <span class="navbar-text text-white">Welcome, <?= htmlspecialchars($_SESSION['username']) ?> </span>
                </li>
                <li class="nav-item">
                    <a href="index.php" class="btn btn-outline-light ml-2">Home</a>
                </li>
                <li class="nav-item">
                    <a href="logout.php" class="btn btn-outline-light ml-2">Logout</a>
                </li>
            <?php else: ?>
                <li class="nav-item">
                    <a href="login.php" class="btn btn-outline-light ml-2">Login</a>
                </li>
                <li class="nav-item">
                    <a href="register.php" class="btn btn-outline-light ml-2">Register</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="text-center mb-4">Edit Post</h1>

    <form action="edit_post.php?post_id=<?= $post['post_id'] ?>" method="post">
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
        </div>

        <div class="form-group">
            <label for="content">Content</label>
            <textarea name="content" class="form-control" rows="6" required><?= htmlspecialchars($post['content']) ?></textarea>
        </div>

        <button type="submit" class="btn btn-primary btn-custom">Update Post</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
