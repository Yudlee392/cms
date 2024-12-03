<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['moduleName'])) {
    $moduleName = htmlspecialchars($_POST['moduleName']);

    $stmt = $pdo->prepare("INSERT INTO modules (module_name) VALUES (?)");
    if ($stmt->execute([$moduleName])) {
        header('Location: manage_modules.php');
        exit();
    } else {
        echo "Error adding module.";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_module_id'])) {
    $moduleId = $_POST['delete_module_id'];

    $stmt = $pdo->prepare("DELETE FROM modules WHERE module_id = ?");
    $stmt->execute([$moduleId]);

    header('Location: manage_modules.php');
    exit();
}

$modules = $pdo->query("SELECT * FROM modules")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Modules</title>
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
            margin-right: 10px;
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
                <a href="admin_dashboard.php" class="btn btn-outline-light ml-2">Back Admin Dashboard</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    <h1 class="text-center mb-4">Manage Modules</h1>

    <form action="manage_modules.php" method="post" class="bg-white p-4 rounded shadow-sm mb-5">
        <div class="form-group">
            <label for="moduleName">Add New Module</label>
            <input type="text" name="moduleName" id="moduleName" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Module</button>
    </form>

    <h2 class="mb-4">Existing Modules</h2>
    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>Module ID</th>
                <th>Module Name</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($modules as $module): ?>
                <tr>
                    <td><?= htmlspecialchars($module['module_id']) ?></td>
                    <td><?= htmlspecialchars($module['module_name']) ?></td>
                    <td>
                        <form action="manage_modules.php" method="post" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to delete this module? This action cannot be undone.');">
                            <input type="hidden" name="delete_module_id" value="<?= $module['module_id'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

</body>
</html>
