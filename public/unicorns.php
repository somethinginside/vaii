<?php
session_start();
$pageTitle = 'Unicorns: Unicorns World';
$additionalCss = '/css/unicorns.css';
include 'config.php';

// ѕолучаем всех единорогов
$stmt = $pdo->query("SELECT * FROM Unicorn ORDER BY name");
$unicorns = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ѕолучаем избранное (если пользователь авторизован)
$userFavourites = [];
if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("
        SELECT unicorn_id FROM Favourite WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $userFavourites = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

include 'templates/header.html';
include 'templates/unicorns_list.html';
include 'templates/footer.html';
?>