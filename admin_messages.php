<?php
session_start();
require 'db.php';

if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header('Location: login.php');
    exit();
}

$messages = $pdo->query("SELECT * FROM messages ORDER BY created_at DESC")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Messages</title>
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
    <h1 class="text-center mb-4">Admin Messages</h1>

    <table class="table table-bordered table-striped">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Sender Email</th>
                <th>Subject</th>
                <th>Message</th>
                <th>Sent At</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($messages as $message): ?>
                <tr>
                    <td><?= htmlspecialchars($message['message_id']) ?></td>
                    <td><?= htmlspecialchars($message['user_email']) ?></td>
                    <td><?= htmlspecialchars($message['subject']) ?></td>
                    <td><?= substr(htmlspecialchars($message['message']), 0, 50) . '...' ?></td> 
                    <td><?= htmlspecialchars($message['created_at']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm" data-toggle="modal" data-target="#messageModal<?= $message['message_id'] ?>">View</button>
                    </td>
                </tr>
                <div class="modal fade" id="messageModal<?= $message['message_id'] ?>" tabindex="-1" role="dialog" aria-labelledby="messageModalLabel<?= $message['message_id'] ?>" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="messageModalLabel<?= $message['message_id'] ?>">Message from <?= htmlspecialchars($message['user_email']) ?></h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
                                <p><strong>Message:</strong></p>
                                <p><?= nl2br(htmlspecialchars($message['message'])) ?></p>
                                <p><strong>Sent At:</strong> <?= htmlspecialchars($message['created_at']) ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.bundle.min.js"></script>

</body>
</html>
