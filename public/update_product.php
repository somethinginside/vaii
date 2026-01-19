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

$fields = ['name', 'price', 'stock_quantity', 'category', 'description', 'image'];
foreach ($fields as $field) {
    if (!isset($input[$field])) {
        http_response_code(400);
        echo json_encode(['error' => "Missing field: $field"]);
        exit;
    }
}

$id = (int)$input['id'];
$name = trim($input['name']);
$price = (float)$input['price'];
$stockQuantity = (int)$input['stock_quantity'];
$category = trim($input['category']);
$description = trim($input['description']);
$image = trim($input['image']);

if (empty($name) || $price < 0 || $stockQuantity < 0 || empty($category) || empty($description) || empty($image)) {
    http_response_code(400);
    echo json_encode(['error' => 'All fields required']);
    exit;
}

try {
    $stmt = $pdo->prepare("UPDATE Product SET name = ?, price = ?, stock_quantity = ?, category = ?, description = ?, image = ? WHERE id = ?");
    $stmt->execute([$name, $price, $stockQuantity, $category, $description, $image, $id]);

    // ✅ Проверяем, существует ли продукт до обновления
    $checkStmt = $pdo->prepare("SELECT id FROM Product WHERE id = ?");
    $checkStmt->execute([$id]);
    $exists = $checkStmt->fetch();

    if ($exists) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Product not found']);
    }
} catch (PDOException $e) {
    error_log("Update product error: " . $e->getMessage());
    echo json_encode(['error' => 'Database error']);
}
?>