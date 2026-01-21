<?php
header('Content-Type: application/json');
session_start();
include 'config.php';

$unicornId = (int)($_GET['id'] ?? 0);
if ($unicornId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

// Получаем данные единорога
$stmt = $pdo->prepare("SELECT * FROM Unicorn WHERE id = ?");
$stmt->execute([$unicornId]);
$unicorn = $stmt->fetch();

if (!$unicorn) {
    echo json_encode(['success' => false, 'error' => 'Unicorn not found']);
    exit;
}

// Проверяем, в избранном ли
$isInFavourites = false;
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT 1 FROM Favourite WHERE user_id = ? AND unicorn_id = ?");
    $stmt->execute([$_SESSION['user_id'], $unicornId]);
    $isInFavourites = (bool)$stmt->fetch();
}

echo json_encode([
    'success' => true,
    'unicorn' => [
        'id' => $unicorn['id'],
        'name' => $unicorn['name'],
        'age' => $unicorn['age'],
        'color' => $unicorn['color'],
        'image' => $unicorn['image'],
        'description' => $unicorn['description']
    ],
    'is_in_favourites' => $isInFavourites
]);
?>