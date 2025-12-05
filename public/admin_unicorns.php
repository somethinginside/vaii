<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied. Only for admins.');
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
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Unicorns - Unicorns World</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .status {
            font-weight: bold;
            margin-top: 8px;
        }
        .status.success { color: #28a745; }
        .status.error { color: #dc3545; }
        .product-img {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 6px;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

    <main class="site-main">
        <header class="site-header">
            <a href="index.php" class="nav-btn main">Home</a>
            <a href="dashboard.php" class="nav-btn auth">Account</a>
            <a href="logout.php" class="nav-btn auth">Logout</a>
        </header>

        <div class="container">
            <h2 style="margin: 30px 0; color: #2e2735; text-align: center;">Unicorns management</h2>

            <div style="text-align: center; margin-bottom: 25px;">
                <a href="add_unicorn.php" class="btn btn-primary">Add unicorn</a>
            </div>

            <?php if (empty($unicorns)): ?>
                <p style="text-align: center; font-size: 1.1rem; color: #2e2735;">Unicorns did not added.</p>
            <?php else: ?>
                <div style="overflow-x: auto; background: white; border-radius: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Color</th>
                                <th>Age</th>
                                <th>Description</th>
                                <th>Image</th>
                                <th>Admin</th>
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
                                        <span class="field" data-field="image" style="display:none;"><?= htmlspecialchars($u['image']) ?></span>
                                        <img src="<?= htmlspecialchars($u['image']) ?>" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                                    </td>
                                    <td><?= htmlspecialchars($u['admin_name'] ?? 'ó') ?></td>
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
    </main>

    <footer class="site-footer">
        <div>
            <p>&copy; <?= date('Y') ?> Unicorns World. All rights reserved.</p>
            <p style="margin-top: 10px; font-size: 0.85rem;">
                We care about your privacy. 
                <a href="privacy.php">Privacy Policy</a>
            </p>
        </div>
    </footer>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // === –≈ƒ¿ “»–Œ¬¿Õ»≈ ===
        function enableEditing(button) {
            const row = button.closest('tr');
            if (!row) return;

            const statusDiv = row.querySelector('.status');
            if (statusDiv) statusDiv.textContent = '';

            const nameField = row.querySelector('.field[data-field="name"]');
            const colorField = row.querySelector('.field[data-field="color"]');
            const ageField = row.querySelector('.field[data-field="age"]');
            const descField = row.querySelector('.field[data-field="description"]');
            const imageField = row.querySelector('.field[data-field="image"]');

            if (!nameField || !colorField || !ageField || !descField || !imageField) {
                console.error('Not all fields was found for editing');
                return;
            }

            const name = nameField.textContent.trim();
            const color = colorField.textContent.trim();
            const age = ageField.textContent.trim();
            const desc = descField.textContent.trim();
            const imageUrl = imageField.textContent.trim();

            nameField.innerHTML = `<input type="text" value="${name}" style="width:100%">`;
            colorField.innerHTML = `<input type="text" value="${color}" style="width:100%">`;
            ageField.innerHTML = `<input type="number" value="${age}" min="0" style="width:100%">`;
            descField.innerHTML = `<textarea rows="2" style="width:100%">${desc}</textarea>`;

            imageField.closest('td').innerHTML = `
                <img src="${imageUrl}" alt="Preview" width="60" style="display:block; margin-bottom:5px; border:1px solid #ccc; border-radius: 6px;">
                <input type="text" value="${imageUrl}" placeholder="URL image" style="width:100%;">
            `;

            button.style.display = 'none';
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            if (saveBtn) saveBtn.style.display = 'inline-block';
            if (cancelBtn) cancelBtn.style.display = 'inline-block';
        }

        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                enableEditing(this);
            });
        });

        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => location.reload());
        });

        // === —Œ’–¿Õ≈Õ»≈ ===
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const statusDiv = row.querySelector('.status');

                const name = row.querySelector('[data-field="name"] input')?.value || '';
                const color = row.querySelector('[data-field="color"] input')?.value || '';
                const age = row.querySelector('[data-field="age"] input')?.value || '';
                const desc = row.querySelector('[data-field="description"] textarea')?.value || '';
                const imageUrl = row.querySelector('td img + input')?.value || '';

                if (!name || !color || !age || !desc || !imageUrl) {
                    if (statusDiv) {
                        statusDiv.textContent = 'All fields required';
                        statusDiv.className = 'status error';
                    }
                    return;
                }

                try {
                    const res = await fetch('update_unicorn.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, name, color, age, description: desc, image: imageUrl })
                    });
                    const data = await res.json();

                    if (data.success) {
                        if (statusDiv) {
                            statusDiv.textContent = 'Saved';
                            statusDiv.className = 'status success';
                        }
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        if (statusDiv) {
                            statusDiv.textContent = (data.error || 'Error');
                            statusDiv.className = 'status error';
                        }
                    }
                } catch (err) {
                    if (statusDiv) {
                        statusDiv.textContent = 'Network error';
                        statusDiv.className = 'status error';
                    }
                }
            });
        });

        // === ”ƒ¿À≈Õ»≈ ===
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const name = row.querySelector('[data-field="name"]')?.textContent || '???';

                if (!confirm(`Delete unicorn "${name}"?`)) return;

                const statusDiv = row.querySelector('.status');
                if (statusDiv) statusDiv.textContent = 'Deleting...';

                try {
                    const res = await fetch('delete_unicorn.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id })
                    });
                    const data = await res.json();

                    if (data.success) {
                        if (statusDiv) {
                            statusDiv.textContent = 'Deleted';
                            statusDiv.className = 'status success';
                        }
                        setTimeout(() => row.remove(), 1000);
                    } else {
                        if (statusDiv) {
                            statusDiv.textContent = (data.error || 'Error');
                            statusDiv.className = 'status error';
                        }
                    }
                } catch (err) {
                    if (statusDiv) statusDiv.textContent = 'Network error';
                }
            });
        });

    });
    </script>

</body>
</html>