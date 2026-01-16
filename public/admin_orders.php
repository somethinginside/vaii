<?php
$pageTitle = 'Admin: Orders';
$isAdminPage = true;
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
<?php include 'templates/admin_header.html'; ?>

        <div class="container">
            <h2>Orders management</h2>

            <?php if (empty($orders)): ?>
                <p>No orders</p>
            <?php else: ?>
                <div style="overflow-x: auto; background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <table class='admin-table'>
                        <thead>
                            <tr>
                                <th>ID</th>
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

        <!-- Модальное окно -->
        <div id="order-modal" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
            <div class="modal-content" style="background:white; padding:25px; border-radius:14px; max-width:600px; width:90%; max-height:80vh; overflow:auto;">
                <h3>Order detalis <span id="order-id-placeholder"></span></h3>
                <div id="order-details-content"></div>
                <button id="close-modal" class="btn btn-secondary" style="margin-top:20px;">Close</button>
            </div>
        </div>

<?php
$jsFile = 'js/main.js';
$additionalJs = 'js/admin.js';
include 'templates/footer.html';
?>