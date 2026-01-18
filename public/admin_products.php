<?php
$pageTitle = 'Admin: Products';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$stmt = $pdo->prepare("
    SELECT p.*
    FROM Product p 
    ORDER BY p.id DESC
");
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

include 'templates/admin_header.html';
include 'templates/admin_products.html';
include 'templates/footer.html';
?>