<?php
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    die('Access denied! Only for administration!');
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
    <title>Admin: Unicrons</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .btn { padding: 6px 12px; margin: 2px; text-decoration: none; border-radius: 4px; cursor: pointer; }
        .btn-primary { background: #007bff; color: white; }
        .btn-success { background: #28a745; color: white; }
        .btn-warning { background: #ffc107; color: black; }
        .btn-danger { background: #dc3545; color: white; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; vertical-align: top; }
        th { background: #f8f9fa; }
        .edit-row input,
        .edit-row textarea,
        .edit-row button {
            display: block; width: 100%; margin: 2px 0; padding: 4px;
        }
        .status { font-weight: bold; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>
    <h2>Admin: Unicorn Managment </h2>

    <a href="add_unicorn.php" class="btn btn-primary"> Add unicorn</a>
    <a href="dashboard.php" class="btn" style="background: #6c757d; color: white;">Back to account </a>

    <table id="unicorns-table">
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
                        <img src="<?= htmlspecialchars($u['image']) ?>" alt="Image" width="50" style="display:block; margin-bottom:5px; border:1px solid #eee;">
                        <span class="field" data-field="image" style="display:none;"><?= htmlspecialchars($u['image']) ?></span>
                    </td>
                    <td><?= htmlspecialchars($u['admin_name'] ?? '—') ?></td>
                    <td>
                        <button class="btn btn-warning edit-btn">Edit</button>
                        <button class="btn btn-success save-btn" style="display:none;">Save</button>
                        <button class="btn btn-secondary cancel-btn" style="display:none;">Cancel</button>
                        <button class="btn btn-danger delete-btn">Delete</button>
                        <div class="status"></div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // === РЕДАКТИРОВАНИЕ ===
        function enableEditing(button) {
            const row = button.closest('tr');
            if (!row) return;

            // Скрыть статус
            const statusDiv = row.querySelector('.status');
            if (statusDiv) statusDiv.textContent = '';

            // Найти все поля для редактирования
            const nameField = row.querySelector('[data-field="name"]');
            const colorField = row.querySelector('[data-field="color"]');
            const ageField = row.querySelector('[data-field="age"]');
            const descField = row.querySelector('[data-field="description"]');
            const imageField = row.querySelector('[data-field="image"]');

            // Убедимся, что поля найдены
            if (!nameField || !colorField || !ageField || !descField || !imageField) {
                console.error('Not all fields found for edit');
                return;
            }

            // Показать форму редактирования
            const name = nameField.textContent.trim();
            const color = colorField.textContent.trim();
            const age = ageField.textContent.trim();
            const desc = descField.textContent.trim();
            const imageUrl = imageField.textContent.trim();

            // Заменяем содержимое ячеек
            nameField.innerHTML = `<input type="text" value="${name}" style="width:100%">`;
            colorField.innerHTML = `<input type="text" value="${color}" style="width:100%">`;
            ageField.innerHTML = `<input type="number" value="${age}" min="0" style="width:100%">`;
            descField.innerHTML = `<textarea rows="2" style="width:100%">${desc}</textarea>`;

            // Для изображения — превью + поле ввода
            imageField.closest('td').innerHTML = `
                <img src="${imageUrl}" alt="Preview" width="50" style="display:block; margin-bottom:5px; border:1px solid #ccc;">
                <input type="text" value="${imageUrl}" placeholder="URL image" style="width:100%;">
            `;

            // Кнопки
            button.style.display = 'none';
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            if (saveBtn) saveBtn.style.display = 'inline-block';
            if (cancelBtn) cancelBtn.style.display = 'inline-block';
        }

        // Назначаем обработчики для всех кнопок "Edit"
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.preventDefault();
                enableEditing(this);
            });
        });

        // === ОТМЕНА ===
        document.querySelectorAll('.cancel-btn').forEach(btn => {
            btn.addEventListener('click', () => location.reload());
        });

        // === СОХРАНЕНИЕ ===
        document.querySelectorAll('.save-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const statusDiv = row.querySelector('.status');

        // Собираем данные из input/textarea в ячейках
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
                            statusDiv.textContent = 'Save';
                            statusDiv.className = 'status success';
                        }
                        setTimeout(() => location.reload(), 1200);
                    } else {
                        if (statusDiv) {
                            statusDiv.textContent =  (data.error || 'Error');
                            statusDiv.className = 'status error';
                        }
                    }
                } catch (err) {
                    if (statusDiv) {
                        statusDiv.textContent = 'NetworkError';
                        statusDiv.className = 'status error';
                    }
                }
            });
        });

        // === УДАЛЕНИЕ ===
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', async function() {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const name = row.querySelector('[data-field="name"]')?.textContent || '???';

                if (!confirm(`Delete "${name}"?`)) return;

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
                            statusDiv.textContent =  (data.error || 'Error');
                            statusDiv.className = 'status error';
                        }
                    }
                } catch (err) {
                    if (statusDiv) statusDiv.textContent = 'Network Error';
                }
            });
        });

    });
    </script>
</body>
</html>