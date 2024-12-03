<?php
require 'db.php'; 
session_start();

try {
    $stmt = $pdo->query("SELECT * FROM modules");
    $modules = $stmt->fetchAll();
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage();
    die();
}

function fetchPosts($pdo, $moduleId = null) {
    if ($moduleId) {
        $query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.user_id WHERE posts.module_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$moduleId]);
    } else {
        $query = "SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.user_id";
        $stmt = $pdo->query($query);
    }
    return $stmt->fetchAll();
}

$selectedModuleId = isset($_GET['module_id']) ? $_GET['module_id'] : null;
$posts = fetchPosts($pdo, $selectedModuleId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Q&A System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            margin-top: 70px; 
        }
        .navbar {
            margin-bottom: 20px;
        }
        .container {
            margin-top: 30px; 
            margin-bottom: 100px; 
        }
        .card-body {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
        }
        .card-title {
            font-size: 1.25rem;
        }
        .card-text {
            font-size: 1rem;
            color: #495057;
        }
        .form-group label {
            font-weight: bold;
        }
        .footer {
            background-color: #343a40;
            color: #fff;
            padding: 10px;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
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
                    <a href="logout.php" class="btn btn-outline-light ml-2">Logout</a>
                </li>
                <?php if ($_SESSION['is_admin']): ?>
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="btn btn-outline-light ml-2">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
                <li class="nav-item">
                    <a href="add_post.php" class="btn btn-primary ml-2">Add New Post</a>
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
    <div class="row">
        <div class="col-md-3">
            <h4>Modules</h4>
            <ul class="list-group">
                <li class="list-group-item"><a href="index.php">View All</a></li>
                <?php foreach ($modules as $module): ?>
                    <li class="list-group-item"><a href="?module_id=<?= $module['module_id'] ?>"><?= htmlspecialchars($module['module_name']) ?></a></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-9">
            <h1 class="mb-4">Student Questions</h1>
            <div id="posts">
                <?php if (!empty($posts)): ?>
                    <?php foreach ($posts as $post): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($post['title']) ?></h5>
                                <p class="card-text"><?= htmlspecialchars($post['content']) ?></p>
                                <p><strong>Posted by: </strong><?= htmlspecialchars($post['username']) ?></p>
                                <?php if ($_SESSION['user_id'] === $post['user_id']): ?>
                                    <a href="edit_post.php?post_id=<?= $post['post_id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <a href="delete_post.php?post_id=<?= $post['post_id'] ?>" onclick="return confirm('Are you sure you want to delete this post?');" class="btn btn-danger btn-sm">Delete</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No posts to display.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="container mt-5">
    <h1>Contact Admin</h1>
    <form action="send_email.php" method="post">
        <div class="form-group">
            <label for="email">Your Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="message">Message</label>
            <textarea name="message" id="message" class="form-control" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Send Message</button>
    </form>
</div>

<div class="footer">
    <p>&copy; 2024 Q&A System. Made By Luong Thach Han</p>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
