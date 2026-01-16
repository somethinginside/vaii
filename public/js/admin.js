// Inline editing for admin tables
document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin JS loaded');

    // === DELETE PRODUCT ===
    document.querySelectorAll('.delete-product-btn').forEach(btn => {
        console.log('Handler assigned to delete-product-btn');
        btn.addEventListener('click', async function () {
            console.log('Delete clicked!');
            const row = this.closest('tr');
            const id = row.dataset.id;
            const name = row.querySelector('[data-field="name"]')?.textContent || '???';

            if (!confirm(`Delete product "${name}"?`)) return;

            const statusDiv = row.querySelector('.status');
            if (statusDiv) statusDiv.textContent = 'Deleting...';

            try {
                console.log('Sending request to delete_product.php...');
                const res = await fetch('/delete_product.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });
                console.log('Response received:', res.status);

                const data = await res.json();
                console.log('Data received:', data);

                if (data.success) {
                    if (statusDiv) {
                        statusDiv.textContent = 'Deleted';
                        statusDiv.className = 'status success';
                    }
                    setTimeout(() => row.remove(), 1000);
                } else {
                    if (statusDiv) {
                        statusDiv.textContent = 'Error: ' + (data.error || 'Unknown error');
                        statusDiv.className = 'status error';
                    }
                }
            } catch (err) {
                console.error('Fetch error:', err); // ← Вот здесь мы ловим Network Error
                if (statusDiv) {
                    statusDiv.textContent = 'Network error';
                    statusDiv.className = 'status error';
                }
            }
        });
    });

    // Editing unicorns
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
            console.error('Fields not found');
            return;
        }

        const name = nameField.textContent.trim();
        const color = colorField.textContent.trim();
        const age = ageField.textContent.trim();
        const desc = descField.textContent.trim();
        const imageUrl = imageField.textContent.trim();

        nameField.innerHTML = `<input type="text" value="${name.replace(/"/g, '&quot;')}" style="width:100%">`;
        colorField.innerHTML = `<input type="text" value="${color.replace(/"/g, '&quot;')}" style="width:100%">`;
        ageField.innerHTML = `<input type="number" value="${age}" min="0" style="width:100%">`;
        descField.innerHTML = `<textarea rows="2" style="width:100%">${desc}</textarea>`;

        imageField.closest('td').innerHTML = `
            <img src="${imageUrl}" alt="Preview" width="60" style="display:block; margin-bottom:5px; border:1px solid #ccc; border-radius:6px;">
            <input type="text" value="${imageUrl}" placeholder="URL изображения" style="width:100%;">
        `;

        button.style.display = 'none';
        const saveBtn = row.querySelector('.save-btn');
        const cancelBtn = row.querySelector('.cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            enableEditing(this);
        });
    });

    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', () => location.reload());
    });

    // Saving unicorn
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
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

    // Deleting unicorn
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
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