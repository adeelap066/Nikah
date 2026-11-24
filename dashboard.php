<?php
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Handle Accept / Decline actions (Works for both Received & Sent for testing)
if (isset($_GET['action']) && isset($_GET['interest_id'])) {
    $interest_id = (int)$_GET['interest_id'];
    $status = ($_GET['action'] == 'accept') ? 'Accepted' : 'Declined';
    
    $update = $pdo->prepare("UPDATE interests SET status = ? WHERE id = ? AND (receiver_id = ? OR sender_id = ?)");
    $update->execute([$status, $interest_id, $user_id, $user_id]);
    header("Location: dashboard.php");
    exit();
}

// Fetch Received Proposals
$recvStmt = $pdo->prepare("
    SELECT i.id AS interest_id, i.status, u.* 
    FROM interests i 
    JOIN users u ON i.sender_id = u.id 
    WHERE i.receiver_id = ?
");
$recvStmt->execute([$user_id]);
$received = $recvStmt->fetchAll();

// Fetch Sent Proposals
$sentStmt = $pdo->prepare("
    SELECT i.id AS interest_id, i.status, u.* 
    FROM interests i 
    JOIN users u ON i.receiver_id = u.id 
    WHERE i.sender_id = ?
");
$sentStmt->execute([$user_id]);
$sent = $sentStmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Nikah Halal Rishta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><a href="index.php">Nikah - A Halal Rishta</a></h1>
    <nav>
        <a href="index.php">Browse Profiles</a>
        <a href="dashboard.php">Dashboard</a>
        <a href="logout.php">Logout</a>
    </nav>
</header>

<div class="container">
    <h2>Assalamu Alaikum, <?= htmlspecialchars($_SESSION['full_name']) ?></h2>

    <br>
    <h3>Received Proposals</h3>
    <div class="grid" style="margin-top:15px; margin-bottom:30px;">
        <?php if (count($received) > 0): ?>
            <?php foreach ($received as $row): ?>
                <div class="card">
                    <span class="badge">Status: <?= htmlspecialchars($row['status']) ?></span>
                    <h3><?= htmlspecialchars($row['full_name']) ?> (<?= $row['age'] ?> yrs)</h3>
                    <p><strong>City:</strong> <?= htmlspecialchars($row['city']) ?></p>
                    <p><strong>Profession:</strong> <?= htmlspecialchars($row['profession']) ?></p>
                    
                    <?php if ($row['status'] == 'Pending'): ?>
                        <br>
                        <a href="dashboard.php?action=accept&interest_id=<?= $row['interest_id'] ?>" class="btn btn-success">Accept</a>
                        <a href="dashboard.php?action=decline&interest_id=<?= $row['interest_id'] ?>" class="btn btn-danger">Decline</a>
                    <?php elseif ($row['status'] == 'Accepted'): ?>
                        <p style="margin-top:10px; color:#27ae60;"><strong>Wali Contact:</strong> <?= htmlspecialchars($row['wali_contact']) ?></p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No proposals received yet.</p>
        <?php endif; ?>
    </div>

    <h3>Sent Proposals</h3>
    <div class="grid" style="margin-top:15px;">
        <?php if (count($sent) > 0): ?>
            <?php foreach ($sent as $row): ?>
                <div class="card">
                    <span class="badge">Status: <?= htmlspecialchars($row['status']) ?></span>
                    <h3><?= htmlspecialchars($row['full_name']) ?> (<?= $row['age'] ?> yrs)</h3>
                    <p><strong>City:</strong> <?= htmlspecialchars($row['city']) ?></p>
                    <p><strong>Profession:</strong> <?= htmlspecialchars($row['profession']) ?></p>
                    
                    <?php if ($row['status'] == 'Accepted'): ?>
                        <p style="margin-top:10px; color:#27ae60;"><strong>Wali Contact:</strong> <?= htmlspecialchars($row['wali_contact']) ?></p>
                    <?php else: ?>
                        <p style="margin-top:10px; font-style:italic;">Wali contact will unlock once accepted.</p>
                        <br>
                        <a href="dashboard.php?action=accept&interest_id=<?= $row['interest_id'] ?>" class="btn btn-success">Accept</a>
                        <a href="dashboard.php?action=decline&interest_id=<?= $row['interest_id'] ?>" class="btn btn-danger">Decline</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>You haven't sent any proposals yet.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>