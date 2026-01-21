<?php
session_start();
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        // Ищем пользователя
        $stmt = $pdo->prepare("SELECT id, password_hash, status, role FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user) {
            $error = 'Invalid email or password.';
        } elseif ($user['status'] === 'blocked') {
            $error = 'Your account has been blocked. Please contact support.';
        } elseif ($user['status'] === 'deleted') {
            $error = 'Your account has been deleted.';
        } elseif (!password_verify($password, $user['password_hash'])) {
            $error = 'Invalid email or password.';
        } else {
            // Успешный вход
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            header('Location: index.php');
            exit;
        }
    }
}

$pageTitle = 'Login';
include 'templates/header.html';
include 'templates/login_form.html';
include 'templates/footer.html';
?>