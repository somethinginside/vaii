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

$input = json_decode(file_get_contents('php://input'), true);
$productId = (int)($input['id'] ?? 0);

if ($productId <= 0) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

try {
    // Удаляем связанные записи в OrderItem
    $stmt = $pdo->prepare("DELETE FROM OrderItem WHERE product_id = ?");
    $stmt->execute([$productId]);

    // Удаляем сам товар
    $stmt = $pdo->prepare("DELETE FROM `Product` WHERE id = ?");
    $count = $stmt->execute([$productId]);

    if ($count) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Product did not found']);
    }
} catch (PDOException $e) {
    error_log("Delete product error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>