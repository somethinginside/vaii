let selectedAge = '';
let selectedColor = '';

document.addEventListener('DOMContentLoaded', function () {
    console.log('us loaded')
    
    //Filters
    const filterButton = document.getElementById('filter-button');
    const dropdown = document.getElementById('dropdown-content');

    if (!filterButton || !dropdown) return;

    // Toggle dropdown
    filterButton.addEventListener('click', function (event) {
        event.stopPropagation();
        const computedStyle = window.getComputedStyle(dropdown);
        if (computedStyle.display === 'block') {
            dropdown.style.display = 'none';
        } else {
            dropdown.style.display = 'block';
        }
        const arrow = filterButton.querySelector('svg');
        if (arrow) {
            arrow.style.transform = computedStyle.display === 'block' ? 'rotate(0deg)' : 'rotate(180deg)';
        }
    });

    // Close on outside click
    document.addEventListener('click', function (event) {
        if (dropdown.style.display === 'block') {
            if (!dropdown.contains(event.target) && event.target !== filterButton) {
                dropdown.style.display = 'none';
                const arrow = filterButton.querySelector('svg');
                if (arrow) arrow.style.transform = 'rotate(0deg)';
            }
        }
    });

    // Age items
    const ageItems = document.querySelectorAll('.dropdown-item[data-age]');
    ageItems.forEach(item => {
        item.addEventListener('click', function () {
            selectedAge = this.getAttribute('data-age');
            ageItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
            updateActiveFiltersDisplay();
        });
        addHoverEffect(item);
    });

    // Color items
    const colorItems = document.querySelectorAll('.dropdown-item[data-color]');
    colorItems.forEach(item => {
        item.addEventListener('click', function () {
            selectedColor = this.getAttribute('data-color');
            colorItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            applyFilters();
            updateActiveFiltersDisplay();
        });
        addHoverEffect(item);
    });

    // ✅ Закрытие модального окна при клике вне карточки
    const modal = document.getElementById('unicorn-modal');
    if (modal) {
        modal.addEventListener('click', function (e) {
            if (e.target === this) {
                closeUnicornModal();
            }
        });
    }
});

function addHoverEffect(el) {
    el.addEventListener('mouseenter', () => {
        if (!el.classList.contains('active')) el.style.backgroundColor = '#f0f5ff';
    });
    el.addEventListener('mouseleave', () => {
        if (!el.classList.contains('active')) el.style.backgroundColor = '';
    });
}

function applyFilters() {
    const cards = document.querySelectorAll('.unicorn-card');
    cards.forEach(card => card.style.display = 'block');

    if (selectedAge) {
        cards.forEach(card => {
            if (card.getAttribute('data-age') !== selectedAge) card.style.display = 'none';
        });
    }

    if (selectedColor) {
        cards.forEach(card => {
            if (card.getAttribute('data-color') !== selectedColor) card.style.display = 'none';
        });
    }
}

function updateActiveFiltersDisplay() {
    const container = document.querySelector('.container');
    const existing = container.querySelector('.active-filters-display');
    if (existing) existing.remove();

    const filters = [];

    if (selectedAge) {
        filters.push({
            label: `Age: ${selectedAge} years`,
            clear: () => {
                selectedAge = '';
                document.querySelectorAll('.dropdown-item[data-age]').forEach(i => i.classList.remove('active'));
                updateActiveFiltersDisplay();
                applyFilters();
            }
        });
    }

    if (selectedColor) {
        filters.push({
            label: `Color: ${selectedColor}`,
            clear: () => {
                selectedColor = '';
                document.querySelectorAll('.dropdown-item[data-color]').forEach(i => i.classList.remove('active'));
                updateActiveFiltersDisplay();
                applyFilters();
            }
        });
    }

    if (filters.length === 0) return;

    const display = document.createElement('div');
    display.className = 'active-filters-display';
    display.style = `
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
        margin: 10px 0 20px;
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
        border: 1px solid #e9ecef;
    `;

    filters.forEach(f => {
        const tag = document.createElement('span');
        tag.textContent = f.label;
        tag.style = `
            background: linear-gradient(135deg, #6e8efb 0%, #a777e3 100%);
            color: white;
            padding: 5px 12px;
            border-radius: 16px;
            font-size: 13px;
            cursor: pointer;
            transition: opacity 0.2s;
        `;
        tag.addEventListener('mouseenter', () => tag.style.opacity = '0.9');
        tag.addEventListener('mouseleave', () => tag.style.opacity = '1');
        tag.addEventListener('click', f.clear);
        display.appendChild(tag);
    });

    container.insertBefore(display, document.getElementById('unicorns-container'));
}

// === МОДАЛЬНОЕ ОКНО ===
function openUnicornModal(unicornId) {
    fetch(`/get_unicorn.php?id=${unicornId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const u = data.unicorn;
                const isInFavourites = data.is_in_favourites || false;

                document.getElementById('modal-content').innerHTML = `
                    <img src="${u.image}" 
                         style="width:100%; border-radius:12px; margin-bottom:20px; max-height:300px; object-fit:cover;">
                    <h2>${u.name}</h2>
                    <p><strong>Age:</strong> ${u.age} years</p>
                    <p><strong>Color:</strong> ${u.color}</p>
                    <div style="margin-top:20px;">
                        <h3>Description</h3>
                        <p>${u.description}</p>
                    </div>
                    <button id="modal-favourite-btn"
                            style="margin-top: 20px; background: ${isInFavourites ? '#28a745' : 'linear-gradient(135deg, #6e8efb 0%, #a777e3 100%)'}; color: white; border: none; padding: 10px 20px; border-radius: 20px; cursor: pointer; width: 100%;">
                        ${isInFavourites ? '✓ In Favourites' : '☆ Add to Favourites'}
                    </button>
                `;

                document.getElementById('unicorn-modal').style.display = 'flex';

                // Навешиваем обработчик
                document.getElementById('modal-favourite-btn').addEventListener('click', function () {
                    toggleFavourite(unicornId, isInFavourites);
                });
            }
        })
        .catch(err => {
            console.error('Modal load error:', err);
            alert('Failed to load unicorn details.');
        });
}

// === ПЕРЕКЛЮЧЕНИЕ ИЗБРАННОГО ===
async function toggleFavourite(unicornId, currentlyInFavourites) {
    const action = currentlyInFavourites ? 'remove' : 'add';
    const btn = document.getElementById('modal-favourite-btn');

    try {
        const response = await fetch('/toggle_favourite.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                unicorn_id: parseInt(unicornId),
                action: action
            })
        });

        const text = await response.text();
        if (text.trim().startsWith('<')) {
            throw new Error('Server returned HTML instead of JSON');
        }

        const data = JSON.parse(text);
        if (data.success) {
            // Обновляем кнопку
            if (data.is_in_favourites) {
                btn.innerHTML = '✓ In Favourites';
                btn.style.background = '#28a745';
            } else {
                btn.innerHTML = '☆ Add to Favourites';
                btn.style.background = 'linear-gradient(135deg, #6e8efb 0%, #a777e3 100%)';
                btn.disabled = false;
            }
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (err) {
        console.error('[Favourites] Error:', err);
        alert('Failed to update favourites. Check console for details.');
    }
}

function closeUnicornModal() {
    const modal = document.getElementById('unicorn-modal');
    if (modal) {
        modal.style.display = 'none';
    }
}

function removeFromFavourites(unicornId, button) {
    fetch('/toggle_favourite.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ unicorn_id: unicornId, action: 'remove' })
    })
        .then(r => r.json())
        .then(data => {
            if (data.success) button.closest('.product-card').remove();
        });
}