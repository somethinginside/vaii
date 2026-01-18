<?php
$pageTitle = 'Admin: Unicorns';
$isAdminPage = true;
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Admin only.');
}

$stmt = $pdo->prepare("
    SELECT u.*, a.name as admin_name 
    FROM Unicorn u 
    LEFT JOIN User a ON u.admin_id = a.id
    ORDER BY u.id DESC
");
$stmt->execute();
$unicorns = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include 'templates/admin_header.html';
$jsFile ='js/main.js';
$additionalJs = 'js/admin.js';
include 'templates/admin_unicorns.html';
include 'templates/footer.html';
?>