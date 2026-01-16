<?php
$pageTitle = 'Edit Product — Unicorns World';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$error = '';
$success = '';
$product = [];

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM Product WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$product) {
        die('Product not found.');
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = (int)($_POST['id'] ?? 0);
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = (float)($_POST['price'] ?? 0);
    $stock = (int)($_POST['stock_quantity'] ?? 0);
    $image = trim($_POST['image']);

    if (empty($name) || $price < 0 || $stock < 0 || empty($image)) {
        $error = 'All fields required.';
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE Product SET name = ?, description = ?, price = ?, stock_quantity = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $stock, $image, $id]);
            $success = 'Product updated successfully!';
        } catch (PDOException $e) {
            $error = 'Error updating product: ' . $e->getMessage();
        }
    }
}

include 'templates/admin_header.html';
include 'templates/product_edit_form.html';
include 'templates/footer.html';
?>