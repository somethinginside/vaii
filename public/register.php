<?php
$pageTitle = 'Register - Unicorns World';
include 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Валидация
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    if (empty($name) || empty($email) || empty($password)) {
        $error = 'All fields required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Incorrect email!';
    } elseif ($password !== $confirm_password) {
        $error = 'Password does not match!';
    } else {
        // Проверяем, существует ли такой email
        $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'User with this email already exists!';
        } else {
            // Хэшируем пароль
            $hashed_password = hashPassword($password);

            // Записываем в БД
            $stmt = $pdo->prepare("
                INSERT INTO User (name, email, password_hash, role, registration_date)
                VALUES (?, ?, ?, 'user', NOW())
            ");
            $stmt->execute([$name, $email, $hashed_password]);

            $success = 'Registration succeed! Now you may login.';
        }
    }
}

include 'templates/header.html';
include 'templates/register_form.html';
include 'templates/footer.html';
?>