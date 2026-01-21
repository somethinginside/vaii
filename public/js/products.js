// ✅ Объявляем переменные
let selectedCategory = '';
let selectedPriceSort = 'default';
let selectedNameFilter = '';

document.addEventListener('DOMContentLoaded', function () {

    console.log('Products JS loaded');

    const filterButton = document.getElementById('filter-button');
    const dropdown = document.getElementById('dropdown-content');

    if (filterButton) {
        filterButton.addEventListener('click', function () {
            dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
            const arrow = filterButton.querySelector('svg');
            if (arrow) {
                arrow.style.transform = dropdown.style.display === 'block' ? 'rotate(180deg)' : 'rotate(0deg)';
            }
        });
    }

    // ✅ Все элементы категорий и сортировки
    const categoryItems = document.querySelectorAll('.dropdown-item[data-category]');
    const priceSortItems = document.querySelectorAll('.dropdown-item[data-price-sort]');
    const nameInput = document.getElementById('name-filter-input');

    // ✅ Вспомогательная функция для обновления фильтров
    function refreshFilters() {
        applyFilters();
        updateActiveFiltersDisplay();
    }

    // ✅ Категории
    categoryItems.forEach(item => {
        item.addEventListener('click', function () {
            selectedCategory = this.getAttribute('data-category');
            // Сброс выделения
            categoryItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            refreshFilters();
        });

        // ✅ Hover-эффект
        item.addEventListener('mouseenter', () => {
            if (!item.classList.contains('active')) {
                item.style.backgroundColor = '#f0f5ff';
            }
        });
        item.addEventListener('mouseleave', () => {
            if (!item.classList.contains('active')) {
                item.style.backgroundColor = '';
            }
        });
    });

    // ✅ Сортировка по цене
    priceSortItems.forEach(item => {
        item.addEventListener('click', function () {
            selectedPriceSort = this.getAttribute('data-price-sort');
            // Сброс выделения
            priceSortItems.forEach(i => i.classList.remove('active'));
            this.classList.add('active');
            refreshFilters();
        });

        // ✅ Hover-эффект
        item.addEventListener('mouseenter', () => {
            if (!item.classList.contains('active')) {
                item.style.backgroundColor = '#f0f5ff';
            }
        });
        item.addEventListener('mouseleave', () => {
            if (!item.classList.contains('active')) {
                item.style.backgroundColor = '';
            }
        });
    });

    // ✅ Поиск по названию
    if (nameInput) {
        let debounceTimer;
        nameInput.addEventListener('input', function () {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                selectedNameFilter = this.value.trim().toLowerCase();
                refreshFilters();
            }, 300); // небольшая задержка, чтобы не фильтровать на каждый символ
        });
    }

    // ✅ Закрытие выпадающего меню при клике вне его
    document.addEventListener('click', function (event) {
        if (
            dropdown.style.display === 'block' &&
            !dropdown.contains(event.target) &&
            !filterButton.contains(event.target)
        ) {
            dropdown.style.display = 'none';
            const arrow = filterButton.querySelector('svg');
            if (arrow) arrow.style.transform = 'rotate(0deg)';
        }
    });

    // ✅ Add to cart
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const id = this.getAttribute('data-id');
            addToCart(id);
        });
    });
});

// ✅ Применение фильтров
function applyFilters() {
    const products = document.querySelectorAll('.product-card');

    products.forEach(product => {
        product.style.display = 'block';
    });

    // Фильтр по категории
    if (selectedCategory) {
        products.forEach(p => {
            if (p.getAttribute('data-category') !== selectedCategory) {
                p.style.display = 'none';
            }
        });
    }

    // Фильтр по названию
    if (selectedNameFilter) {
        products.forEach(p => {
            const name = p.querySelector('h3').textContent.toLowerCase();
            if (!name.includes(selectedNameFilter)) {
                p.style.display = 'none';
            }
        });
    }

    // Сортировка
    const visible = Array.from(products).filter(p => p.style.display !== 'none');
    if (selectedPriceSort !== 'default') {
        visible.sort((a, b) => {
            const aPrice = parseFloat(a.querySelector('.price').textContent.replace('$', ''));
            const bPrice = parseFloat(b.querySelector('.price').textContent.replace('$', ''));
            return selectedPriceSort === 'low-high' ? aPrice - bPrice : bPrice - aPrice;
        });

        const container = document.getElementById('products-container');
        visible.forEach(p => container.appendChild(p));
    }
}

// ✅ Отображение активных фильтров под заголовком
function updateActiveFiltersDisplay() {
    const container = document.querySelector('.container');
    const existing = container.querySelector('.active-filters-display');

    // Удаляем старый блок, если есть
    if (existing) existing.remove();

    // Собираем активные фильтры
    const filters = [];

    if (selectedCategory) {
        filters.push({
            type: 'category',
            label: `Category: ${selectedCategory}`,
            clear: () => {
                selectedCategory = '';
                document.querySelectorAll('.dropdown-item[data-category]').forEach(i => i.classList.remove('active'));
                updateActiveFiltersDisplay();
                applyFilters();
            }
        });
    }

    if (selectedNameFilter) {
        filters.push({
            type: 'name',
            label: `Name: ${selectedNameFilter}`,
            clear: () => {
                selectedNameFilter = '';
                document.getElementById('name-filter-input').value = '';
                updateActiveFiltersDisplay();
                applyFilters();
            }
        });
    }

    if (selectedPriceSort && selectedPriceSort !== 'default') {
        let label = '';
        switch (selectedPriceSort) {
            case 'low-high': label = 'Price: Low → High'; break;
            case 'high-low': label = 'Price: High → Low'; break;
        }
        filters.push({
            type: 'price',
            label,
            clear: () => {
                selectedPriceSort = 'default';
                document.querySelectorAll('.dropdown-item[data-price-sort]').forEach(i => i.classList.remove('active'));
                updateActiveFiltersDisplay();
                applyFilters();
            }
        });
    }

    // Если нет активных фильтров — ничего не показываем
    if (filters.length === 0) return;

    // Создаём блок
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

    container.insertBefore(display, document.getElementById('products-container'));
}

// ✅ Добавление в корзину
function addToCart(productId) {
    fetch('/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: 1 })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Added to cart');
            updateCartCount?.(); // безопасный вызов, если функция существует
        } else {
            console.error('❌ Cart error:', data.error);
        }
    })
    .catch(err => console.error('Fetch error:', err));
}