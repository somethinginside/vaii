<?php
$pageTitle = 'Unicorns';
include 'config.php';

// Получаем всех единорогов
$stmt = $pdo->prepare("
    SELECT u.*, a.name as admin_name 
    FROM Unicorn u 
    LEFT JOIN User a ON u.admin_id = a.id
    ORDER BY u.id DESC
");
$stmt->execute();
$unicorns = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/unicorns_list.html';
include 'templates/footer.html';
?>
