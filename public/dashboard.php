<?php
include 'config.php';

// Проверяем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 50px; }
        .header { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .user-info { background: #f0f0f0; padding: 20px; border-radius: 8px; }
        .menu a { margin-right: 15px; text-decoration: none; color: blue; }
    </style>
</head>
<body>
    <div class="header">
        <h2>Welcome, <?= htmlspecialchars($user['name']) ?>!</h2>
        <div class="menu">
            <a href="dashboard.php">Account</a>
            <a href="logout.php">Logout</a>
        </div>
    </div>

    <div class="user-info">
        <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
        <p><strong>Role:</strong> <?= htmlspecialchars($user['role']) ?></p>
        <p><strong>Registration date:</strong> <?= $user['registration_date'] ?></p>
    </div>

    <!-- Пример: показать заказы пользователя -->
    <?php
    $stmt = $pdo->prepare("SELECT * FROM `Order` WHERE user_id = ? ORDER BY `date` DESC");
    $stmt->execute([$user['id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <h3>Your orders:</h3>
    <?php if (count($orders) > 0): ?>
        <ul>
        <?php foreach ($orders as $order): ?>
            <li>Order №<?= $order['id'] ?> — <?= $order['total_price'] ?> eur. (<?= $order['status'] ?>)</li>
        <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>You have no orders.</p>
    <?php endif; ?>

    <!-- Для администратора — дополнительные ссылки -->
    <?php if ($user['role'] === 'admin'): ?>
        <h3>Administrative functions</h3>
        <ul>
            <li><a href="#">Product management</a></li>
            <li><a href="#">Check all orders</a></li>
            <li><a href="admin_unicorns.php">Unicorn management</a></li>
        </ul>
    <?php endif; ?>
</body>
</html>