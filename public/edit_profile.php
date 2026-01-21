<?php
session_start();
$pageTitle = 'Edit Profile - Unicorns World';
include 'config.php';

// Защита
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$error = '';
$success = '';

// Получаем текущие данные
$stmt = $pdo->prepare("SELECT name, email, avatar FROM User WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
    die('User not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Определяем, какие поля обновлять
    $updateName = !empty($name) ? $name : $user['name'];
    $updateEmail = !empty($email) ? $email : $user['email'];

    // Валидация email
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Incorrect email!';
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error = 'New passwords do not match!';
    } else {
        // Проверка уникальности email (если изменён)
        if (!empty($email) && $email !== $user['email']) {
            $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ? AND id != ?");
            $stmt->execute([$email, $_SESSION['user_id']]);
            if ($stmt->fetch()) {
                $error = 'Email already in use!';
            }
        }

        if (!$error) {
            // Обработка аватара
            $avatarPath = $user['avatar']; // сохраняем старый
            if (!empty($_FILES['avatar']['name'])) {
                $uploadDir = 'uploads/avatars/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $fileExt = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                if (in_array(strtolower($fileExt), $allowed)) {
                    // Удаляем старый аватар
                    if ($user['avatar'] && file_exists($user['avatar'])) {
                        unlink($user['avatar']);
                    }
                    $fileName = 'avatar_' . $_SESSION['user_id'] . '_' . time() . '.' . strtolower($fileExt);
                    $filePath = $uploadDir . $fileName;
                    if (move_uploaded_file($_FILES['avatar']['tmp_name'], $filePath)) {
                        $avatarPath = $filePath;
                    }
                }
            }

            // Подготовка запроса
            $fields = [];
            $params = [];

            $fields[] = "name = ?";
            $params[] = $updateName;

            $fields[] = "email = ?";
            $params[] = $updateEmail;

            $fields[] = "avatar = ?";
            $params[] = $avatarPath;

            if (!empty($new_password)) {
                $fields[] = "password_hash = ?";
                $params[] = hashPassword($new_password);
            }

            $params[] = $_SESSION['user_id'];

            $sql = "UPDATE User SET " . implode(', ', $fields) . " WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            // Обновляем данные в сессии (если нужно)
            $_SESSION['user_name'] = $updateName; // опционально

            $success = 'Profile updated successfully!';
            // Обновляем $user для отображения
            $user['name'] = $updateName;
            $user['email'] = $updateEmail;
            $user['avatar'] = $avatarPath;
        }
    }
}

include 'templates/header.html';
include 'templates/profile_form.html';
include 'templates/footer.html';
?>