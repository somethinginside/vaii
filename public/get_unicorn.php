<?php
header('Content-Type: application/json');
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id || !is_numeric($id)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid ID']);
    exit;
}

$stmt = $pdo->prepare("
    SELECT u.*, a.name as admin_name 
    FROM Unicorn u 
    LEFT JOIN User a ON u.admin_id = a.id
    WHERE u.id = ?
");
$stmt->execute([(int)$id]);
$unicorn = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$unicorn) {
    http_response_code(404);
    echo json_encode(['success' => false, 'error' => 'Unicorn not found']);
    exit;
}

echo json_encode(['success' => true, 'unicorn' => [
    'id' => $unicorn['id'],
    'name' => htmlspecialchars($unicorn['name']),
    'age' => (int)$unicorn['age'],
    'color' => htmlspecialchars($unicorn['color']),
    'description' => htmlspecialchars($unicorn['description']),
    'image' => htmlspecialchars($unicorn['image']),
    'admin_name' => $unicorn['admin_id'] ? htmlspecialchars($unicorn['admin_name']) : null
]]);
?>