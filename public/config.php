<?php
session_start();

// Настройки подключения к БД
$host = 'MySQL-5.7';
$dbname = 'unicorns_world';
$username = 'root'; // по умолчанию в OpenServer
$password = '';     // по умолчанию пустой

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection error: " . $e->getMessage());
}

// Функция для безопасного хеширования пароля
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Функция для проверки пароля
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// Функция для получения текущего пользователя
function getCurrentUser() {
    if (isset($_SESSION['user_id'])) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM User WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    return null;
}
?>