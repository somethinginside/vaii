<?php
include 'config.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
$orders = [];
if ($user['role'] !== 'admin') {
    $stmt = $pdo->prepare("SELECT * FROM `Order` WHERE user_id = ? ORDER BY `date` DESC");
    $stmt->execute([$user['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">    
</head>
<body>
    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            
            <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                <a href="products.php" class="nav-btn main">Shop</a>
                <a href="unicorns.php" class="nav-btn main">Unicorns</a>
                <a href="cart.php" class="nav-btn auth">Cart</a>
            <?php endif; ?>
            
            <a href="dashboard.php" class="nav-btn auth">Account</a>
            <a href="logout.php" class="nav-btn auth">Logout</a>

        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735;">Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>

            <div style="background: white; padding: 25px; border-radius: 14px; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                <h3 style="margin-bottom: 15px; color: #766288;">Your data</h3>
                <p><strong>Name:</strong> <?=htmlspecialchars($user['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
                <p><strong>Date of registration</strong> <?= $user['registration_date'] ?></p>
            </div>

            <?php if ($user['role'] !== 'admin'): ?>

                <h3 style="margin: 30px 0 20px; color: #2e2735;">Your orders</h3>
                <?php if (count($orders) > 0): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Sum</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $order['date'] ?></td>
                                    <td><?= number_format($order['total_price'], 2, ',', ' ') ?> eur.</td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'created' => 'Created',
                                            'shipped' => 'Shipped',
                                            'ready to сollection' => 'Ready to collection',
                                            'done' => 'Done'
                                        ];
                                        echo $statusLabels[$order['status']] ?? $order['status'];
                                        ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>You do not have any orders.</p>
                <?php endif; ?>
            <?php endif; ?>
            <?php if ($user['role'] === 'admin'): ?>
                <div style="margin-top: 30px;">
                    <h3 style="color: #2e2735;">Admins functions</h3>
                    <a href="admin_unicorns.php" class="btn btn-secondary">unicorns management</a>
                    <a href="admin_products.php" class="btn btn-secondary">product management</a>
                    <a href="admin_orders.php" class="btn btn-secondary">check orders</a>
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