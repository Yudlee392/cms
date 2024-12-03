<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$isAdmin = isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1;

$modules = $pdo->query("SELECT module_id, module_name FROM modules")->fetchAll();

$users = [];
if ($isAdmin) {
    $users = $pdo->query("SELECT user_id, username FROM users")->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $content = htmlspecialchars($_POST['content']);
    $moduleId = $_POST['module_id'];
    $userId = $isAdmin ? $_POST['user_id'] : $_SESSION['user_id']; 

    $imagePath = "";
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] == 0) {
        $fileName = basename($_FILES['post_image']['name']);
        $targetPath = "upload/" . $fileName;

        if (move_uploaded_file($_FILES['post_image']['tmp_name'], $targetPath)) {
            $imagePath = $targetPath;
        }
    }

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, user_id, module_id, image) VALUES (?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $content, $userId, $moduleId, $imagePath])) {
        header("Location: index.php");
        exit();
    } else {
        echo "Error adding post.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add New Post</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px; 
        }
        .container {
            margin-top: 30px;
        }
        .btn-custom {
            margin-top: 20px;
        }
        .form-group label {
            font-weight: bold;
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
                <?php if ($_SESSION['is_admin']): ?>
                    <li class="nav-item">
                        <a href="admin_dashboard.php" class="btn btn-outline-light ml-2">Admin Dashboard</a>
                    </li>
                <?php endif; ?>
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
    <h1 class="text-center mb-4">Add New Post</h1>
    <form action="add_post.php" method="post" enctype="multipart/form-data" class="bg-white p-4 rounded shadow-sm">
        <div class="form-group">
            <label for="title">Title:</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="content">Content:</label>
            <textarea name="content" id="content" class="form-control" rows="5" required></textarea>
        </div>
        <div class="form-group">
            <label for="module_id">Module:</label>
            <select name="module_id" id="module_id" class="form-control" required>
                <?php foreach ($modules as $module): ?>
                    <option value="<?= $module['module_id'] ?>"><?= htmlspecialchars($module['module_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <?php if ($isAdmin): ?>
            <div class="form-group">
                <label for="user_id">Assign to User:</label>
                <select name="user_id" id="user_id" class="form-control" required>
                    <?php foreach ($users as $user): ?>
                        <option value="<?= $user['user_id'] ?>"><?= htmlspecialchars($user['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endif; ?>
        <button type="submit" class="btn btn-primary btn-custom">Add Post</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
