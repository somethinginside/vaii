<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <main class="site-main">

        <!-- Шапка -->
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
    
            <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'): ?>
                <a href="products.php" class="nav-btn main">Shop</a>
                <a href="unicorns.php" class="nav-btn main">Unicorns</a>
            <?php endif; ?>
    
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                    <a href="cart.php" class="nav-btn auth">Cart</a>
                <?php endif; ?>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <!-- Герой -->
        <section class="hero">
            <!-- Контейнер для единорога и текста -->
            <div class="scene-container">
                <!-- Анимация: бегущий единорог -->
                <img src="images/unicorn_start.gif" alt="Running unicorn" class="unicorn-gif">

                <!-- Текст поверх -->
                <div class="welcome-text">Welcome to the Magical World of Unicorns!</div>
            </div>
        </section>
    </main>

    <!-- Футер -->
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