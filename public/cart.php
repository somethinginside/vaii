<?php
include 'config.php';

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

        // Проверка остатков
        foreach ($_SESSION['cart']['product'] as $id => $qty) {
            if (!isset($productData[$id]) || $qty > $productData[$id]['stock_quantity']) {
                header('Location: cart.php?error=stock_changed');
                exit;
            }
        }

        // Расчёт итога
        $total = 0;
        foreach ($_SESSION['cart']['product'] as $id => $qty) {
            $total += $productData[$id]['price'] * $qty;
        }

        try {
            $pdo->beginTransaction();
            $stmt = $pdo->prepare("INSERT INTO `Order` (user_id, total_price, status, `date`) VALUES (?, ?, 'created', NOW())");
            $stmt->execute([$_SESSION['user_id'], $total]);
            $orderId = $pdo->lastInsertId();

            foreach ($_SESSION['cart']['product'] as $id => $qty) {
                $subtotal = $productData[$id]['price'] * $qty;
                $stmt = $pdo->prepare("INSERT INTO `OrderItem` (order_id, product_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
                $stmt->execute([$orderId, $id, $qty, $subtotal]);
            }

            $pdo->commit();
            unset($_SESSION['cart']['product']);
            header('Location: cart.php?message=order_created&id=' . $orderId);
            exit;

        } catch (PDOException $e) {
            $pdo->rollback();
            error_log("Order error: " . $e->getMessage());
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
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .cart-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        .cart-table th, .cart-table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        .cart-table th {
            background: #f0e6f4;
            color: #766288;
        }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
        }
        .total-row {
            font-weight: bold;
            background: #f9f5fb;
        }
        .message {
            padding: 12px;
            margin: 20px 0;
            border-radius: 8px;
        }
        .message.success { background: #d4edda; color: #155724; }
        .message.error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="products.php" class="nav-btn main">Shop</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="cart.php" class="nav-btn auth">Cart</a>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h1 style="margin: 30px 0; color: #2e2735; text-align: center;">Your cart</h1>

            <?php if (isset($_GET['message'])): ?>
                <?php if ($_GET['message'] === 'added'): ?>
                    <div class="message success">Product has been added to cart!</div>
                <?php elseif ($_GET['message'] === 'removed'): ?>
                    <div class="message success">Product has been deleted</div>
                <?php elseif ($_GET['message'] === 'order_created'): ?>
                    <div class="message success">Order n.<?= htmlspecialchars($_GET['id']) ?> successfully issued!</div>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="message error">
                    <?php if ($_GET['error'] === 'empty'): ?>
                        Cart is empty
                    <?php elseif ($_GET['error'] === 'order_failed'): ?>
                        Error while creating an order
                    <?php elseif ($_GET['error'] === 'out_of_stock'): ?>
                        The product is out of stock.
                    <?php elseif ($_GET['error'] === 'stock_changed'): ?>
                        Some items have changed their balances. Check the shopping cart.
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735; margin: 30px 0;">
                    Your cart is empty<a href="products.php" class="btn btn-secondary" style="margin-left: 10px;">Go to shop</a>
                </p>
            <?php else: ?>
                <form method="POST" action="cart.php?action=update">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Total</th>
                                <th>Actions</th>
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
                                           onclick="return confirm('Delete?')">Delete</a>
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
                    <a href="products.php" class="btn btn-secondary" style="margin-right: 15px;">Continue shopping</a>
                    <a href="cart.php?action=checkout" class="btn btn-primary" 
                       onclick="return confirm('Place an order for <?= number_format($total, 2, ',', ' ') ?> eur.?')">
                        Place an order
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <footer class="site-footer">
        <div>
            <p>&copy; <?= date('Y') ?> Unicorns World. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 0.85rem;">
                We care about your privacy. 
                <a href="privacy.php">Privacy Policy</a>
            </p>
        </div>
    </footer>

</body>
</html>