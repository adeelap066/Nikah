<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = (int)$_GET['id'];

if ($sender_id !== $receiver_id) {
    $check = $pdo->prepare("SELECT id FROM interests WHERE sender_id = ? AND receiver_id = ?");
    $check->execute([$sender_id, $receiver_id]);
    
    if ($check->rowCount() == 0) {
        $insert = $pdo->prepare("INSERT INTO interests (sender_id, receiver_id, status) VALUES (?, ?, 'Pending')");
        $insert->execute([$sender_id, $receiver_id]);
    }
}

header("Location: index.php?success=1");
exit();
?>