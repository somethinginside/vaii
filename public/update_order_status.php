<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}

$orderId = (int)($_POST['order_id'] ?? 0);
$newStatus = $_POST['status'] ?? '';

$availableStatuses = ['created', 'shipped', 'ready to ñollection', 'done'];

if ($orderId <= 0 || !in_array($newStatus, $availableStatuses)) {
    header('Location: admin_orders.php?error=invalid');
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE `Order` SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);
    header('Location: admin_orders.php?message=updated');
    exit;
} catch (PDOException $e) {
    header('Location: admin_orders.php?error=db');
    exit;
}
?>