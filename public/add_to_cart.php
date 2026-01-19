<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['product_id']) || !isset($input['quantity'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid input']);
    exit;
}

$productId = (int)$input['product_id'];
$quantity = (int)$input['quantity'];

if ($quantity <= 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid quantity']);
    exit;
}

//error_log("Looking for product ID: $productID");

// ? Проверяем, есть ли товар в базе
$stmt = $pdo->prepare("SELECT id, stock_quantity FROM Product WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch();

//error_log("Product Found:" . var_export($product, true));

if (!$product) {
    http_response_code(400);
    echo json_encode(['error' => 'Product not found']);
    exit;
}

if ($quantity > $product['stock_quantity']) {
    http_response_code(400);
    echo json_encode(['error' => 'Not enough stock']);
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ? Обновляем количество
if (isset($_SESSION['cart'][$productId])) {
    $newQuantity = $_SESSION['cart'][$productId] + $quantity;
    if ($newQuantity > $product['stock_quantity']) {
        http_response_code(400);
        echo json_encode(['error' => 'Not enough stock for total quantity']);
        exit;
    }
    $_SESSION['cart'][$productId] = $newQuantity;
} else {
    $_SESSION['cart'][$productId] = $quantity;
}

echo json_encode(['success' => true]);
?>