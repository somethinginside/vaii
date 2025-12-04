<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Accsess denied');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $stock = (int)($_POST['stock_quantity'] ?? 0);

    if (empty($name) || empty($category) || $price <= 0 || empty($image) || $stock < 0) {
        $error = 'All fiels are requied and must be correct';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO `Product` (name, category, price, description, image, stock_quantity)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $category, $price, $description, $image, $stock]);
            header('Location: admin_products.php');
            exit;
        } catch (PDOException $e) {
            $error = 'Error while adding product';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add product</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="admin_products.php" class="nav-btn main">Back to products</a>
            <a href="dashboard.php" class="nav-btn auth">Account</a>
            <a href="logout.php" class="nav-btn auth">Logout</a>
        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735;">Add new product</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" style="max-width: 600px; margin: 0 auto;">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Category</label>
                    <input type="text" name="category" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Price (eur.)</label>
                    <input type="number" name="price" step="0.01" min="0.01" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" class="form-control" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>URL image</label>
                    <input type="text" name="image" class="form-control" required>
                </div>
                <div class="form-group">
                    <label>Remaining stock</label>
                    <input type="number" name="stock_quantity" min="0" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary" style="width: 100%;">Add proudct</button>
            </form>
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