<?php
session_start();
$pageTitle = 'Products — Unicorns World';
include 'config.php';


// Получаем все товары
$stmt = $pdo->prepare("
    SELECT p.*
    FROM Product p 
    ORDER BY p.id DESC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/products.html';
include 'templates/footer.html';
?>