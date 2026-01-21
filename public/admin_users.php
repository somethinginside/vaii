<?php
$pageTitle = 'Manage Users: Admin Panel';
$isAdminPage = true;
include 'config.php';
include 'auth_check.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

// Обработка POST-запросов
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = (int)($_POST['user_id'] ?? 0);
    $action = $_POST['action'] ?? '';

    // Защита: нельзя управлять самим собой
    if ($userId === $_SESSION['user_id']) {
        $_SESSION['error'] = 'You cannot modify your own account.';
        header('Location: index.php');
        exit;
    }

    switch ($action) {
        case 'block':
            $pdo->prepare("UPDATE User SET status = 'blocked' WHERE id = ?")->execute([$userId]);
            break;

        case 'unblock':
            $pdo->prepare("UPDATE User SET status = 'active' WHERE id = ?")->execute([$userId]);
            break;

        case 'delete':
            // Удаляем заказы
            $pdo->prepare("DELETE FROM `Order` WHERE user_id = ?")->execute([$userId]);
            // Помечаем пользователя как удалённого
            $pdo->prepare("UPDATE User SET status = 'deleted' WHERE id = ?")->execute([$userId]);
            break;

        default:
            $_SESSION['error'] = 'Invalid action.';
            break;
    }

    header('Location: admin_users.php');
    exit;
}

// Получаем всех пользователей (кроме админов, если нужно)
$adminId = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT id, name, email, status 
    FROM User 
    WHERE id != ? 
    ORDER BY status DESC, name ASC
");
$stmt->execute([$adminId]);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/admin_users.html';
include 'templates/footer.html';
?>