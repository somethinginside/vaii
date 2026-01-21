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

    //Validation
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
            //Avatar
            $avatarPath = null;
            if (!empty($_FILES['avatar']['name'])) {
                $uploadDir = 'uploads/avatars/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $fileExt = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array(strtolower($fileExt), $allowed)) {
                    $fileName = 'avatar_' . uniqid() .'.' . strtolower($fileExt);
                    $filePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                        $avatarPath = $filePath;
                    }
                }
            }
            // Хэшируем пароль
            $hashed_password = hashPassword($password);

            // Записываем в БД
            $stmt = $pdo->prepare("
                INSERT INTO User (name, email, password_hash, status, role, registration_date, avatar)
                VALUES (?, ?, ?,'active', 'user', NOW(), ?)
            ");
            $stmt->execute([$name, $email, $hashed_password, $avatarPath]);

            $success = 'Registration succeed! Now you may login.';
        }
    }
}

include 'templates/header.html';
include 'templates/register_form.html';
include 'templates/footer.html';
?>