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

<?php include 'templates/admin_header.html'; ?>

        <div class="container">
            <h2>Manage Unicorns</h2>

            <div style="text-align: center; margin-bottom: 25px;">
                <a href="add_unicorn.php" class="btn btn-primary">Add Unicorn</a>
            </div>

            <?php if (empty($unicorns)): ?>
                <p>No unicorns added.</p>
            <?php else: ?>
                <div style="overflow-x: auto; background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Age</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Added By</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unicorns as $u): ?>
                                <tr data-id="<?= $u['id'] ?>" class="data-row">
                                    <td><?= htmlspecialchars($u['id']) ?></td>
                                    <td class="field" data-field="name"><?= htmlspecialchars($u['name']) ?></td>
                                    <td class="field" data-field="color"><?= htmlspecialchars($u['color']) ?></td>
                                    <td class="field" data-field="age"><?= htmlspecialchars($u['age']) ?></td>
                                    <td class="field" data-field="description"><?= htmlspecialchars($u['description']) ?></td>
                                    <td>
                                        <img src="<?= htmlspecialchars($u['image']) ?>" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                                        <span class="field" data-field="image" style="display:none;"><?= htmlspecialchars($u['image']) ?></span>
                                    </td>
                                    <td><?= htmlspecialchars($u['admin_name'] ?? '—') ?></td>
                                    <td>
                                        <button class="btn btn-warning btn-sm edit-btn">Edit</button>
                                        <button class="btn btn-success btn-sm save-btn" style="display:none;">Save</button>
                                        <button class="btn btn-secondary btn-sm cancel-btn" style="display:none;">Cancel</button>
                                        <button class="btn btn-danger btn-sm delete-btn">Delete</button>
                                        <div class="status"></div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>

<?php
$jsFile ='js/main.js';
$additionalJs = 'js/admin.js';
include 'templates/footer.html';
?>