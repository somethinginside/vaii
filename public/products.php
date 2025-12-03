<?php
include 'config.php';

// Получаем уникальные категории
$stmt = $pdo->query("SELECT DISTINCT category FROM `Product` WHERE category != '' ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Получаем параметры фильтрации из GET
$selectedCategory = trim($_GET['category'] ?? '');
$minPrice = trim($_GET['min_price'] ?? '');
$maxPrice = trim($_GET['max_price'] ?? '');
$searchQuery = trim($_GET['search'] ?? '');

// Начинаем строить запрос
$sql = "SELECT * FROM `Product` WHERE 1=1";
$params = [];

// Фильтр по категории
if ($selectedCategory !== '') {
    $sql .= " AND category = ?";
    $params[] = $selectedCategory;
}

// Фильтр по поиску в названии
if ($searchQuery !== '') {
    $sql .= " AND name LIKE ?";
    $params[] = '%' . $searchQuery . '%';
}

// Фильтр по цене
if (is_numeric($minPrice) && $minPrice >= 0) {
    $sql .= " AND price >= ?";
    $params[] = (float)$minPrice;
}
if (is_numeric($maxPrice) && $maxPrice >= 0) {
    $sql .= " AND price <= ?";
    $params[] = (float)$maxPrice;
}

$sql .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shop</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .filters {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .filters label { display: inline-block; width: 100px; margin-right: 10px; }
        .filters input, .filters select, .filters button {
            padding: 6px 10px;
            margin-right: 10px;
            margin-bottom: 8px;
        }
        .products { display: flex; flex-wrap: wrap; gap: 20px; }
        .product-card {
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 15px;
            width: 220px;
            text-align: center;
            background: white;
        }
        .product-card img {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 4px;
        }
        .price { font-size: 1.1em; color: #e74c3c; font-weight: bold; margin: 8px 0; }
        .category { font-size: 0.9em; color: #666; margin-bottom: 8px; background: #f1f1f1; padding: 4px; border-radius: 4; }
        .stock { font-size: 0.9em; margin: 5px 0; }
        .stock.out { color: #e74c3c; font-weight: bold; }
        .stock.in { color: #27ae60; }
        .btn {
            background: #3498db; color: white; border: none;
            padding: 6px 12px; border-radius: 4px;
            cursor: pointer; text-decoration: none; display: inline-block;
            font-size: 0.9em;
            width: 100%;
            margin-top: 8px;
        }
        .btn:disabled {
            background: #ccc;
            cursor: not-allowed;
        }
        .clear-filters {
            margin-left: 10px;
        }
    </style>
</head>
<body>
    <h1>Shop</h1>

    <!-- Форма фильтрации -->
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
                <label>Цена от:</label>
                <input type="number" name="min_price" value="<?= htmlspecialchars($minPrice) ?>" min="0" step="0.01">
                <label>до:</label>
                <input type="number" name="max_price" value="<?= htmlspecialchars($maxPrice) ?>" min="0" step="0.01">
            </div>
            <button type="submit">Submit</button>
            <?php if ($selectedCategory || $minPrice || $maxPrice || $searchQuery): ?>
                <a href="products.php" class="clear-filters">Reset filter</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if (empty($products)): ?>
        <p>Items did not found.</p>
    <?php else: ?>
        <div class="products">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <img src="<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                    <?php if (!empty($p['category'])): ?>
                        <div class="category"><?= htmlspecialchars($p['category']) ?></div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($p['name']) ?></h3>
                    <p><?= htmlspecialchars(substr($p['description'] ?? '', 0, 80)) ?>...</p>
                    <div class="price"><?= number_format($p['price'], 2, ',', ' ') ?> eur.</div>
                    
                    <?php if ((int)$p['stock_quantity'] <= 0): ?>
                        <div class="stock out">Out of stock</div>
                        <button class="btn" disabled>Out of stock</button>
                    <?php else: ?>
                        <div class="stock in">In stock: <?= (int)$p['stock_quantity'] ?> шт.</div>
                        <a href="cart.php?action=add&id=<?= $p['id'] ?>" class="btn">Add to the product basket</a>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <p><a href="dashboard.php">Back to account</a></p>
</body>
</html>