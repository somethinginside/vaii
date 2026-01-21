<?php
$pageTitle = 'Checkout — Unicorns World';
include 'config.php';
include 'auth_check.php';


// Проверяем, вошёл ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

//Получаем товары в корзине
$cartItems = [];
$total = 0;

if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    if (!empty($productIds)) {
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM `Product` WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($products as $product) {
            $quantity = $_SESSION['cart'][$product['id']];
            $subtotal = $product['price'] * $quantity;
            
            $cartItems[] = [
                'product' => $product,
                'quantity' => $quantity,
                'subtotal' => $subtotal
            ];
            
            $total += $subtotal;
        }
    }
}

// Обработка формы
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($cartItems)) {
        $error = 'Your cart is empty.';
    } else {
        try {
            //Начинаем транзакцию
            $pdo->beginTransaction();

            // Создаём заказ
            $shipping_address = trim($_POST['shipping_address']);
            $phone = trim($_POST['phone']);

            // Валидация (минимум)
            if (empty($shipping_address) || empty($phone)) {
                // ошибка
            }

            $stmt = $pdo->prepare("
                INSERT INTO `Order` (user_id, total_price, shipping_address, phone, date)
                VALUES (?, ?, ?, ?, NOW())
            ");
            $stmt->execute([
                $_SESSION['user_id'],
                $total,
                $shipping_address,
                $phone
            ]);

            $orderId = $pdo->lastInsertId();

            // Добавляем товары в OrderItem
            foreach ($cartItems as $item) {
                $stmt = $pdo->prepare("INSERT INTO OrderItem (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $item['product']['id'], $item['quantity'], $item['subtotal']]);
                
                //Обновляем остатки
                $stmt = $pdo->prepare("UPDATE Product SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product']['id']]);
            }

            //  Очищаем корзину
            unset($_SESSION['cart']);

            //  Коммитим транзакцию
            $pdo->commit();

            $message = 'Order placed successfully! Your order ID is #' . $orderId;
            $cartItems = []; //  Очищаем для отображения
            $total = 0;

        } catch (Exception $e) {
            // Откатываем транзакцию
            $pdo->rollback();
            $error = 'Error placing order: ' . $e->getMessage();
        }
    }
}

include 'templates/header.html';
include 'templates/checkout.html';
include 'templates/footer.html';
?>