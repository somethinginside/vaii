<?php
include 'config.php';

// Только для админов
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Access denied! Only for administration!']);
    exit;
}

// Только POST-запросы
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method do not support']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input || !isset($input['id'])) {
    echo json_encode(['error' => 'Data does not exist']);
    exit;
}

$unicorn_id = (int)$input['id'];
$name = trim($input['name']);
$color = trim($input['color']);
$age = (int)$input['age'];
$description = trim($input['description']);
$image = trim($input['image']);

if (empty($name) || empty($color) || $age < 0 || empty($description) || empty($image)) {
    echo json_encode(['error' => 'All fields required']);
    exit;
}

try {
    $stmt = $pdo->prepare("
        UPDATE `Unicorn` 
        SET name = ?, color = ?, age = ?, description = ?, image = ?
        WHERE id = ?
    ");
    $stmt->execute([$name, $color, $age, $description, $image, $unicorn_id]);

    echo json_encode(['success' => true, 'message' => 'Update']);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?>