document.addEventListener('DOMContentLoaded', function () {
    console.log('Products JS loaded');

    // ✅ Обновляем счётчик корзины при загрузке
    if (typeof updateCartCount === 'function') {
        updateCartCount();
    }

    // ✅ Обработка кнопок "Add to Cart"
    document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const productId = this.dataset.id;
            const quantity = parseInt(this.dataset.quantity) || 1;

            console.log('Add to Cart button clicked:', { productId, quantity });

            if (typeof addToCart === 'function') {
                addToCart(productId, quantity);
            } else {
                alert('addToCart function not found');
            }
        });
    });

    // ✅ Фильтр по категориям (если используется)
    const categoryFilter = document.getElementById('category-filter');
    if (categoryFilter) {
        categoryFilter.addEventListener('change', filterProducts);
    }
});

// ✅ Функции для фильтра
function filterProducts() {
    const filter = document.getElementById('category-filter').value;
    const cards = document.querySelectorAll('.product-card');

    cards.forEach(card => {
        if (filter === '' || card.dataset.category === filter) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}
function toggleDropdown() {
    const dropdown = document.getElementById('filter-dropdpwn');
    dropdown.classList.toggle('active');
}

