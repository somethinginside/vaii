<?php
include 'config.php';

// Только для админов
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Only for admins');
}

// Получаем все заказы с именем пользователя
$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name 
    FROM `Order` o
    JOIN User u ON o.user_id = u.id
    ORDER BY o.date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Возможные статусы
$availableStatuses = [
    'created' => 'Created',
    'shipped' => 'Shipped',
    'ready to сollection' => 'Ready to collection',
    'done' => 'Done'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: orders - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status-select {
            padding: 6px;
            border-radius: 4px;
            border: 1px solid #ccc;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735; text-align: center;">Orders management</h2>

            <?php if (empty($orders)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735;">No orders</p>
            <?php else: ?>
                <div style="overflow-x: auto; background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <table>
                        <thead>
                            <tr>
                                <th>N.</th>
                                <th>User</th>
                                <th>Date</th>
                                <th>Sum</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?= $order['id'] ?></td>
                                    <td><?= htmlspecialchars($order['user_name']) ?></td>
                                    <td><?= $order['date'] ?></td>
                                    <td><?= number_format($order['total_price'], 2, ',', ' ') ?> eur.</td>
                                    <td>
                                        <span class="status-<?= $order['status'] ?>">
                                            <?= $availableStatuses[$order['status']] ?? $order['status'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <!-- Форма смены статуса -->
                                        <form method="POST" action="update_order_status.php" style="display:inline;" onsubmit="return confirm('Chnage status?')">
                                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                            <select name="status" class="status-select">
                                                <?php foreach ($availableStatuses as $key => $label): ?>
                                                    <option value="<?= $key ?>" <?= $key === $order['status'] ? 'selected' : '' ?>>
                                                        <?= $label ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <button type="submit" class="btn btn-secondary btn-sm">Submit</button>
                                        </form>
                                        <!-- Кнопка "Детали" -->
                                        <button class="btn btn-secondary btn-sm view-details-btn" data-order-id="<?= $order['id'] ?>">Details</button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
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
<!-- Модальное окно -->
<div id="order-modal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
    <div class="modal-content" style="background:white; padding:25px; border-radius:14px; max-width:600px; width:90%; max-height:80vh; overflow:auto;">
        <h3 style="margin-top:0;">Order detalis <span id="order-id-placeholder"></span></h3>
        <div id="order-details-content"></div>
        <button id="close-modal" class="btn btn-secondary" style="margin-top:20px;">Close</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Кнопки "Детали"
    document.querySelectorAll('.view-details-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const orderId = this.dataset.orderId;
            const modal = document.getElementById('order-modal');
            const content = document.getElementById('order-details-content');
            const idPlaceholder = document.getElementById('order-id-placeholder');

            idPlaceholder.textContent = orderId;
            content.textContent = 'Loading...';
            modal.style.display = 'flex';

            try {
                const res = await fetch('get_order_items.php?id=' + orderId);
                const data = await res.json();

                if (data.error) {
                    content.textContent = 'Error: ' + data.error;
                } else if (data.items && data.items.length > 0) {
                    let html = '<table style="width:100%; border-collapse:collapse; margin-top:15px;">';
                    html += '<thead><tr><th>Product</th><th>Amount</th><th>Total</th></tr></thead><tbody>';
                    data.items.forEach(item => {
                        html += `<tr>
                            <td>${item.name}</td>
                            <td>${item.quantity}</td>
                            <td>${item.subtotal} eur.</td>
                        </tr>`;
                    });
                    html += '</tbody></table>';
                    content.innerHTML = html;
                } else {
                    content.textContent = 'No products in order';
                }
            } catch (err) {
                content.textContent = 'Error while loading.';
            }
        });
    });

    // Закрытие модального окна
    document.getElementById('close-modal').addEventListener('click', function() {
        document.getElementById('order-modal').style.display = 'none';
    });
});
</script>
</body>
</html>