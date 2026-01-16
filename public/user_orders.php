<?php
$pageTitle = 'My Orders — Unicorns World';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT o.*, 
           COUNT(oi.product_id) as items_count
    FROM `Order` o
    LEFT JOIN `OrderItem` oi ON o.id = oi.order_id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.date DESC
");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/user_orders.html';
include 'templates/footer.html';
?>