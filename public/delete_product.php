<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !is_numeric($input['id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$id = (int)$input['id'];

try {
    // Проверяем, есть ли заказы с этим продуктом
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM OrderItem WHERE product_id = ?");
    $stmt->execute([$id]);
    $orderCount = $stmt->fetchColumn();

    if ($orderCount > 0) {
        echo json_encode(['error' => 'Cannot delete product: linked to ' . $orderCount . ' order(s)']);
        exit;
    }

    // Удаляем продукт
    $stmt = $pdo->prepare("DELETE FROM Product WHERE id = ?");
    $stmt->execute([$id]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    error_log("Delete product error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>