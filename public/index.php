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
            <a href="products.php" class="nav-btn main">Shop</a>
            <a href="#about" class="nav-btn main">About</a>
            <a href="#contact" class="nav-btn main">Contact</a>
        
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php
                $cartCount = 0;
                if (!empty($_SESSION['cart']['product'])) {
                    $cartCount = array_sum($_SESSION['cart']['product']);
                }
                ?>
                <a href="cart.php" class="nav-btn auth">
                    Cart<?php if ($cartCount > 0): ?> (<?= $cartCount ?>)<?php endif; ?>
                </a>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <!-- Контент -->
        <div class="container">
            <div class="hero" style="text-align: center; padding: 60px 0;">
                <h1 style="font-size: 2.8rem; margin-bottom: 20px;">Discover Magical Unicorns</h1>
                <p style="font-size: 1.2rem; margin-bottom: 30px; opacity: 0.9;">
                    Explore our enchanted collection of accessories, gifts, and wonders for your mythical friends.
                </p>
                <div class="hero-buttons">
                    <a href="products.php" class="btn btn-primary">Shop Now</a>
                    <a href="#about" class="btn btn-secondary">Learn More</a>
                </div>
            </div>
        </div>
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