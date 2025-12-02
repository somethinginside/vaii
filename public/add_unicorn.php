<?php
include 'config.php';

// Проверка прав администратора
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied! Only for administration! .');
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
        $error = 'All fields required!';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Unicorn (name, color, age, description, image, admin_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $color, $age, $description, $image, $_SESSION['user_id']]);
            $success = 'Unicorn successfully added!';
        } catch (PDOException $e) {
            $error = 'Error while adding: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add unicorn</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 600px; margin: 50px auto; padding: 20px; }
        input, textarea, button { display: block; width: 100%; margin: 10px 0; padding: 10px; }
        .error { color: red; }
        .success { color: green; }
        .btn { padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 4px; display: inline-block; }
    </style>
</head>
<body>
    <h2>Add unicorn</h2>

    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
        <a href="admin_unicorns.php" class="btn">For the list of unicorns</a>
    <?php else: ?>
        <form method="POST">
            <input type="text" name="name" placeholder="Name" required>
            <input type="text" name="color" placeholder="Color" required>
            <input type="number" name="age" placeholder="Age" min="0" required>
            <textarea name="description" placeholder="Description" rows="5" required></textarea>
            <input type="text" name="image" placeholder="URL image (e.g.: images/unicorn1.jpg)" required>
            <button type="submit">Add unicorn</button>
        </form>
        <a href="admin_unicorns.php" class="btn">Back</a>
    <?php endif; ?>
</body>
</html>