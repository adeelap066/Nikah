<?php
require_once 'db.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$interest_id = (int)$_GET['id'];

// Ensure user only deletes their own sent proposal
$stmt = $pdo->prepare("DELETE FROM interests WHERE id = ? AND sender_id = ?");
$stmt->execute([$interest_id, $user_id]);

header("Location: dashboard.php");
exit();
?>