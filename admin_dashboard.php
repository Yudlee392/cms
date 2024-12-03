<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    $checkStmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
    $checkStmt->execute([$username, $email]);
    if ($checkStmt->fetch()) {
        echo "Error: Username or email already exists.";
    } else {
        $stmt = $pdo->prepare("INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)");
        if ($stmt->execute([$username, $email, $password, $isAdmin])) {
            echo "User added successfully.";
        } else {
            echo "Error adding user.";
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user_id'])) {
    $userId = $_POST['edit_user_id'];
    $username = htmlspecialchars($_POST['username']);
    $email = htmlspecialchars($_POST['email']);
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE users SET username = ?, email = ?, is_admin = ? WHERE user_id = ?");
    if ($stmt->execute([$username, $email, $isAdmin, $userId])) {
        echo "User updated successfully.";
    } else {
        echo "Error updating user.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user_id'])) {
    $userId = $_POST['delete_user_id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
    $stmt->execute([$userId]);

    echo "User deleted successfully.";
    header('Location: admin_dashboard.php');
    exit();
}

$stmt = $pdo->query("SELECT user_id, username, email, is_admin FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            padding-top: 70px;
        }
        .navbar {
            margin-bottom: 30px;
        }
        .container {
            margin-top: 30px;
        }
        .form-check-label {
            font-weight: normal;
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
        .table th, .table td {
            vertical-align: middle;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <a class="navbar-brand" href="#">Admin Dashboard</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a href="logout.php" class="btn btn-outline-light ml-2">Logout</a>
            </li>
            <li class="nav-item">
                <a href="index.php" class="btn btn-outline-light ml-2">Go to Site</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="text-center mb-4">Admin Dashboard</h1>

    <h2 class="mb-4">Add New User</h2>
    <form action="admin_dashboard.php" method="post">
        <input type="hidden" name="add_user" value="1">
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required>
        </div>
        <div class="form-check">
            <input type="checkbox" name="is_admin" id="is_admin" class="form-check-input">
            <label for="is_admin" class="form-check-label">Admin</label>
        </div>
        <button type="submit" class="btn btn-primary mt-2">Add User</button>
    </form>
    <ul>
    <li><a href="manage_posts.php">Manage Posts</a></li>
    <li><a href="manage_modules.php">Manage Modules</a></li>
    <li><a href="admin_messages.php">View Messages</a></li>
    </ul>
    <h2 class="mt-5">Existing Users</h2>
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th>User ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($users as $user): ?>
            <tr>
                <td><?= htmlspecialchars($user['user_id']) ?></td>
                <td><?= htmlspecialchars($user['username']) ?></td>
                <td><?= htmlspecialchars($user['email']) ?></td>
                <td><?= $user['is_admin'] ? 'Admin' : 'User' ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editUserModal<?= $user['user_id'] ?>">Edit</button>
                    <form action="admin_dashboard.php" method="post" style="display:inline;">
                        <input type="hidden" name="delete_user_id" value="<?= $user['user_id'] ?>">
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete</button>
                    </form>
                </td>
            </tr>

            <div class="modal fade" id="editUserModal<?= $user['user_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="editUserModalLabel<?= $user['user_id'] ?>" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="editUserModalLabel<?= $user['user_id'] ?>">Edit User</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form action="admin_dashboard.php" method="post">
                                <input type="hidden" name="edit_user_id" value="<?= $user['user_id'] ?>">
                                <div class="form-group">
                                    <label for="username">Username</label>
                                    <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="email">Email</label>
                                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                                </div>
                                <div class="form-check">
                                    <input type="checkbox" name="is_admin" class="form-check-input" <?= $user['is_admin'] ? 'checked' : '' ?>>
                                    <label for="is_admin" class="form-check-label">Admin</label>
                                </div>
                                <button type="submit" class="btn btn-primary mt-2">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="footer">
    <p>&copy; 2024 Admin Dashboard. Made By Luong Thach Han.</p>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

</body>
</html>
