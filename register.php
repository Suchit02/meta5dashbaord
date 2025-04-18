<?php
session_start();
require 'db.php';

$name = $email = $password = $account_login = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $account_login = trim($_POST['account_login']);

    // Validation
    if (empty($name)) $errors[] = 'Name is required.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    if (empty($account_login)) $errors[] = 'MetaTrader Account Login is required.';

    // Check if email already exists
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = 'Email already registered.';

    if (!$errors) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash, account_login) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $password_hash, $account_login]);
        header('Location: login.php?registered=1');
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Fundings4u</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-container">
    <h2>Create Account</h2>
    <?php if ($errors): ?>
        <div class="error-box">
            <?php foreach ($errors as $error) echo '<p>' . htmlspecialchars($error) . '</p>'; ?>
        </div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="text" name="name" placeholder="Your name" value="<?= htmlspecialchars($name) ?>" required><br>
        <input type="email" name="email" placeholder="Your email" value="<?= htmlspecialchars($email) ?>" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <input type="text" name="account_login" placeholder="MetaTrader Account Login" value="<?= htmlspecialchars($account_login) ?>" required><br>
        <button type="submit">Create Account</button>
    </form>
    <p>Already have an account? <a href="login.php">Sign in</a></p>
</div>
</body>
</html>
