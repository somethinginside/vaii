<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy policy — Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main class="site-main">
        <!-- Øàïêà -->
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="products.php" class="nav-btn main">Shop</a>
            <a href="privacy.php" class="nav-btn auth">Privacy Policy</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h1 style="margin: 30px 0; color: #2e2735; text-align: center;">Privacy policy</h1>
            
            <div style="background: white; padding: 30px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
                <p style="margin-bottom: 20px; font-size: 1.05rem;">
                    <strong>Unicorns World</strong> respects your privacy and undertakes to protect your personal data.
                </p>

                <h2 style="color: #766288; margin: 25px 0 15px;">1. What data do we collect?</h2>
                <p style="margin-bottom: 15px;">
                    When registering and using the site, we may collect:
                </p>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li>Name</li>
                    <li>Email</li>
                    <li>Information about orders</li>
                    <li>Session data (cookies)</li>
                </ul>

                <h2 style="color: #766288; margin: 25px 0 15px;">2. What do we use your data for?</h2>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li>For registration and authorization</li>
                    <li>Order processing</li>
                    <li>Website improvements</li>
                    <li>Compliance with the law</li>
                </ul>

                <h2 style="color: #766288; margin: 25px 0 15px;">3. Data protection</h2>
                <p style="margin-bottom: 20px;">
                    All personal data is stored in a secure database and is not shared with third parties without your consent, except as required by law.
                </p>

                <h2 style="color: #766288; margin: 25px 0 15px;">4. Your rights</h2>
                <p style="margin-bottom: 15px;">
                    You have the right:
                </p>
                <ul style="margin-left: 20px; margin-bottom: 20px;">
                    <li>Get information about what data is stored about you</li>
                    <li>Correct inaccurate data</li>
                    <li>Request the deletion of your data</li>
                    <li>Revoke consent to processing</li>
                </ul>

                <h2 style="color: #766288; margin: 25px 0 15px;">5. Contacts</h2>
                <p>
                    For privacy issues, please contact:
                    <strong>privacy@unicornsworld.local</strong>
                </p>
            </div>

            <div style="text-align: center; margin-bottom: 30px;">
                <a href="index.php" class="btn btn-secondary">Bcak to main page</a>
            </div>
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