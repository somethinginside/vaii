<?php
$pageTitle = 'Admin: Orders — Unicorns World';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name 
    FROM `Order` o 
    LEFT JOIN User u ON o.user_id = u.id AND u.status != 'deleted'
    ORDER BY o.id DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/admin_orders.html';

$additionalJs = 'js/admin_orders.js';
include 'templates/footer.html';
?>