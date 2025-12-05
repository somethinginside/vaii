<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$orderId = (int)($_GET['id'] ?? 0);

if ($orderId <= 0) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit;
}

// Получаем товары из OrderItem
$stmt = $pdo->prepare("
    SELECT oi.*, p.name 
    FROM OrderItem oi
    JOIN Product p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);

$result = [];
foreach ($items as $item) {
    $result[] = [
        'name' => htmlspecialchars($item['name']),
        'quantity' => $item['quantity'],
        'subtotal' => number_format($item['subtotal'], 2, ',', ' ')
    ];
}

echo json_encode(['items' => $result]);
?>