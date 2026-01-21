<?php
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$unicornId = (int)($input['unicorn_id'] ?? 0);
$action = $input['action'] ?? 'add'; // 'add' или 'remove'

if ($unicornId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid unicorn ID']);
    exit;
}

include 'config.php';

// Проверяем существование
$stmt = $pdo->prepare("SELECT id FROM Unicorn WHERE id = ?");
$stmt->execute([$unicornId]);
if (!$stmt->fetch()) {
    echo json_encode(['success' => false, 'error' => 'Unicorn not found']);
    exit;
}

try {
    if ($action === 'remove') {
        // Удаляем из избранного
        $stmt = $pdo->prepare("DELETE FROM Favourite WHERE user_id = ? AND unicorn_id = ?");
        $success = $stmt->execute([$_SESSION['user_id'], $unicornId]);
        $newState = false;
    } else {
        // Добавляем
        $stmt = $pdo->prepare("INSERT IGNORE INTO Favourite (user_id, unicorn_id) VALUES (?, ?)");
        $success = $stmt->execute([$_SESSION['user_id'], $unicornId]);
        $newState = true;
    }

    echo json_encode([
        'success' => $success,
        'is_in_favourites' => $newState
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Database error']);
}
?>