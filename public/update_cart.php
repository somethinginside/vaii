<?php
include 'config.php';

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

if ($quantity < 0) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid quantity']);
    exit;
}

if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($quantity === 0) {
    unset($_SESSION['cart'][$productId]);
} else {
    $_SESSION['cart'][$productId] = $quantity;
}

echo json_encode(['success' => true]);
?>