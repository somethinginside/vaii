<?php
$pageTitle = 'Cart-Unicorns World';
include 'config.php';

// Админ не может заходить в корзину
if (isset($_SESSION['user_id']) && $_SESSION['user_role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Инициализация корзины
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['cart']['product'])) {
    $_SESSION['cart']['product'] = [];
}

// === ОБРАБОТКА ДЕЙСТВИЙ ===
if (isset($_GET['action'])) {
    $action = $_GET['action'];

    if ($action === 'add') {
        $id = (int)($_GET['id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("SELECT id, stock_quantity FROM `Product` WHERE id = ? AND stock_quantity > 0");
            $stmt->execute([$id]);
            if ($product = $stmt->fetch()) {
                $currentQty = $_SESSION['cart']['product'][$id] ?? 0;
                $newQty = $currentQty + 1;
                
                if ($newQty > $product['stock_quantity']) {
                    header('Location: products.php?error=stock_limit');
                    exit;
                }
                
                $_SESSION['cart']['product'][$id] = $newQty;
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
                
                if ($productId > 0 && $qty > 0) {
                    $stmt = $pdo->prepare("SELECT stock_quantity FROM `Product` WHERE id = ?");
                    $stmt->execute([$productId]);
                    $stock = (int)($stmt->fetchColumn() ?? 0);
                    
                    if ($stock > 0) {
                        $_SESSION['cart']['product'][$productId] = min($qty, $stock);
                    } else {
                        unset($_SESSION['cart']['product'][$productId]);
                    }
                } else {
                    unset($_SESSION['cart']['product'][$productId]);
                }
            }
        }
        header('Location: cart.php');
        exit;

    } elseif ($action === 'checkout') {
        if (empty($_SESSION['cart']['product'])) {
            header('Location: cart.php?error=empty');
            exit;
        }
        if (!isset($_SESSION['user_id'])) {
            header('Location: login.php');
            exit;
        }

        $productIds = array_keys($_SESSION['cart']['product']);
        $placeholders = str_repeat('?,', count($productIds) - 1) . '?';
        $stmt = $pdo->prepare("SELECT id, name, price, stock_quantity FROM `Product` WHERE id IN ($placeholders)");
        $stmt->execute($productIds);
        $productData = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $productData[$row['id']] = $row;
        }

        // Проверяем, что все товары есть в наличии
        foreach ($_SESSION['cart']['product'] as $id => $qty) {
            if (!isset($productData[$id]) || $qty > $productData[$id]['stock_quantity']) {
                header('Location: cart.php?error=stock_changed');
                exit;
            }
        }

        $total = 0;
        foreach ($_SESSION['cart']['product'] as $id => $qty) {
            $total += $productData[$id]['price'] * $qty;
        }

        try {
            $pdo->beginTransaction();

            // Используем CURRENT_TIMESTAMP или NOW() в зависимости от версии MySQL
            $stmt = $pdo->prepare("INSERT INTO `Order` (user_id, total_price, status, date) VALUES (?, ?, 'created', CURRENT_TIMESTAMP)");
            $stmt->execute([$_SESSION['user_id'], $total]);
            $orderId = $pdo->lastInsertId();

            foreach ($_SESSION['cart']['product'] as $id => $qty) {
                $subtotal = $productData[$id]['price'] * $qty;
                $stmt = $pdo->prepare("INSERT INTO `OrderItem` (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $id, $qty, $subtotal]);
            }

            // Уменьшаем количество на складе
            foreach ($_SESSION['cart']['product'] as $id => $qty) {
                $stmt = $pdo->prepare("UPDATE `Product` SET stock_quantity = stock_quantity - ? WHERE id = ?");
                $stmt->execute([$qty, $id]);
            }

            $pdo->commit();
            unset($_SESSION['cart']['product']);
            header('Location: cart.php?message=order_created&id=' . $orderId);
            exit;

        } catch (PDOException $e) {
            $pdo->rollback();
            error_log("Order creation error: " . $e->getMessage()); // Записываем ошибку в лог
            header('Location: cart.php?error=order_failed');
            exit;
        }
    }
}

// === ДАННЫЕ ДЛЯ ОТОБРАЖЕНИЯ ===
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

<?php include 'templates/header.html'; ?>

        <div class="container">
            <h1>Your Cart</h1>

            <?php if (isset($_GET['message'])): ?>
                <?php if ($_GET['message'] === 'added'): ?>
                    <div class="message success">Item added to cart!</div>
                <?php elseif ($_GET['message'] === 'removed'): ?>
                    <div class="message success">Item removed.</div>
                <?php elseif ($_GET['message'] === 'order_created'): ?>
                    <div class="message success">Order #<?= htmlspecialchars($_GET['id']) ?> created successfully!</div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="message error">
                    <?php if ($_GET['error'] === 'empty'): ?>
                        Cart is empty.
                    <?php elseif ($_GET['error'] === 'order_failed'): ?>
                        Error creating order.
                    <?php elseif ($_GET['error'] === 'out_of_stock'): ?>
                        Item out of stock.
                    <?php elseif ($_GET['error'] === 'stock_changed'): ?>
                        Some items changed stock. Please review cart.
                    <?php elseif ($_GET['error'] === 'stock_limit'): ?>
                        Cannot add more than available in stock.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735; margin: 30px 0;">
                    Your cart is empty. <a href="products.php" class="btn btn-secondary" style="margin-left: 10px;">Go to Shop</a>
                </p>
            <?php else: ?>
                <form method="POST" action="cart.php?action=update">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="product-img">
                                        <?= htmlspecialchars($item['name']) ?>
                                    </td>
                                    <td><?= number_format($item['price'], 2, ',', ' ') ?> eur.</td>
                                    <td>
                                        <input type="number" name="quantities[<?= $item['id'] ?>]" value="<?= $item['quantity'] ?>" min="1" style="width: 70px; padding: 6px; border: 1px solid #ccc; border-radius: 4px;">
                                    </td>
                                    <td><?= number_format($item['total'], 2, ',', ' ') ?> eur.</td>
                                    <td>
                                        <a href="cart.php?action=remove&id=<?= $item['id'] ?>" class="btn btn-secondary btn-sm" 
                                           onclick="return confirm('Remove?')">Remove</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="total-row">
                                <td colspan="3" style="text-align: right;">Total:</td>
                                <td><?= number_format($total, 2, ',', ' ') ?> eur.</td>
                                <td>
                                    <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 8px 16px;">Update</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </form>

                <div style="text-align: center; margin: 30px 0;">
                    <a href="products.php" class="btn btn-secondary" style="margin-right: 15px;">Continue Shopping</a>
                    <a href="cart.php?action=checkout" class="btn btn-primary" 
                       onclick="return confirm('Place order for <?= number_format($total, 2, ',', ' ') ?> eur.?')">
                        Checkout
                    </a>
                </div>
            <?php endif; ?>
        </div>

<?php
$jsFile = 'js/cart.js';
include 'templates/footer.html';
?>
