<?php
include 'config.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart']['product'])) {
    $_SESSION['cart']['product'] = [];
}

if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT id, stock_quantity FROM `Product` WHERE id = ? AND stock_quantity > 0");
            $stmt->execute([$id]);
            if ($product = $stmt->fetch()) {
                if (!isset($_SESSION['cart']['product'][$id])) {
                    $_SESSION['cart']['product'][$id] = 1;
                } else {
                    if ($_SESSION['cart']['product'][$id] < $product['stock_quantity']) {
                        $_SESSION['cart']['product'][$id]++;
                    }
                }
                header('Location: cart.php?message=added');
                exit;
            }
        }
        header('Location: products.php?error=out_of_stock');
        exit;
    } elseif ($action === 'remove') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            unset($_SESSION['cart']['product'][$id]);
        }
        header('Location: cart.php?message=removed');
        exit;

    } elseif ($action === 'update') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $productId => $qty) {
                $productId = (int)$productId;
                $qty = (int)$qty;
                if ($productId > 0) {
                    if ($qty <= 0) {
                        unset($_SESSION['cart']['product'][$productId]);
                    } else {
                        $_SESSION['cart']['product'][$productId] = $qty;
                    }
                }
            }
        }
        header('Location: cart.php');
        exit;

    } elseif ($action === 'checkout') {
    // Проверка: есть ли товары
    if (empty($_SESSION['cart']['product'])) {
        header('Location: cart.php?error=empty');
        exit;
    }

    // Проверка: авторизован ли пользователь
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }

    $productIds = array_keys($_SESSION['cart']['product']);

    // Получаем данные товаров за один запрос
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, price, stock_quantity FROM `Product` WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $productData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $productData[$row['id']] = $row;
    }

    // Проверка остатков и расчёт итога
    $total = 0;
    foreach ($_SESSION['cart']['product'] as $id => $qty) {
        if (!isset($productData[$id])) {
            header('Location: cart.php?error=product_removed');
            exit;
        }
        if ($qty > $productData[$id]['stock_quantity']) {
            header('Location: cart.php?error=stock_changed');
            exit;
        }
        $subtotal = $productData[$id]['price'] * $qty;
        $total += $subtotal;
    }

    try {
        $pdo->beginTransaction();

        // Создаём заказ со статусом 'created'
        $stmt = $pdo->prepare("INSERT INTO `Order` (user_id, total_price, status, `date`) VALUES (?, ?, 'created', NOW())");
        $stmt->execute([$_SESSION['user_id'], $total]);
        $orderId = $pdo->lastInsertId();

        // Добавляем позиции заказа с полем subtotal
        foreach ($_SESSION['cart']['product'] as $id => $qty) {
            $price = $productData[$id]['price'];
            $subtotal = $price * $qty;
            $stmt = $pdo->prepare("INSERT INTO `OrderItem` (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $id, $qty, $subtotal]);
        }

        $pdo->commit();
        unset($_SESSION['cart']['product']); // Очищаем корзину
        header('Location: cart.php?message=order_created&id=' . $orderId);
        exit;

    } catch (PDOException $e) {
        $pdo->rollback();
        error_log("Error while creating order: " . $e->getMessage());
        header('Location: cart.php?error=order_failed');
        exit;
    }   
}
}
// === ОТОБРАЖЕНИЕ КОРЗИНЫ ===
$cartItems = [];
$total = 0;

if (!empty($_SESSION['cart']['product'])) {
    $productIds = array_keys($_SESSION['cart']['product']);
    $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
    $stmt = $pdo->prepare("SELECT id, name, image, price FROM `Product` WHERE id IN ($placeholders)");
    $stmt->execute($productIds);
    $productList = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($productList as $p) {
        $qty = $_SESSION['cart']['product'][$p['id']];
        $itemTotal = $p['price'] * $qty;
        $cartItems[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'image' => $p['image'],
            'price' => $p['price'],
            'quantity' => $qty,
            'total' => $itemTotal
        ];
        $total += $itemTotal;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f9f9f9;
        }
        h1 {
            color: #2c3e50;
        }
        .message {
            padding: 12px;
            margin: 15px 0;
            border-radius: 6px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: white;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
        input[type="number"] {
            width: 70px;
            padding: 6px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            font-weight: bold;
            text-align: center;
        }
        .btn-primary {
            background-color: #28a745;
            color: white;
        }
        .btn-danger {
            background-color: #dc3545;
            color: white;
        }
        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }
        .total-row {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .actions a {
            margin-right: 8px;
        }
    </style>
</head>
<body>

    <h1>Your shopping Cart</h1>

    <?php if (isset($_GET['message'])): ?>
        <?php if ($_GET['message'] === 'added'): ?>
            <div class="message success">The product has been added to the cart!</div>
        <?php elseif ($_GET['message'] === 'removed'): ?>
            <div class="message success">The product has been removed from the shopping cart.</div>
        <?php elseif ($_GET['message'] === 'order_created'): ?>
            <div class="message success">Order №<?= htmlspecialchars($_GET['id']) ?> successfully issued!</div>
        <?php endif; ?>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
        <div class="message error">
            <?php if ($_GET['error'] === 'empty'): ?>
                The shopping cart is empty.
            <?php elseif ($_GET['error'] === 'order_failed'): ?>
                Couldn't place an order. Try again later.
            <?php elseif ($_GET['error'] === 'not_found'): ?>
                The product was not found.
            <?php elseif ($_GET['error'] === 'out_of_stock'): ?>
                The product is out of stock.
            <?php elseif ($_GET['error'] === 'stock_changed'): ?>
                Some items have changed their balances. Check the shopping cart.
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>
        <p>The shopping cart is empty. <a href="products.php" class="btn btn-secondary">Go to the store</a></p>
    <?php else: ?>
        <form method="POST" action="cart.php?action=update">
            <table>
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                <?= htmlspecialchars($item['name']) ?>
                            </td>
                            <td><?= number_format($item['price'], 2, ',', ' ') ?> eur.</td>
                            <td>
                                <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" required>
                            </td>
                            <td><?= number_format($item['total'], 2, ',', ' ') ?> eur.</td>
                            <td class="actions">
                                <a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-danger" onclick="return confirm('Delete this product?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="total-row">
                        <td colspan="3" style="text-align: right;">Итого:</td>
                        <td><?= number_format($total, 2, ',', ' ') ?> eur.</td>
                        <td>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </form>

        <div style="text-align: center; margin-top: 20px;">
            <a href="products.php" class="btn btn-secondary">Continue shopping</a>
            <a href="cart.php?action=checkout" class="btn btn-primary" style="margin-left: 10px;" 
               onclick="return confirm('Place an order for <?= number_format($total, 2, ',', ' ') ?> eur?')">
                Place an order
            </a>
        </div>
    <?php endif; ?>

    <p style="margin-top: 30px;">
        <a href="dashboard.php">Back to account</a>
    </p>

</body>
</html>