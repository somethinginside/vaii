<?php
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied']);
    exit;
}

$count = 0;
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $count = array_sum($_SESSION['cart']); // Сумма всех товаров
}

echo json_encode(['success' => true, 'count' => $count]);
?>