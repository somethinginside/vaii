document.addEventListener('DOMContentLoaded', function() {
    console.log('Admin JS loaded');

    // === INLINE EDITING ===
    function enableEditing(button) {
        const row = button.closest('tr');
        if (!row) return;

        const statusDiv = row.querySelector('.status');
        if (statusDiv) statusDiv.textContent = '';

        // ✅ Сохраняем оригинальный HTML всех полей
        const nameField = row.querySelector('.field[data-field="name"]');
        const colorField = row.querySelector('.field[data-field="color"]');
        const ageField = row.querySelector('.field[data-field="age"]');
        const descField = row.querySelector('.field[data-field="description"]');
        const imageField = row.querySelector('.field[data-field="image"]');

        // ✅ Сохраняем оригинальный HTML
        row.dataset.originalNameHtml = nameField.outerHTML;
        row.dataset.originalColorHtml = colorField.outerHTML;
        row.dataset.originalAgeHtml = ageField.outerHTML;
        row.dataset.originalDescHtml = descField.outerHTML;
        row.dataset.originalImageHtml = imageField.outerHTML;

        // ✅ Сохраняем оригинальные значения
        const originalValues = {
            name: nameField.textContent.trim(),
            color: colorField.textContent.trim(),
            age: ageField.textContent.trim(),
            desc: descField.textContent.trim(),
            image: imageField.textContent.trim()
        };

        // ✅ Сохраняем эти значения в data-атрибутах строки
        row.setAttribute('data-original-name', originalValues.name);
        row.setAttribute('data-original-color', originalValues.color);
        row.setAttribute('data-original-age', originalValues.age);
        row.setAttribute('data-original-desc', originalValues.desc);
        row.setAttribute('data-original-image', originalValues.image);

        if (!nameField || !colorField || !ageField || !descField || !imageField) {
            console.error('Fields not found');
            return;
        }

        const name = nameField.textContent.trim();
        const color = colorField.textContent.trim();
        const age = ageField.textContent.trim();
        const desc = descField.textContent.trim();
        const imageUrl = imageField.textContent.trim();

        // ✅ Заменяем поля на input
        nameField.innerHTML = `<input type="text" value="${name.replace(/"/g, '&quot;')}" style="width:100%">`;
        colorField.innerHTML = `<input type="text" value="${color.replace(/"/g, '&quot;')}" style="width:100%">`;
        ageField.innerHTML = `<input type="number" value="${age}" min="0" style="width:100%">`;
        descField.innerHTML = `<textarea rows="2" style="width:100%">${desc}</textarea>`;

        // ✅ Теперь imageField — это span, а td — его родитель
        const imageTd = imageField.closest('td');
        imageTd.innerHTML = `
            <img src="${imageUrl}" alt="Preview" width="60" style="display:block; margin-bottom:5px; border:1px solid #ccc; border-radius:6px;">
            <input type="text" value="${imageUrl}" placeholder="URL изображения" style="width:100%;">
        `;

        // ✅ Скрываем кнопку Edit
        button.style.display = 'none';
        const saveBtn = row.querySelector('.save-btn');
        const cancelBtn = row.querySelector('.cancel-btn');
        if (saveBtn) saveBtn.style.display = 'inline-block';
        if (cancelBtn) cancelBtn.style.display = 'inline-block';
    }

    document.querySelectorAll('.edit-btn').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Edit button clicked!');
            enableEditing(this);
        });
    });

    // === CANCEL ===
    document.querySelectorAll('.cancel-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const row = this.closest('tr');

            // ✅ Получаем оригинальные значения из data-атрибутов
            const originalName = row.getAttribute('data-original-name');
            const originalColor = row.getAttribute('data-original-color');
            const originalAge = row.getAttribute('data-original-age');
            const originalDesc = row.getAttribute('data-original-desc');
            const originalImage = row.getAttribute('data-original-image');

            if (!originalName || !originalColor || !originalAge || !originalDesc || !originalImage) {
                console.error('Original values not found');
                return;
            }

            // ✅ Восстанавливаем оригинальный HTML
            const nameField = row.querySelector('.field[data-field="name"]');
            const colorField = row.querySelector('.field[data-field="color"]');
            const ageField = row.querySelector('.field[data-field="age"]');
            const descField = row.querySelector('.field[data-field="description"]');
            const imageField = row.querySelector('.field[data-field="image"]');

            if (!nameField || !colorField || !ageField || !descField || !imageField) {
                console.error('Fields not found');
                return;
            }

            nameField.outerHTML = row.dataset.originalNameHtml;
            colorField.outerHTML = row.dataset.originalColorHtml;
            ageField.outerHTML = row.dataset.originalAgeHtml;
            descField.outerHTML = row.dataset.originalDescHtml;
            imageField.outerHTML = row.dataset.originalImageHtml;

            // ✅ Восстанавливаем изображение (img + span) в TD
            const imageTd = imageField.closest('td');
            imageTd.innerHTML = `
                <img src="${originalImage}" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                <span class="field" data-field="image" style="display:none;">${originalImage}</span>
            `;

            // ✅ Возвращаем кнопки в исходное состояние
            const editBtn = row.querySelector('.edit-btn');
            const saveBtn = row.querySelector('.save-btn');
            const cancelBtn = row.querySelector('.cancel-btn');
            editBtn.style.display = 'inline-block';
            saveBtn.style.display = 'none';
            cancelBtn.style.display = 'none';

            // ✅ Удаляем data-атрибуты
            row.removeAttribute('data-original-name');
            row.removeAttribute('data-original-color');
            row.removeAttribute('data-original-age');
            row.removeAttribute('data-original-desc');
            row.removeAttribute('data-original-image');
            delete row.dataset.originalNameHtml;
            delete row.dataset.originalColorHtml;
            delete row.dataset.originalAgeHtml;
            delete row.dataset.originalDescHtml;
            delete row.dataset.originalImageHtml;
        });
    });

    // === SAVE ===
    document.querySelectorAll('.save-btn').forEach(btn => {
        btn.addEventListener('click', async function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const statusDiv = row.querySelector('.status');

            // ✅ Получаем оригинальные значения из data-атрибутов
            const originalName = row.getAttribute('data-original-name');
            const originalColor = row.getAttribute('data-original-color');
            const originalAge = row.getAttribute('data-original-age');
            const originalDesc = row.getAttribute('data-original-desc');
            const originalImage = row.getAttribute('data-original-image');

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
                const res = await fetch('/update_unicorn.php', {
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

                    // ✅ Обновляем текстовые поля
                    const nameField = row.querySelector('.field[data-field="name"]');
                    const colorField = row.querySelector('.field[data-field="color"]');
                    const ageField = row.querySelector('.field[data-field="age"]');
                    const descField = row.querySelector('.field[data-field="description"]');
                    const imageField = row.querySelector('.field[data-field="image"]');

                    nameField.textContent = name;
                    colorField.textContent = color;
                    ageField.textContent = age;
                    descField.textContent = desc;
                    imageField.textContent = imageUrl;

                    // ✅ Восстанавливаем изображение (img + span) в TD
                    const imageTd = imageField.closest('td');
                    imageTd.innerHTML = `
                        <img src="${imageUrl}" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                        <span class="field" data-field="image" style="display:none;">${imageUrl}</span>
                    `;

                    // ✅ Удаляем data-атрибуты
                    row.removeAttribute('data-original-name');
                    row.removeAttribute('data-original-color');
                    row.removeAttribute('data-original-age');
                    row.removeAttribute('data-original-desc');
                    row.removeAttribute('data-original-image');
                    delete row.dataset.originalNameHtml;
                    delete row.dataset.originalColorHtml;
                    delete row.dataset.originalAgeHtml;
                    delete row.dataset.originalDescHtml;
                    delete row.dataset.originalImageHtml;

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

                // ✅ ВОССТАНАВЛИВАЕМ ВСЁ состояние при Network Error
                // ✅ Восстанавливаем изображение (старое значение)
                const imageTd = row.querySelector('.field[data-field="image"]').closest('td');
                imageTd.innerHTML = `
                    <img src="${originalImage}" alt="Image" width="60" style="display:block; margin-bottom:5px; border:1px solid #eee; border-radius: 6px;">
                    <span class="field" data-field="image" style="display:none;">${originalImage}</span>
                `;

                // ✅ Возвращаем кнопки в исходное состояние
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
        btn.addEventListener('click', async function() {
            const row = this.closest('tr');
            const id = row.dataset.id;
            const name = row.querySelector('[data-field="name"]')?.textContent || '???';

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
});