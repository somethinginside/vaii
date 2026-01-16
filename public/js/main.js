document.addEventListener('DOMContentLoaded', function () {
    console.log('Main scripts loaded');

    // === ÏÐÎÑÌÎÒÐ ÄÅÒÀËÅÉ ÇÀÊÀÇÀ (äëÿ user_orders.php) ===
    if (document.querySelector('.view-order-btn')) {
        document.querySelectorAll('.view-order-btn').forEach(btn => {
            btn.addEventListener('click', async function () {
                const orderId = this.dataset.orderId;
                const modal = document.getElementById('order-modal');
                const content = document.getElementById('order-details-content');
                const idPlaceholder = document.getElementById('order-id-placeholder');

                idPlaceholder.textContent = '#' + orderId;
                content.textContent = 'Loading...';
                modal.style.display = 'flex';

                try {
                    const res = await fetch('get_order_items.php?id=' + orderId);
                    const data = await res.json();

                    if (data.error) {
                        content.textContent = 'Error: ' + data.error;
                    } else if (data.items && data.items.length > 0) {
                        let html = '<table style="width:100%; border-collapse:collapse; margin-top:15px;"><thead><tr><th>Product</th><th>Qty</th><th>Price</th><th>Total</th></tr></thead><tbody>';
                        data.items.forEach(item => {
                            html += `<tr>
                                <td>${item.name}</td>
                                <td>${item.quantity}</td>
                                <td>${item.price_per_unit} ðóá.</td>
                                <td>${item.subtotal} ðóá.</td>
                            </tr>`;
                        });
                        html += '</tbody></table>';
                        content.innerHTML = html;
                    } else {
                        content.textContent = 'No items in order.';
                    }
                } catch (err) {
                    content.textContent = 'Error loading details.';
                }
            });
        });

        document.getElementById('close-modal').addEventListener('click', function () {
            document.getElementById('order-modal').style.display = 'none';
        });
    }
});