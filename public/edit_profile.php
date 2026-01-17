<?php
$pageTitle = 'Edit Profile';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Начальные значения — текущие данные пользователя
    $newName = $user['name'];
    $newEmail = $user['email'];
    $newHashedPassword = null;

    // Проверяем, что хотя бы что-то изменилось
    $changesMade = false;

    if (!empty($name) && $name !== $user['name']) {
        $newName = $name;
        $changesMade = true;
    }

    if (!empty($email) && $email !== $user['email']) {
        // Проверяем, не занят ли email
        $stmt = $pdo->prepare("SELECT id FROM User WHERE email = ? AND id != ?");
        $stmt->execute([$email, $_SESSION['user_id']]);
        if ($stmt->fetch()) {
            $error = 'Email already exists.';
        } else {
            $newEmail = $email;
            $changesMade = true;
        }
    }

    if (!empty($newPassword)) {
        if (empty($confirmPassword)) {
            $error = 'Please confirm new password';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } else {
            $newHashedPassword = hashPassword($newPassword);
            $changesMade = true;
        }

    }

    if (!$changesMade) {
        $error = 'No changes made.';
    } elseif (empty($error)) {
        try {
            if ($newHashedPassword) {
                $stmt = $pdo->prepare("UPDATE User SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$newName, $newEmail, $newHashedPassword, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE User SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$newName, $newEmail, $_SESSION['user_id']]);
            }
            $success = 'Profile updated successfully!';
            // Обновляем сессию
            $_SESSION['user_name'] = $newName;
        } catch (PDOException $e) {
            $error = 'Database error occurred.';
        }
    }
}

include 'templates/header.html';
include 'templates/profile_form.html';
include 'templates/footer.html';
?>