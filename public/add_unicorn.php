<?php
include 'config.php';

// Только для админов
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Only for admins.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $color = trim($_POST['color']);
    $age = intval($_POST['age']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']); // URL изображения

    if (empty($name) || empty($color) || empty($age) || empty($description) || empty($image)) {
        $error = 'All fields required';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Unicorn (name, color, age, description, image, admin_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $color, $age, $description, $image, $_SESSION['user_id']]);
            $success = 'Unicorn successfully added';
        } catch (PDOException $e) {
            $error = 'Error while adding ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add unicorn - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="admin_unicorns.php" class="nav-btn main">Unicorns</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="dashboard.php" class="nav-btn auth">Account</a>
                <a href="logout.php" class="nav-btn auth">Logout</a>
            <?php endif; ?>
        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735; text-align: center;">Add new unicorn</h2>

            <?php if ($error): ?>
                <div class="message error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="message success"><?= htmlspecialchars($success) ?></div>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin_unicorns.php" class="btn btn-primary">Back to list</a>
                </div>
            <?php else: ?>
                <form method="POST" style="max-width: 600px; margin: 0 auto; background: white; padding: 30px; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                    <div class="form-group">
                        <label for="name">Name of unicorn</label>
                        <input type="text" name="name" id="name" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="color">Color</label>
                        <input type="text" name="color" id="color" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" name="age" id="age" min="0" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea name="description" id="description" class="form-control" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">URL image</label>
                        <input type="text" name="image" id="image" class="form-control" placeholder="/images/unicorn.jpg" required>
                        <p style="font-size: 0.85rem; color: #766288; margin-top: 5px;">
                            Example: <code>/images/unicorn1.jpg</code> (the file must be in the folder <code>public/images/</code>)
                        </p>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 18px;">Add unicorn</button>
                </form>

                <div style="text-align: center; margin-top: 25px;">
                    <a href="admin_unicorns.php" class="btn btn-secondary">Back to list</a>
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