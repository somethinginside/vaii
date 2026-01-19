console.log('Admin Orders JS loaded');

document.addEventListener('DOMContentLoaded', function () {
    console.log('DOM loaded for Admin Orders');

    // === STATUS EDITING ===

    // === ENABLE STATUS EDITING ===
    function enableStatusEditing(button) {
        console.log('Enable status editing called');
        const row = button.closest('tr');
        if (!row) {
            console.log('Row not found');
            return;
        }

        const statusMessage = row.querySelector('.status-message');
        if (statusMessage) statusMessage.textContent = '';

        // ✅ Показываем select, скрываем span
        const statusCell = row.querySelector('.status-cell');
        const statusText = statusCell.querySelector('.status-text');
        const statusSelect = statusCell.querySelector('.status-select');

        if (statusText) statusText.style.display = 'none';
        if (statusSelect) statusSelect.style.display = 'block';

        // ✅ Скрываем кнопку Edit Status
        button.style.display = 'none';
        const saveBtn = row.querySelector('.save-status-btn');
        const cancelBtn = row.querySelector('.cancel-status-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-status-btn').forEach(btn => {
        console.log('Found edit-status-btn:', btn);
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('Edit status button clicked!');
            enableStatusEditing(this);
        });
    });

    // === CANCEL STATUS ===
    document.querySelectorAll('.cancel-status-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            console.log('Cancel status button clicked!');
            const row = this.closest('tr');

            // ✅ Скрываем select, показываем span
            const statusCell = row.querySelector('.status-cell');
            const statusText = statusCell.querySelector('.status-text');
            const statusSelect = statusCell.querySelector('.status-select');

            if (statusText) statusText.style.display = 'inline';
            if (statusSelect) statusSelect.style.display = 'none';

            // ✅ Возвращаем кнопки в исходное состояние
            const editBtn = row.querySelector('.edit-status-btn');
            const saveBtn = row.querySelector('.save-status-btn');
            const cancelBtn = row.querySelector('.cancel-status-btn');
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        });
    });

    // === SAVE STATUS ===
    document.querySelectorAll('.save-status-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            console.log('Save status button clicked!');
            const row = this.closest('tr');
            const id = row.dataset.id;
            const statusMessage = row.querySelector('.status-message');

            const statusSelect = row.querySelector('.status-cell .status-select');
            const newStatus = statusSelect.value;

            if (!newStatus) {
                if (statusMessage) {
                    statusMessage.textContent = 'Status required';
                    statusMessage.className = 'status-message error';
                }
                return;
            }

            try {
                const res = await fetch('/update_order_status.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, status: newStatus })
                });
                const data = await res.json();

                if (data.success) {
                    if (statusMessage) {
                        statusMessage.textContent = 'Saved';
                        statusMessage.className = 'status-message success';
                    }

                    // ✅ Обновляем span с новым статусом
                    const statusText = row.querySelector('.status-cell .status-text');
                    statusText.textContent = newStatus;

                    // ✅ Скрываем select, показываем span
                    const statusSelect = row.querySelector('.status-cell .status-select');
                    statusText.style.display = 'inline';
                    statusSelect.style.display = 'none';

                    // ✅ Возвращаем кнопки в исходное состояние
                    const editBtn = row.querySelector('.edit-status-btn');
                    const saveBtn = row.querySelector('.save-status-btn');
                    const cancelBtn = row.querySelector('.cancel-status-btn');
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';

                } else {
                    if (statusMessage) {
                        statusMessage.textContent = 'Error: ' + (data.error || 'Unknown error');
                        statusMessage.className = 'status-message error';
                    }
                    // ✅ Восстанавливаем кнопки даже при ошибке
                    const editBtn = row.querySelector('.edit-status-btn');
                    const saveBtn = row.querySelector('.save-status-btn');
                    const cancelBtn = row.querySelector('.cancel-status-btn');
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';
                }
            } catch (err) {
                if (statusMessage) {
                    statusMessage.textContent = 'Network error';
                    statusMessage.className = 'status-message error';
                }

                // ✅ Восстанавливаем кнопки при Network Error
                const editBtn = row.querySelector('.edit-status-btn');
                const saveBtn = row.querySelector('.save-status-btn');
                const cancelBtn = row.querySelector('.cancel-status-btn');
                editBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            }
        });
    });
});