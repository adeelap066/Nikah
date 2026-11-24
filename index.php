<?php
require_once 'db.php';

$params = [];
$whereClauses = ["1=1"];

if (!empty($_GET['gender'])) {
    $whereClauses[] = "gender = ?";
    $params[] = $_GET['gender'];
}
if (!empty($_GET['city'])) {
    $whereClauses[] = "city LIKE ?";
    $params[] = "%" . $_GET['city'] . "%";
}
if (isset($_SESSION['user_id'])) {
    $whereClauses[] = "id != ?";
    $params[] = $_SESSION['user_id'];
}

$sql = "SELECT * FROM users WHERE " . implode(" AND ", $whereClauses) . " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$profiles = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Nikah - A Halal Rishta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><a href="index.php">Nikah - A Halal Rishta</a></h1>
    <nav>
        <a href="index.php">Browse Profiles</a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <form method="GET" class="filter-bar">
        <select name="gender">
            <option value="">All Genders</option>
            <option value="Male" <?= (isset($_GET['gender']) && $_GET['gender'] == 'Male') ? 'selected' : '' ?>>Groom (Male)</option>
            <option value="Female" <?= (isset($_GET['gender']) && $_GET['gender'] == 'Female') ? 'selected' : '' ?>>Bride (Female)</option>
        </select>
        <input type="text" name="city" placeholder="Search by City" value="<?= htmlspecialchars($_GET['city'] ?? '') ?>">
        <button type="submit">Search Rishta</button>
    </form>

    <?php if (isset($_GET['success'])): ?>
        <div class="alert">Proposal successfully sent!</div>
    <?php endif; ?>

    <div class="grid">
        <?php if (count($profiles) > 0): ?>
            <?php foreach ($profiles as $row): ?>
                <div class="card">
                    <span class="badge"><?= htmlspecialchars($row['gender']) ?> • <?= $row['age'] ?> Years</span>
                    <h3><?= htmlspecialchars($row['full_name']) ?></h3>
                    <p><strong>City:</strong> <?= htmlspecialchars($row['city']) ?></p>
                    <p><strong>Education:</strong> <?= htmlspecialchars($row['education']) ?></p>
                    <p><strong>Profession:</strong> <?= htmlspecialchars($row['profession']) ?></p>
                    <p><strong>Sect:</strong> <?= htmlspecialchars($row['sect']) ?></p>
                    <p><strong>About:</strong> <?= htmlspecialchars($row['bio']) ?></p>
                    <br>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="send_interest.php?id=<?= $row['id'] ?>" class="btn">Send Rishta Proposal</a>
                    <?php else: ?>
                        <a href="login.php" class="btn">Login to Connect</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No profiles found matching your criteria.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>