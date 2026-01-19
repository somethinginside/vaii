document.addEventListener('DOMContentLoaded', function () {
    console.log('Admin Products JS loaded');

    // ✅ Создаём WeakMap для хранения ссылок на input
    const productInputs = new WeakMap();

    // === PRODUCT EDITING ===

    // === ENABLE EDITING ===
    function enableProductEditing(button) {
        const row = button.closest('tr');
        if (!row) return;

        const statusDiv = row.querySelector('.status');
        if (statusDiv) statusDiv.textContent = '';

        // ✅ Сохраняем оригинальные значения в data-атрибутах
        row.setAttribute('data-original-name', row.querySelector('.field[data-field="name"] .text').textContent);
        row.setAttribute('data-original-price', row.querySelector('.field[data-field="price"] .text').textContent);
        row.setAttribute('data-original-stock_quantity', row.querySelector('.field[data-field="stock_quantity"] .text').textContent);
        row.setAttribute('data-original-category', row.querySelector('.field[data-field="category"] .text').textContent);
        row.setAttribute('data-original-description', row.querySelector('.field[data-field="description"] .text').textContent);
        row.setAttribute('data-original-image', row.querySelector('.field[data-field="image"] .text').textContent);

        // ✅ Показываем input, скрываем span
        row.querySelectorAll('.field').forEach(field => {
            const span = field.querySelector('.text');
            const input = field.querySelector('.edit-input');
            if (span) span.style.display = 'none';
            if (input) input.style.display = 'block';
        });

        // ✅ Для image — показываем input, скрываем изображение
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

        // ✅ Сохраняем оригинальные значения в data-атрибутах
        row.setAttribute('data-original-name', row.querySelector('.field[data-field="name"] .text').textContent);
        row.setAttribute('data-original-price', row.querySelector('.field[data-field="price"] .text').textContent);
        row.setAttribute('data-original-stock_quantity', row.querySelector('.field[data-field="stock_quantity"] .text').textContent);
        row.setAttribute('data-original-category', row.querySelector('.field[data-field="category"] .text').textContent);
        row.setAttribute('data-original-description', row.querySelector('.field[data-field="description"] .text').textContent);
        row.setAttribute('data-original-image', row.querySelector('.field[data-field="image"] .text').textContent);

        // ✅ Сохраняем ссылки на input в WeakMap
        const nameInput = row.querySelector('.field[data-field="name"] .edit-input');
        const priceInput = row.querySelector('.field[data-field="price"] .edit-input');
        const stockInput = row.querySelector('.field[data-field="stock_quantity"] .edit-input');
        const categoryInput = row.querySelector('.field[data-field="category"] .edit-input');
        const descInput = row.querySelector('.field[data-field="description"] .edit-input');
        const imageInput = row.querySelector('.field[data-field="image"] .edit-input');

        productInputs.set(row, {
            name: nameInput,
            price: priceInput,
            stock: stockInput,
            category: categoryInput,
            description: descInput,
            image: imageInput
        });
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

                // ✅ Получаем оригинальные значения из data-атрибутов
                const originalName = row.getAttribute('data-original-name');
                const originalPrice = row.getAttribute('data-original-price');
                const originalStock = row.getAttribute('data-original-stock_quantity');
                const originalCategory = row.getAttribute('data-original-category');
                const originalDesc = row.getAttribute('data-original-description');
                const originalImage = row.getAttribute('data-original-image');

                // ✅ Восстанавливаем span с оригинальными значениями
                row.querySelector('.field[data-field="name"] .text').textContent = originalName;
                row.querySelector('.field[data-field="price"] .text').textContent = originalPrice;
                row.querySelector('.field[data-field="stock_quantity"] .text').textContent = originalStock;
                row.querySelector('.field[data-field="category"] .text').textContent = originalCategory;
                row.querySelector('.field[data-field="description"] .text').textContent = originalDesc;

                // ✅ Обновляем изображение (только src, не текст)
                row.querySelector('.field[data-field="image"] img').src = originalImage;

                // ✅ Скрываем input, показываем span (для всех, кроме image)
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

                // ✅ Удаляем data-атрибуты
                row.removeAttribute('data-original-name');
                row.removeAttribute('data-original-price');
                row.removeAttribute('data-original-stock_quantity');
                row.removeAttribute('data-original-category');
                row.removeAttribute('data-original-description');
                row.removeAttribute('data-original-image');
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

                // ✅ Получаем сохранённые input из WeakMap
                const inputs = productInputs.get(row);
                if (!inputs) {
                    if (statusDiv) {
                        statusDiv.textContent = 'Error: No input data found';
                        statusDiv.className = 'status error';
                    }
                    return;
                }

                const name = inputs.name.value;
                const price = inputs.price.value;
                const stockQuantity = inputs.stock.value;
                const category = inputs.category.value;
                const description = inputs.description.value;
                const imageUrl = inputs.image.value;

                // ✅ Отладка — выводим значения
                console.log({
                    name, price, stockQuantity, category, description, imageUrl,
                    nameValid: !!name,
                    priceValid: !!price,
                    stockValid: !!stockQuantity,
                    catValid: !!category,
                    descValid: !!description,
                    imageValid: !!imageUrl
                });

                if (!name || !price || !stockQuantity || !category || !description || !imageUrl) {
                    if (statusDiv) {
                        statusDiv.textContent = 'All fields required';
                        statusDiv.className = 'status error';
                    }
                    return;
                }

                try {
                    console.log('Sending request to /update_product.php...');

                    const res = await fetch('/update_product.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id, name, price, stock_quantity: stockQuantity, category, description, image: imageUrl })
                    });

                    console.log('Response object:', res);
                    console.log('Response status:', res.status);
                    console.log('Response ok:', res.ok);
                    console.log('Response type:', res.type);

                    if (!res.ok) {
                        console.error('HTTP error:', res.status);
                        if (statusDiv) {
                            statusDiv.textContent = 'HTTP error: ' + res.status;
                            statusDiv.className = 'status error';
                        }
                        return;
                    }

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
                        row.querySelector('.field[data-field="price"] .text').textContent = parseFloat(price).toFixed(2);
                        row.querySelector('.field[data-field="stock_quantity"] .text').textContent = parseInt(stockQuantity);
                        row.querySelector('.field[data-field="category"] .text').textContent = category;
                        row.querySelector('.field[data-field="description"] .text').textContent = description;

                        // ✅ Обновляем изображение (только src, не текст)
                        row.querySelector('.field[data-field="image"] img').src = imageUrl;

                        // ✅ Скрываем input, показываем span (для всех, кроме image)
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
                    console.error('Fetch error:', err); // ✅ Вот тут будет Network Error
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