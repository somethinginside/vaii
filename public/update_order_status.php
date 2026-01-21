<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !isset($input['status'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$orderId = (int)$input['id'];
$newStatus = trim($input['status']);

//  Проверяем, что статус допустим
$allowedStatuses = ['created', 'shipped', 'ready', 'done'];
if (!in_array($newStatus, $allowedStatuses)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid status']);
    exit;
}

try {
    //  Проверим, существует ли заказ
    $checkStmt = $pdo->prepare("SELECT id FROM `Order` WHERE id = ?");
    $checkStmt->execute([$orderId]);
    $orderExists = $checkStmt->fetch();

    if (!$orderExists) {
        http_response_code(400);
        echo json_encode(['error' => 'Order not found']);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE `Order` SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $orderId]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Order not found']);
    }
} catch (PDOException $e) {
    error_log("Update order status error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>