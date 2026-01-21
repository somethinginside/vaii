<?php
session_start();
$pageTitle = 'Home — Unicorns World';
include 'config.php';


// Получаем последние товары
$stmt = $pdo->prepare("SELECT * FROM Product ORDER BY id DESC LIMIT 8");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/header.html';
include 'templates/index.html';
// Подключаем JS для главной
$additionalJs = 'js/products.js';
include 'templates/footer.html';
?>