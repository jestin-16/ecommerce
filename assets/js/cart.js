// assets/js/cart.js

$(document).ready(function () {
    // Event listeners
    $('#cart-items-container').on('click', '.qty-btn', function () {
        const productId = $(this).data('id');
        let currentQty = parseInt($(this).siblings('.qty-input').val());
        const action = $(this).data('action');

        if (action === 'increase') {
            currentQty++;
        } else if (action === 'decrease') {
            currentQty--;
        }

        if (currentQty > 0) {
            $(this).siblings('.qty-input').val(currentQty);
            updateCartItem(productId, currentQty);
        } else {
            removeCartItem(productId);
        }
    });

    $('#cart-items-container').on('change', '.qty-input', function () {
        const productId = $(this).data('id');
        let newQty = parseInt($(this).val());

        if (newQty > 0) {
            updateCartItem(productId, newQty);
        } else {
            removeCartItem(productId);
        }
    });

    $('#cart-items-container').on('click', '.remove-item-btn', function () {
        const productId = $(this).data('id');
        removeCartItem(productId);
    });

    $('#clear-cart-btn').on('click', function () {
        if (confirm('Are you sure you want to clear your cart?')) {
            clearCart();
        }
    });

    $('#checkout-btn').on('click', function () {
        alert('Checkout functionality is not implemented in this demo.');
    });
});

/**
 * Fetch cart data and update UI
 */
function refreshCart() {
    $.ajax({
        url: 'api/cart_get.php',
        method: 'GET',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                renderCart(response.data);
            } else {
                console.error(response.message);
            }
        },
        error: function (err) {
            console.error('AJAX cart get error:', err);
        }
    });
}

/**
 * Render cart items and totals
 * @param {Object} data 
 */
function renderCart(data) {
    const container = $('#cart-items-container');
    const badge = $('#cart-badge');
    const titleCount = $('#cart-count-title');
    const totalEl = $('#cart-total');

    // Update counts and total
    const totalItems = data.total_items || 0;
    const subtotal = data.subtotal || 0;

    badge.text(totalItems);
    titleCount.text(totalItems);
    totalEl.text('$' + parseFloat(subtotal).toFixed(2));

    container.empty();

    if (!data.items || data.items.length === 0) {
        container.html(`
            <div class="text-center text-muted py-5 empty-cart-message">
                <i class="bi bi-cart-x fs-1 mb-3"></i>
                <p>Your cart is empty.</p>
            </div>
        `);
        return;
    }

    data.items.forEach(item => {
        const itemHtml = `
            <div class="cart-item d-flex gap-3 mb-3 border-bottom pb-3">
                <div class="cart-item-info flex-grow-1">
                    <h6 class="mb-1">${item.name}</h6>
                    <div class="text-muted small mb-2">$${parseFloat(item.price).toFixed(2)}</div>
                    <div class="d-flex align-items-center gap-2">
                        <div class="input-group input-group-sm w-auto">
                            <button class="btn btn-outline-secondary qty-btn" type="button" data-action="decrease" data-id="${item.product_id}">-</button>
                            <input type="text" class="form-control text-center qty-input" value="${item.qty}" data-id="${item.product_id}" style="max-width: 50px;" readonly>
                            <button class="btn btn-outline-secondary qty-btn" type="button" data-action="increase" data-id="${item.product_id}">+</button>
                        </div>
                        <button class="btn btn-sm btn-outline-danger remove-item-btn" data-id="${item.product_id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                </div>
                <div class="cart-item-total fw-bold text-end">
                    $${parseFloat(item.line_total).toFixed(2)}
                </div>
            </div>
        `;
        container.append(itemHtml);
    });
}

/**
 * Update cart item quantity
 * @param {number} productId 
 * @param {number} quantity 
 */
function updateCartItem(productId, quantity) {
    $.ajax({
        url: 'api/cart_update.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                refreshCart();
            } else {
                alert(response.message || 'Failed to update item.');
                refreshCart();
            }
        },
        error: function (err) {
            console.error('AJAX cart update error:', err);
        }
    });
}

/**
 * Remove item from cart
 * @param {number} productId 
 */
function removeCartItem(productId) {
    $.ajax({
        url: 'api/cart_remove.php',
        method: 'POST',
        data: {
            product_id: productId
        },
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                refreshCart();
            } else {
                alert(response.message || 'Failed to remove item.');
            }
        },
        error: function (err) {
            console.error('AJAX cart remove error:', err);
        }
    });
}

/**
 * Clear all items from cart
 */
function clearCart() {
    $.ajax({
        url: 'api/cart_clear.php',
        method: 'POST',
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                refreshCart();
            } else {
                alert(response.message || 'Failed to clear cart.');
            }
        },
        error: function (err) {
            console.error('AJAX cart clear error:', err);
        }
    });
}
