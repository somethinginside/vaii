<?php
$pageTitle = 'Admin: adding product';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Accsess denied');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $category = trim($_POST['category']);
    $price = (float)($_POST['price'] ?? 0);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $stock = (int)($_POST['stock_quantity'] ?? 0);

    if (empty($name) || empty($category) || $price <= 0 || empty($image) || $stock < 0) {
        $error = 'All fiels are requied and must be correct';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO `Product` (name, category, price, description, image, stock_quantity)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $category, $price, $description, $image, $stock]);
            $success = 'Product added successfully';
        } catch (PDOException $e) {
            $error = 'Error while adding product';
        }
    }
}

include 'templates/header.html';
include 'templates/product_form.html';
include 'templates/footer.html';
?>