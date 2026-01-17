<?php
$pageTitle = 'Admin: Orders';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name 
    FROM `Order` o
    JOIN User u ON o.user_id = u.id
    ORDER BY o.date DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

$availableStatuses = [
    'created' => 'Created',
    'shipped' => 'Shipped',
    'ready' => 'Ready to collection',
    'done' => 'Done'
];

include 'templates/admin_header.html';
include 'templates/admin_orders.html';
$jsFile = 'js/main.js';
$additionalJs = 'js/admin_orders.js';
include 'templates/footer.html';
?>