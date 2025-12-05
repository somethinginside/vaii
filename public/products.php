<?php
include 'config.php';

// Получаем категории
$stmt = $pdo->query("SELECT DISTINCT category FROM `Product` WHERE category != '' ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Параметры фильтрации
$selectedCategory = trim($_GET['category'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$searchQuery = trim($_GET['search'] ?? '');

// Строим запрос
$sql = "SELECT * FROM `Product` WHERE stock_quantity > 0";
$params = [];

if ($selectedCategory !== '') {
    $sql .= " AND category = ?";
    $params[] = $selectedCategory;
}
if ($searchQuery !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $searchQuery . '%';
}
if (is_numeric($minPrice)) {
    $sql .= " AND price >= ?";
    $params[] = (float)$minPrice;
}
if (is_numeric($maxPrice)) {
    $sql .= " AND price <= ?";
    $params[] = (float)$maxPrice;
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .filters {
            background: white;
            padding: 20px;
            border-radius: 14px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        .filters label {
            display: inline-block;
            width: 100px;
            margin-right: 10px;
            font-weight: 600;
        }
        .filters input, .filters select {
            padding: 8px;
            margin-right: 15px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        .clear-filters {
            margin-left: 15px;
            font-size: 14px;
        }
        .product-card {
            text-align: center;
        }
        .product-img {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: 10px;
        }
        .category-tag {
            display: inline-block;
            background: #f0e6f4;
            color: #766288;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 0.9rem;
            margin-bottom: 10px;
        }
        .stock-info {
            font-size: 0.9rem;
            color: #2e2735;
            opacity: 0.8;
            margin: 8px 0;
        }
        .out-of-stock {
            color: #e74c3c;
            font-weight: bold;
        }
    </style>
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
    
            <?php if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin'): ?>
                <a href="products.php" class="nav-btn main">Shop</a>
                <a href="unicorns.php" class="nav-btn main">Unicorns</a>
            <?php endif; ?>
    
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] !== 'admin'): ?>
                    <a href="cart.php" class="nav-btn auth">Cart</a>
                <?php endif; ?>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php else: ?>
                <a href="login.php" class="nav-btn auth">Login</a>
                <a href="register.php" class="nav-btn auth">Register</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h1 style="margin: 30px 0; color: #2e2735; text-align: center;">Shop</h1>

            <!-- Фильтры -->
            <div class="filters">
                <form method="GET">
                    <div>
                        <label>Search:</label>
                        <input type="text" name="search" value="<?= htmlspecialchars($searchQuery) ?>" placeholder="Item">
                    </div>
                    <div>
                        <label>Category:</label>
                        <select name="category">
                            <option value="">All categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= htmlspecialchars($cat) ?>" <?= $selectedCategory === $cat ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($cat) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label>Price from:</label>
                        <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" min="0" step="0.01">
                        <label>up to:</label>
                        <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" min="0" step="0.01">
                    </div>
                    <button type="submit" class="btn btn-primary" style="font-size: 16px; padding: 8px 20px;">Submit</button>
                    <?php if ($selectedCategory || $minPrice || $maxPrice || $searchQuery): ?>
                        <a href="products.php" class="clear-filters">Clear filters</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (empty($products)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735;">Itmes did not found</p>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <div class="product-card">
                            <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="product-img">
                            <?php if (!empty($p['category'])): ?>
                                <div class="category-tag"><?= htmlspecialchars($p['category']) ?></div>
                            <?php endif; ?>
                            <h3><?= htmlspecialchars($p['name']) ?></h3>
                            <p style="font-size: 0.95rem; margin: 10px 0;"><?= htmlspecialchars(substr($p['description'], 0, 80)) ?>...</p>
                            <div style="font-size: 1.2rem; color: #766288; font-weight: 700; margin: 10px 0;">
                                <?= number_format($p['price'], 2, ',', ' ') ?> eur.
                            </div>
                            <div class="stock-info">
                                In stock: <?= (int)$p['stock_quantity'] ?> pc.
                            </div>
                            <a href="cart.php?action=add&id=<?= $p['id'] ?>" class="btn btn-primary" style="font-size: 16px; padding: 10px;">
                                Add to cart
                            </a>
                        </div>
                    <?php endforeach; ?>
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