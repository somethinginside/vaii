<?php
// auth_check.php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Проверяем статус пользователя
$stmt = $pdo->prepare("SELECT status FROM User WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$status = $stmt->fetchColumn();

if ($status === 'blocked') {
    session_destroy();
    die('<h2>Your account has been blocked.</h2><p>Contact support for more information.</p>');
}

if ($status === 'deleted') {
    session_destroy();
    die('<h2>Your account has been deleted.</h2><p>All your data has been removed.</p>');
}
?>