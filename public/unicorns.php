<?php
include 'config.php';

// Получаем всех единорогов
$stmt = $pdo->prepare("
    SELECT u.*, a.name as admin_name 
    FROM Unicorn u 
    LEFT JOIN User a ON u.admin_id = a.id
    ORDER BY u.id DESC
");
$stmt->execute();
$unicorns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unicorns - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .unicorns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 25px;
            margin-top: 20px;
        }
        .unicorn-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: transform 0.3s ease;
        }
        .unicorn-card:hover {
            transform: translateY(-5px);
        }
        .unicorn-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }
        .unicorn-info {
            padding: 20px;
        }
        .unicorn-info h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #2e2735;
        }
        .detail {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            font-size: 0.95rem;
        }
        .label {
            font-weight: 600;
            color: #766288;
        }
        .value {
            color: #2e2735;
        }
        .admin-tag {
            display: inline-block;
            background: #f0e6f4;
            color: #766288;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            margin-top: 10px;
        }
    </style>
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="products.php" class="nav-btn main">Shop</a>
            <a href="unicorns.php" class="nav-btn main">Unicorns</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php" class="nav-btn auth">Cart</a>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h1 style="margin: 30px 0; color: #2e2735; text-align: center;">Unicorns</h1>

            <?php if (empty($unicorns)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735;">
                    There are no added unicorns yet.
                </p>
            <?php else: ?>
                <div class="unicorns-grid">
                    <?php foreach ($unicorns as $u): ?>
                        <div class="unicorn-card">
                            <img src="<?= htmlspecialchars($u['image']) ?>" alt="<?= htmlspecialchars($u['name']) ?>" class="unicorn-img">
                            <div class="unicorn-info">
                                <h3><?= htmlspecialchars($u['name']) ?></h3>
                                <div class="detail">
                                    <span class="label">Color:</span>
                                    <span class="value"><?= htmlspecialchars($u['color']) ?></span>
                                </div>
                                <div class="detail">
                                    <span class="label">Age:</span>
                                    <span class="value"><?= (int)$u['age'] ?> years</span>
                                </div>
                                <p style="margin: 12px 0; font-size: 0.95rem; color: #2e2735;">
                                    <?= htmlspecialchars($u['description']) ?>
                                </p>
                                <?php if (!empty($u['admin_name'])): ?>
                                    <div class="admin-tag">Added by: <?= htmlspecialchars($u['admin_name']) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
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