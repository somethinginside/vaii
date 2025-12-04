<?php
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Валидация
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Incorrect email!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password does not match!';
    } else {
        // Проверяем, существует ли такой email
        $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'User with this email already exists!';
        } else {
            // Хэшируем пароль
            $hashed_password = hashPassword($password);

            // Записываем в БД
            $stmt = $pdo->prepare("
                INSERT INTO User (name, email, password_hash, role, registration_date)
                VALUES (?, ?, ?, 'user', NOW())
            ");
            $stmt->execute([$name, $email, $hashed_password]);

            $success = 'Registration succeed! Now you may login.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Registration</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="site-main">
        <!-- Шапка -->
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="products.php" class="nav-btn main">Shop</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="nav-btn auth">Login</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h2 style="text-align: center; margin: 30px 0; color: #2e2735;">Registration</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="login.php" class="btn btn-primary">Login</a>
                </div>
            <?php else: ?>
                <form method="POST" style="max-width: 500px; margin: 0 auto;">
                    <div class="form-group">
                        <label for="name">Name</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" name="email" id="email" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" name="password" id="password" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirm password</label>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">Register</button>
                </form>

                <p style="text-align: center; margin-top: 25px; font-size: 1rem;">
                    Already have an account? 
                    <a href="login.php" class="btn btn-secondary" style="display: inline-block; padding: 8px 16px; font-size: 14px; margin-left: 8px;">
                        Login
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div>
            <p>&copy; <?= date('Y') ?> Unicorns World. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 0.85rem;">
                We care about your privacy. 
                <a href="privacy.php">Privacy Policy</a>
            </p>
        </div>
    </footer>
</body>
</html>