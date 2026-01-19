document.addEventListener('DOMContentLoaded', function () {
    console.log('Cart JS loaded');

    // ✅ Обновляем счётчик при загрузке на всех страницах
    updateCartCount();

    // === CART PAGE ONLY ===
    // === QUANTITY CONTROLS ===
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

    // === REMOVE ITEM ===
    document.querySelectorAll('.remove-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const row = this.closest('tr');
            const productId = row.dataset.id;
            updateCartItem(productId, 0);
        });
    });

    // === CHECKOUT BUTTON ===
    const checkoutBtn = document.getElementById('checkout-btn');
    if (checkoutBtn) {
        checkoutBtn.addEventListener('click', function () {
            window.location.href = 'checkout.php';
        });
    }
});

// === ADD TO CART ===
function addToCart(productId, quantity) {
    console.log('addToCart called with:', { product_id: productId, quantity: quantity });

    // ✅ Проверим, что productId — число
    if (!productId || isNaN(productId) || productId <= 0) {
        console.error('Invalid product_id:', productId);
        alert('Invalid product ID');
        return;
    }

    // ✅ Проверим, что quantity — число
    if (!quantity || isNaN(quantity) || quantity <= 0) {
        console.error('Invalid quantity:', quantity);
        alert('Invalid quantity');
        return;
    }

    fetch('/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: parseInt(productId), quantity: parseInt(quantity) })
    })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                //alert('Product added to cart!');
                updateCartCount(); // ✅ Обновляем счётчик
            } else {
                alert('Error adding to cart: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Network error');
        });
}

// === UPDATE CART VIA AJAX ===
function updateCartItem(productId, quantity) {
    fetch('/update_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ product_id: productId, quantity: quantity })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload(); // ✅ Перезагружаем для обновления
            } else {
                alert('Error updating cart: ' + (data.error || 'Unknown error'));
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            alert('Network error');
        });
}

// === UPDATE CART COUNT ===
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
        .catch(err => {
            console.error('Fetch error:', err);
        });
}