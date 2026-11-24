<?php
require_once 'db.php';

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Nikah Halal Rishta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><a href="index.php">Nikah - A Halal Rishta</a></h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="register.php">Register</a>
    </nav>
</header>

<div class="form-box">
    <h2>Member Login</h2>
    <?php if (isset($_GET['registered'])) echo "<div class='alert'>Registration successful! Please login.</div>"; ?>
    <?php if ($error) echo "<div class='alert alert-error'>$error</div>"; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>