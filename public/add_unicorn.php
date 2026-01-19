<?php
$pageTitle = 'Admin: adding unicorns';
$isAdminpage = true;
include 'config.php';

// Только для админов
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Only for admins.');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $color = trim($_POST['color']);
    $age = intval($_POST['age']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    if (empty($name) || empty($color) || empty($age) || empty($description) || empty($image)) {
        $error = 'All fields required';
    } else {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO Unicorn (name, color, age, description, image, admin_id)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([$name, $color, $age, $description, $image, $_SESSION['user_id']]);
            $success = 'Unicorn successfully added';
        } catch (PDOException $e) {
            $error = 'Error while adding ' . $e->getMessage();
        }
    }
}

include 'templates/header.html';
include 'templates/unicorn_form.html';
include 'templates/footer.html';
?>
