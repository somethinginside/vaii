<?php
include 'config.php';
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied');
}

$stmt = $pdo->query("SELECT * FROM `Product` ORDER BY id DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin panel: Products</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="dashboard.php" class="nav-btn auth">Account</a>
            <a href="logout.php" class="nav-btn auth">Logout</a>
        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735;">Products management</h2>
            <a href="add_product.php" class="btn btn-primary" style="margin-bottom: 20px;">Add product</a>

            <?php if (empty($products)): ?>
                <p>No products added.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Acrtions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td><?= $p['id'] ?></td>
                                <td><?= htmlspecialchars($p['name']) ?></td>
                                <td><?= htmlspecialchars($p['category']) ?></td>
                                <td><?= number_format($p['price'], 2, ',', ' ') ?> eur.</td>
                                <td><?= (int)$p['stock_quantity'] ?></td>
                                <td>
                                    <a href="edit_product.php?id=<?= $p['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                                    <button type="button" class="btn btn-danger btn-sm delete-btn" data-id="<?= $p['id'] ?>">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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

    <script>
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const id = this.dataset.id;
                const name = this.closest('tr').querySelector('td:nth-child(2)').textContent;
        
                if (!confirm(`Delete product "${name}"?`)) return;
        
                try {
                    const res = await fetch('delete_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const data = await res.json();
            
                    if (data.success) {
                        this.closest('tr').remove();
                    } else {
                        alert('Error: ' + (data.error || 'Unknown product'));
                    }
                } catch (err) {
                    alert('Network error');
                }
            });
        });
    </script>
</body>
</html>