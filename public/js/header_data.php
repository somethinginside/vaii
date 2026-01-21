<?php
header('Content-Type: application/javascript');
session_start();

echo 'window.userData = ' . json_encode([
    'isLoggedIn' => isset($_SESSION['user_id']),
    'role' => $_SESSION['user_role'] ?? null
], JSON_HEX_TAG | JSON_HEX_AMP) . ';';
?>