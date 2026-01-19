<?php
header('Content-Type: app/JavaSC');
session_start();
?>
window.userData = <?php echo json_encode([
	'role' => $_SESSION['user_role'] ?? '',
	'isLoggedIn' => isset($_SESSION['user_role'])
], JSON_HEX_TAG | JSON_HEX_AMP); ?>;