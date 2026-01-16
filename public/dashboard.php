<?php
$pageTitle = 'Dashboard - Unicorns World';
include 'config.php';

// ѕровер€ем, авторизован ли пользователь
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

include 'templates/header.html';
include 'templates/dashboard_content.html';
include 'templates/footer.html';
?>
