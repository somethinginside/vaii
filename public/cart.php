<?php
$pageTitle = 'Cart';
include 'config.php';
include 'auth_check.php';
// ✅ Проверяем, вошёл ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// ✅ Получаем товары в корзине
$cartItems = [];
if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    if (!empty($productIds)) {
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM Product WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $cartItems[] = [
                'product' => $product,
                'quantity' => $_SESSION['cart'][$product['id']]
            ];
        }
    }
}

include 'templates/header.html';
include 'templates/cart.html';
include 'templates/footer.html';
?>