<?php
require_once 'db.php';

$message = "";
$errorClass = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $gender = $_POST['gender'];
    $age = (int)$_POST['age'];
    $city = trim($_POST['city']);
    $education = trim($_POST['education']);
    $profession = trim($_POST['profession']);
    $sect = trim($_POST['sect']);
    $wali_contact = trim($_POST['wali_contact']);
    $bio = trim($_POST['bio']);

    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() > 0) {
        $message = "Email is already registered.";
        $errorClass = "alert-error";
    } else {
        $sql = "INSERT INTO users (full_name, email, password, gender, age, city, education, profession, sect, wali_contact, bio) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        if ($stmt->execute([$full_name, $email, $password, $gender, $age, $city, $education, $profession, $sect, $wali_contact, $bio])) {
            header("Location: login.php?registered=1");
            exit();
        } else {
            $message = "Registration failed. Try again.";
            $errorClass = "alert-error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Nikah Halal Rishta</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <h1><a href="index.php">Nikah - A Halal Rishta</a></h1>
    <nav>
        <a href="index.php">Home</a>
        <a href="login.php">Login</a>
    </nav>
</header>

<div class="form-box">
    <h2>Create Halal Rishta Profile</h2>
    <?php if ($message) echo "<div class='alert $errorClass'>$message</div>"; ?>
    <form method="POST">
        <input type="text" name="full_name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="gender" required>
            <option value="">Select Gender</option>
            <option value="Male">Male (Groom)</option>
            <option value="Female">Female (Bride)</option>
        </select>
        <input type="number" name="age" placeholder="Age (e.g. 25)" min="18" max="75" required>
        <input type="text" name="city" placeholder="City" required>
        <input type="text" name="education" placeholder="Highest Education" required>
        <input type="text" name="profession" placeholder="Profession" required>
        <input type="text" name="sect" placeholder="Sect / Maslak" value="Sunni">
        <input type="text" name="wali_contact" placeholder="Wali Contact (Hidden until accepted)" required>
        <textarea name="bio" rows="3" placeholder="Brief background..." required></textarea>
        <button type="submit">Register Profile</button>
    </form>
</div>

</body>
</html>