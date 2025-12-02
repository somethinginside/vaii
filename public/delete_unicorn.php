<?php
include 'config.php';

// Только для админов
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

// Только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id']) || !is_numeric($input['id'])) {
    echo json_encode(['error' => 'Invalid ID']);
    exit;
}

$unicorn_id = (int)$input['id'];

try {
    // Сначала удалим связанные записи в OrderItem (если есть)
    $stmt = $pdo->prepare("DELETE FROM OrderItem WHERE product_id = ?");
    $stmt->execute([$unicorn_id]);

    // Теперь удалим самого единорога
    $stmt = $pdo->prepare("DELETE FROM `Unicorn` WHERE id = ?");
    $stmt->execute([$unicorn_id]);

    echo json_encode(['success' => true, 'message' => 'Deleted']);
} catch (PDOException $e) {
    error_log("Delete error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>