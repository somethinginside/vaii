<?php
$pageTitle = 'Login - Unicorns World';
include 'config.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (empty($email) || empty($password)) {
        $error = 'Enter email and password!';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM User WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && verifyPassword($password, $user['password_hash'])) {
            // Успешный вход
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_role'] = $user['role'];

            // Перенаправляем на личный кабинет
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Wrong email or password!';
        }
    }
}

include 'templates/header.html';
include 'templates/login_form.html';
include 'templates/footer.html';
?>