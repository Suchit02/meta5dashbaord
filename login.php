<?php
session_start();
require 'db.php';

$email = $password = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required.';
    if (empty($password)) $errors[] = 'Password is required.';

    if (!$errors) {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: dashboard.php');
            exit();
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Fundings4u</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="auth-container">
    <h2>Sign in to your account</h2>
    <?php if (isset($_GET['registered'])): ?>
        <div class="success-box">Account created! Please login.</div>
    <?php endif; ?>
    <?php if ($errors): ?>
        <div class="error-box">
            <?php foreach ($errors as $error) echo '<p>' . htmlspecialchars($error) . '</p>'; ?>
        </div>
    <?php endif; ?>
    <form method="post" autocomplete="off">
        <input type="email" name="email" placeholder="Your email" value="<?= htmlspecialchars($email) ?>" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Sign in</button>
    </form>
    <p>Don't have an account? <a href="register.php">Create Account</a></p>
</div>
</body>
</html>
