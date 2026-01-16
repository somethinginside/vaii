<?php
$pageTitle = 'Edit Profile Ч Unicorns World';
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user = getCurrentUser();

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($name) || empty($email)) {
        $error = 'Name and email are required.';
    } elseif ($newPassword !== '' && $newPassword !== $confirmPassword) {
        $error = 'New passwords do not match.';
    } else {
        try {
            if ($newPassword !== '') {
                $hashedPassword = hashPassword($newPassword);
                $stmt = $pdo->prepare("UPDATE User SET name = ?, email = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $email, $hashedPassword, $_SESSION['user_id']]);
            } else {
                $stmt = $pdo->prepare("UPDATE User SET name = ?, email = ? WHERE id = ?");
                $stmt->execute([$name, $email, $_SESSION['user_id']]);
            }
            $success = 'Profile updated successfully!';
            // ќбновл€ем сессию
            $_SESSION['user_name'] = $name;
        } catch (PDOException $e) {
            $error = 'Email already exists.';
        }
    }
}

include 'templates/header.html';
include 'templates/profile_form.html';
include 'templates/footer.html';
?>