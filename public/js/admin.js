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
                console.error('Fetch error:', err);
                if (statusDiv) {
                    statusDiv.textContent = 'Network error';
                    statusDiv.className = 'status error';
                }
            }
        });
    });

    // === INLINE EDITING ===
    function enableEditing(button) {
        const row = button.closest('tr');
        if (!row) return;

        const statusDiv = row.querySelector('.status');
        if (statusDiv) statusDiv.textContent = '';

        // ✅ Показываем input, скрываем span
        row.querySelectorAll('.field').forEach(field => {
            const span = field.querySelector('.text');
            const input = field.querySelector('.edit-input');
            if (span) span.style.display = 'none';
            if (input) input.style.display = 'block';
        });

        // ✅ Скрываем кнопку Edit
        button.style.display = 'none';
        const saveBtn = row.querySelector('.save-btn');
        const cancelBtn = row.querySelector('.cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            console.log('Edit button clicked!');
            enableEditing(this);
        });
    });

    // === CANCEL ===
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');

            // ✅ Скрываем input, показываем span
            row.querySelectorAll('.field').forEach(field => {
                const fieldType = field.dataset.field;
                if (fieldType !== 'image') {
                    const span = field.querySelector('.text');
                    const input = field.querySelector('.edit-input');
                    if (span) span.style.display = 'inline';
                    if (input) input.style.display = 'none';
                } else {
                    const input = field.querySelector('.edit-input');
                    if (input) input.style.display = 'none';
                    const img = field.querySelector('img');
                    if (img) img.style.display = 'block';
                }


            });

            // ✅ Возвращаем кнопки в исходное состояние
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';
        });
    });

    // === SAVE ===
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const statusDiv = row.querySelector('.status');

            // ✅ Получаем значения из input
            const nameInput = row.querySelector('.field[data-field="name"] .edit-input');
            const colorInput = row.querySelector('.field[data-field="color"] .edit-input');
            const ageInput = row.querySelector('.field[data-field="age"] .edit-input');
            const descInput = row.querySelector('.field[data-field="description"] .edit-input');
            const imageInput = row.querySelector('.field[data-field="image"] .edit-input');

            const name = nameInput.value;
            const color = colorInput.value;
            const age = ageInput.value;
            const desc = descInput.value;
            const imageUrl = imageInput.value;

            if (!name || !color || !age || !desc || !imageUrl) {
                if (statusDiv) {
                    statusDiv.textContent = 'All fields required';
                    statusDiv.className = 'status error';
                }
                return;
            }

            try {
                console.log('Sending request to /update_unicorn.php...');
                console.log('Data being sent:', { id, name, color, age, description: desc, image: imageUrl });

                const res = await fetch('/update_unicorn.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id, name, color, age, description: desc, image: imageUrl })
                });

                console.log('Response status:', res.status);

                const responseText = await res.text(); // ✅ Получаем как текст
                console.log('Raw response:', responseText);

                let data;
                try {
                    data = JSON.parse(responseText); // ✅ Парсим JSON
                } catch (parseErr) {
                    console.error('JSON parse error:', parseErr);
                    console.error('Raw response was:', responseText);
                    throw new Error('Invalid JSON response');
                }

                console.log('Parsed ', data);

                if (data.success) {
                    if (statusDiv) {
                        statusDiv.textContent = 'Saved';
                        statusDiv.className = 'status success';
                    }

                    // ✅ Обновляем span с новыми значениями
                    row.querySelector('.field[data-field="name"] .text').textContent = name;
                    row.querySelector('.field[data-field="color"] .text').textContent = color;
                    row.querySelector('.field[data-field="age"] .text').textContent = age;
                    row.querySelector('.field[data-field="description"] .text').textContent = desc;
                    row.querySelector('.field[data-field="image"] .text').textContent = imageUrl;

                    // ✅ Обновляем изображение
                    row.querySelector('.field[data-field="image"] img').src = imageUrl;

                    // ✅ Скрываем input, показываем span
                    row.querySelectorAll('.field').forEach(field => {
                        const fieldType = field.dataset.field;
                        if (fieldType !== 'image') {
                            const span = field.querySelector('.text');
                            const input = field.querySelector('.edit-input');
                            if (span) span.style.display = 'inline';
                            if (input) input.style.display = 'none';
                        } else {
                            const input = field.querySelector('.edit-input');
                            if (input) input.style.display = 'none';
                            const img = field.querySelector('img');
                            if (img) img.style.display = 'block';
                        }

                    });

                    // ✅ Возвращаем кнопки в исходное состояние
                    const editBtn = row.querySelector('.edit-btn');
                    const saveBtn = row.querySelector('.save-btn');
                    const cancelBtn = row.querySelector('.cancel-btn');
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';

                } else {
                    if (statusDiv) {
                        statusDiv.textContent = 'Error: ' + (data.error || 'Unknown error');
                        statusDiv.className = 'status error';
                    }
                    // ✅ Восстанавливаем кнопки даже при ошибке
                    const editBtn = row.querySelector('.edit-btn');
                    const saveBtn = row.querySelector('.save-btn');
                    const cancelBtn = row.querySelector('.cancel-btn');
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';
                }
            } catch (err) {
                console.error('Save error:', err);
                if (statusDiv) {
                    statusDiv.textContent = 'Network error';
                    statusDiv.className = 'status error';
                }

                // ✅ Восстанавливаем кнопки при Network Error
                const editBtn = row.querySelector('.edit-btn');
                const saveBtn = row.querySelector('.save-btn');
                const cancelBtn = row.querySelector('.cancel-btn');
                editBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            }
        });
    });

    // === DELETE UNICORN ===
    document.querySelectorAll('.delete-btn').forEach(btn => {
        btn.addEventListener('click', async function () {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const name = row.querySelector('.field[data-field="name"] .text')?.textContent || '???';

            if (!confirm(`Delete unicorn "${name}"?`)) return;

            const statusDiv = row.querySelector('.status');
            if (statusDiv) statusDiv.textContent = 'Deleting...';

            try {
                const res = await fetch('/delete_unicorn.php', {
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
                        statusDiv.textContent = 'Error: ' + (data.error || 'Unknown error');
                        statusDiv.className = 'status error';
                    }
                }
            } catch (err) {
                if (statusDiv) statusDiv.textContent = 'Network error';
            }
        });
    });
    // === PRODUCT EDITING ===

    // === ENABLE EDITING ===
    function enableProductEditing(button) {
        const row = button.closest('tr');
        if (!row) return;

        const statusDiv = row.querySelector('.status');
        if (statusDiv) statusDiv.textContent = '';

        // ✅ Показываем input, скрываем span
        row.querySelectorAll('.field').forEach(field => {
            const span = field.querySelector('.text');
            const input = field.querySelector('.edit-input');
            if (span) span.style.display = 'none';
            if (input) input.style.display = 'block';
        });

        const imageField = row.querySelector('.field[data-field="image"]');
        if (imageField) {
            imageField.querySelector('img').style.display = 'none';
            imageField.querySelector('.edit-input').style.display = 'block';
        }

        // ✅ Скрываем кнопку Edit
        button.style.display = 'none';
        const saveBtn = row.querySelector('.save-btn');
        const cancelBtn = row.querySelector('.cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        // ✅ Проверяем, является ли кнопка для продукта
        if (btn.closest('tr').querySelector('.field[data-field="name"]')) {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                console.log('Edit product button clicked!');
                enableProductEditing(this);
            });
        }
    });

    // === CANCEL PRODUCT ===
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        // ✅ Проверяем, является ли кнопка для продукта
        if (btn.closest('tr').querySelector('.field[data-field="name"]')) {
            btn.addEventListener('click', function () {
                const row = this.closest('tr');

                // ✅ Скрываем input, показываем span
                row.querySelectorAll('.field').forEach(field => {
                    const fieldType = field.dataset.field;
                    if (fieldType !== 'image') {
                        const span = field.querySelector('.text');
                        const input = field.querySelector('.edit-input');
                        if (span) span.style.display = 'inline';
                        if (input) input.style.display = 'none';
                    } else {
                        // ✅ Для image — скрываем input, показываем изображение
                        const input = field.querySelector('.edit-input');
                        if (input) input.style.display = 'none';
                        const img = field.querySelector('img');
                        if (img) img.style.display = 'block';
                    }
                });

                // ✅ Возвращаем кнопки в исходное состояние
                const editBtn = row.querySelector('.edit-btn');
                const saveBtn = row.querySelector('.save-btn');
                const cancelBtn = row.querySelector('.cancel-btn');
                editBtn.style.display = 'inline-block';
                saveBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
            });
        }
    });

    // === SAVE PRODUCT ===
    document.querySelectorAll('.save-btn').forEach(btn => {
        // ✅ Проверяем, является ли кнопка для продукта
        if (btn.closest('tr').querySelector('.field[data-field="name"]')) {
            btn.addEventListener('click', async function () {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const statusDiv = row.querySelector('.status');

                // ✅ Получаем значения из input
                const nameInput = row.querySelector('.field[data-field="name"] .edit-input');
                const priceInput = row.querySelector('.field[data-field="price"] .edit-input');
                const stockInput = row.querySelector('.field[data-field="stock_quantity"] .edit-input');
                const categoryInput = row.querySelector('.field[data-field="category"] .edit-input');
                const descInput = row.querySelector('.field[data-field="description"] .edit-input');
                const imageInput = row.querySelector('.field[data-field="image"] .edit-input');

                const name = nameInput.value;
                const price = priceInput.value;
                const stockQuantity = stockInput.value;
                const category = categoryInput.value;
                const description = descInput.value;
                const imageUrl = imageInput.value;

                if (!name || !price || !stockQuantity || !category || !description || !imageUrl) {
                    if (statusDiv) {
                        statusDiv.textContent = 'All fields required';
                        statusDiv.className = 'status error';
                    }
                    return;
                }

                try {
                    const res = await fetch('/update_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, name, price, stock_quantity: stockQuantity, category, description, image: imageUrl })
                    });
                    const data = await res.json();

                    if (data.success) {
                        if (statusDiv) {
                            statusDiv.textContent = 'Saved';
                            statusDiv.className = 'status success';
                        }

                        // ✅ Обновляем span с новыми значениями
                        row.querySelector('.field[data-field="name"] .text').textContent = name;
                        row.querySelector('.field[data-field="price"] .text').textContent = parseFloat(price).toFixed(2);
                        row.querySelector('.field[data-field="stock_quantity"] .text').textContent = parseInt(stockQuantity);
                        row.querySelector('.field[data-field="category"] .text').textContent = category;
                        row.querySelector('.field[data-field="description"] .text').textContent = description;

                        row.querySelector('.field[data-field="image"] img').src = imageUrl;

                        // ✅ Скрываем input, показываем span
                        row.querySelectorAll('.field').forEach(field => {
                            const fieldType = field.dataset.field;
                            if (fieldType !== 'image') {
                                const span = field.querySelector('.text');
                                const input = field.querySelector('.edit-input');
                                if (span) span.style.display = 'inline';
                                if (input) input.style.display = 'none';
                            } else {
                                // ✅ Для image — скрываем input, показываем изображение
                                const input = field.querySelector('.edit-input');
                                if (input) input.style.display = 'none';
                                const img = field.querySelector('img');
                                if (img) img.style.display = 'block';
                            }
                        });

                        // ✅ Возвращаем кнопки в исходное состояние
                        const editBtn = row.querySelector('.edit-btn');
                        const saveBtn = row.querySelector('.save-btn');
                        const cancelBtn = row.querySelector('.cancel-btn');
                        editBtn.style.display = 'inline-block';
                        saveBtn.style.display = 'none';
                        cancelBtn.style.display = 'none';

                    } else {
                        if (statusDiv) {
                            statusDiv.textContent = 'Error: ' + (data.error || 'Unknown error');
                            statusDiv.className = 'status error';
                        }
                        // ✅ Восстанавливаем кнопки даже при ошибке
                        const editBtn = row.querySelector('.edit-btn');
                        const saveBtn = row.querySelector('.save-btn');
                        const cancelBtn = row.querySelector('.cancel-btn');
                        editBtn.style.display = 'inline-block';
                        saveBtn.style.display = 'none';
                        cancelBtn.style.display = 'none';
                    }
                } catch (err) {
                    if (statusDiv) {
                        statusDiv.textContent = 'Network error';
                        statusDiv.className = 'status error';
                    }

                    // ✅ Восстанавливаем кнопки при Network Error
                    const editBtn = row.querySelector('.edit-btn');
                    const saveBtn = row.querySelector('.save-btn');
                    const cancelBtn = row.querySelector('.cancel-btn');
                    editBtn.style.display = 'inline-block';
                    saveBtn.style.display = 'none';
                    cancelBtn.style.display = 'none';
                }
            });
        }
    });

    // === DELETE PRODUCT ===
    document.querySelectorAll('.delete-btn').forEach(btn => {
        // ✅ Проверяем, является ли кнопка для продукта
        if (btn.closest('tr').querySelector('.field[data-field="name"]')) {
            btn.addEventListener('click', async function () {
                const row = this.closest('tr');
                const id = row.dataset.id;
                const name = row.querySelector('.field[data-field="name"] .text')?.textContent || '???';

                if (!confirm(`Delete product "${name}"?`)) return;

                const statusDiv = row.querySelector('.status');
                if (statusDiv) statusDiv.textContent = 'Deleting...';

                try {
                    const res = await fetch('/delete_product.php', {
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
                            statusDiv.textContent = 'Error: ' + (data.error || 'Unknown error');
                            statusDiv.className = 'status error';
                        }
                    }
                } catch (err) {
                    if (statusDiv) statusDiv.textContent = 'Network error';
                }
            });
        }
    });
});