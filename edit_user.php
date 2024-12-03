<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

$userId = $_GET['user_id'] ?? null;

if (!$userId) {
    echo "User not found.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    $stmt = $pdo->prepare("UPDATE users SET email = ?, is_admin = ? WHERE user_id = ?");
    $stmt->execute([$email, $isAdmin, $userId]);

    header("Location: admin_dashboard.php");
    exit();
}

// Fetch user data
$stmt = $pdo->prepare("SELECT username, email, is_admin FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if (!$user) {
    echo "User not found.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>
    <form action="edit_user.php?user_id=<?= $userId ?>" method="post">
        Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required><br>
        Is Admin: <input type="checkbox" name="is_admin" <?= $user['is_admin'] ? 'checked' : '' ?>><br>
        <input type="submit" value="Update User">
    </form>
</body>
</html>
