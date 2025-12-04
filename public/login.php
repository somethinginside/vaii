<?php
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Enter email and password!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Успешный вход
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Перенаправляем на личный кабинет
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Wrong email or password!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    
</head>
<body>
    <main class="site-main">
        <!-- Шапка -->
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="products.php" class="nav-btn main">Shop</a>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h2 style="text-align: center; margin: 30px 0; color: #2e2735;">Login</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" style="max-width: 500px; margin: 0 auto;">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Login</button>
            </form>

            <p style="text-align: center; margin-top: 20px;">
                Do not have an account? <a href="register.php" class="btn btn-secondary" style="display: inline-block; padding: 8px 16px; font-size: 14px;">Register</a>
            </p>
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