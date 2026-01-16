<?php
$pageTitle = 'Shop - Unicorns World';
include 'config.php';

$stmt = $pdo->prepare("SELECT * FROM `Product` ORDER BY id DESC");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/header.html'; ?>

        <div class="container">
            <h1>Our Products</h1>

            <?php if (empty($products)): ?>
                <p>No products available.</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            <div class="product-info">
                                <h3><?= htmlspecialchars($p['name']) ?></h3>
                                <p><?= htmlspecialchars($p['description']) ?></p>
                                <p><strong><?= number_format($p['price'], 2, ',', ' ') ?> eur.</strong></p>
                                <a href="cart.php?action=add&id=<?= $p['id'] ?>" class="btn btn-primary">Add to Cart</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

<?php
$jsFile = 'js/main.js';
include 'templates/footer.html'; 
?>
