<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userEmail = htmlspecialchars($_POST['email']);
    $subject = htmlspecialchars($_POST['subject']);
    $message = htmlspecialchars($_POST['message']);

    // Validate email address
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email address.";
        exit();
    }

    // Insert the message into the database
    $stmt = $pdo->prepare("INSERT INTO messages (user_email, subject, message) VALUES (?, ?, ?)");
    if ($stmt->execute([$userEmail, $subject, $message])) {
        echo "<h1>Message Sent Successfully!</h1>";
        echo "<p>Your message has been received and will be reviewed by our admin.</p>";
        echo "<a href='index.php'>Back to Home</a>";
    } else {
        echo "Error: Could not send the message.";
    }
} else {
    header('Location: index.php');
    exit();
}
