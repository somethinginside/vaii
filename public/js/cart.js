document.addEventListener('DOMContentLoaded', function () {
    console.log('Cart JS loaded');

    // Обновляем счётчик корзины
    updateCartCount();

    // === УПРАВЛЕНИЕ КОЛИЧЕСТВОМ ===
    document.querySelectorAll('.quantity-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const productId = row.dataset.id;
            const quantitySpan = row.querySelector('.quantity-value');
            let quantity = parseInt(quantitySpan.textContent);

            if (this.dataset.action === 'increase') {
                quantity++;
            } else if (this.dataset.action === 'decrease' && quantity > 1) {
                quantity--;
            }

            updateCartItem(productId, quantity);
        });
    });

    // === УДАЛЕНИЕ ТОВАРА ===
    document.querySelectorAll('.btn-remove').forEach(btn => {
        btn.addEventListener('click', function () {
            if (confirm('Remove this item from cart?')) {
                const row = this.closest('tr');
                const productId = row.dataset.id;
                updateCartItem(productId, 0);
            }
        });
    });

    // === ОФОРМЛЕНИЕ ЗАКАЗА ===
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            window.location.href = 'checkout.php';
        });
    }
});

// === ОБНОВЛЕНИЕ ЭЛЕМЕНТА КОРЗИНЫ ===
function updateCartItem(productId, quantity) {
    fetch('/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: parseInt(productId), quantity: parseInt(quantity) })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const row = document.querySelector(`tr[data-id="${productId}"]`);
                if (!row) return;

                if (quantity === 0) {
                    // Удаляем строку
                    row.remove();
                } else {
                    // Обновляем количество
                    row.querySelector('.quantity-value').textContent = quantity;
                    // Обновляем итог по строке
                    const pricePerItem = parseFloat(row.cells[1].textContent.replace('$', ''));
                    row.cells[3].textContent = '$' + (pricePerItem * quantity).toFixed(2);
                }
                // Обновляем общую сумму
                updateTotalPrice();
            } else {
                alert('Error: ' + (data.error || 'Failed to update cart'));
            }
        })
        .catch(err => {
            console.error('Update cart error:', err);
            alert('Network error. Please try again.');
        });
}

// === ОБНОВЛЕНИЕ ОБЩЕЙ СУММЫ ===
function updateTotalPrice() {
    let total = 0;
    document.querySelectorAll('tr[data-id]').forEach(row => {
        const priceText = row.cells[1].textContent.replace('$', '');
        const qty = parseInt(row.querySelector('.quantity-value').textContent);
        total += parseFloat(priceText) * qty;
    });
    const totalEl = document.getElementById('total-price');
    if (totalEl) {
        totalEl.textContent = total.toFixed(2);
    }
}

// === ДОБАВЛЕНИЕ В КОРЗИНУ (для других страниц) ===
function addToCart(productId, quantity = 1) {
    if (!productId || isNaN(productId) || productId <= 0) {
        console.error('Invalid product_id:', productId);
        return;
    }
    if (!quantity || isNaN(quantity) || quantity <= 0) {
        quantity = 1;
    }

    fetch('/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: parseInt(productId), quantity: parseInt(quantity) })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount();
                // Можно добавить всплывающее уведомление
            } else {
                alert('Error: ' + (data.error || 'Could not add to cart'));
            }
        })
        .catch(err => {
            console.error('Add to cart error:', err);
            alert('Network error');
        });
}

// === ОБНОВЛЕНИЕ СЧЁТЧИКА КОРЗИНЫ ===
function updateCartCount() {
    fetch('/get_cart_count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const cartCountEl = document.querySelector('.cart-count');
                if (cartCountEl) {
                    cartCountEl.textContent = data.count;
                }
            }
        })
        .catch(err => console.error('Cart count error:', err));
}