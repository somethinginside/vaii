<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'My Favourites';
include 'config.php';

$stmt = $pdo->prepare("
    SELECT u.* 
    FROM Unicorn u
    INNER JOIN Favourite f ON u.id = f.unicorn_id
    WHERE f.user_id = ?
    ORDER BY f.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$favourites = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/favourites.html';
include 'templates/footer.html';
?>