<?php
$pageTitle = 'Admin: products';
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied');
}

$stmt = $pdo->query("SELECT * FROM `Product` ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/admin_header.html'; ?>

        <div class="container">
            <h2>Products management</h2>
            <a href="add_product.php" class="btn btn-primary" style="margin-bottom: 20px;">Add product</a>

            <?php if (empty($products)): ?>
                <p>No products added.</p>
            <?php else: ?>
                <table class='admin-table'>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr data-id="<?= $p['id'] ?>" class="data-row">
                                <td><?= htmlspecialchars($p['id']) ?></td>
                                <td class="field" data-field="name"><?= htmlspecialchars($p['name']) ?></td>
                                <td class="field" data-field="category"><?= htmlspecialchars($p['category']) ?></td>
                                <td class="field" data-field="description"><?= htmlspecialchars($p['description']) ?></td>
                                <td class="field" data-field="price"><?= number_format($p['price'], 2, ',', ' ') ?></td>
                                <td class="field" data-field="stock_quantity"><?= (int)$p['stock_quantity'] ?></td>
                                <td>
                                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                                    <span class="field" data-field="image" style="display:none;"><?= htmlspecialchars($p['image']) ?></span>
                                </td>
                                <td>
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">Edit</a>
                                    <button class="btn btn-danger btn-sm delete-product-btn">Delete</button>
                                    <div class="status"></div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
<?php echo 'Reached footer';?>
<?php
$jsFile = 'js/main.js';
$additionalJs = 'js/admin.js';
include 'templates/footer.html';
?>