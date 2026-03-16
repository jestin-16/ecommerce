// assets/js/cart.js

$(document).ready(function () {
    // Event listeners
    $('#cart-items-container').on('click', '.qty-btn', function () {
        const cartKey = $(this).data('key');
        let currentQty = parseInt($(this).siblings('.qty-input').val());
        const action = $(this).data('action');

        if (action === 'increase') {
            currentQty++;
        } else if (action === 'decrease') {
            currentQty--;
        }

        if (currentQty > 0) {
            $(this).siblings('.qty-input').val(currentQty);
            updateCartItem(cartKey, currentQty);
        } else {
            removeCartItem(cartKey);
        }
    });

    $('#cart-items-container').on('change', '.qty-input', function () {
        const cartKey = $(this).data('key');
        let newQty = parseInt($(this).val());

        if (newQty > 0) {
            updateCartItem(cartKey, newQty);
        } else {
            removeCartItem(cartKey);
        }
    });

    $('#cart-items-container').on('click', '.remove-item-btn', function () {
        const cartKey = $(this).data('key');
        removeCartItem(cartKey);
    });

    $('#clear-cart-btn').on('click', function () {
        if (confirm('Are you sure you want to clear your cart?')) {
            clearCart();
        }
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
    totalEl.text('₹' + parseFloat(subtotal).toFixed(2));

    container.empty();

    if (!data.items || data.items.length === 0) {
        container.html(`
            <div class="empty-state">
                <i class="bi bi-snow empty-state-icon text-white"></i>
                <h4 class="empty-state-title font-playfair text-white">Your collection is empty</h4>
                <p class="empty-state-subtext text-secondary-light">Explore our curated pieces and elevate your wardrobe.</p>
            </div>
        `);
        return;
    }

    data.items.forEach(item => {
        const variantText = item.size || item.color ? `<div class="cart-item-variant text-secondary fs-8">${item.size ? 'Size: ' + item.size : ''}${item.size && item.color ? ' | ' : ''}${item.color ? 'Color: ' + item.color : ''}</div>` : '';
        const itemHtml = `
            <div class="cart-item">
                <img src="${item.image_url || 'https://placehold.co/60x60/f8f8f8/1a1616?text=Luxury'}" class="cart-item-img" alt="${item.name}">
                <div class="cart-item-info">
                    <div class="cart-item-title text-dark fw-bold">${item.name}</div>
                    ${variantText}
                    <div class="qty-controls mt-2">
                        <button class="qty-btn font-mono" type="button" data-action="decrease" data-key="${item.cart_key}">-</button>
                        <input type="text" class="qty-input" value="${item.qty}" data-key="${item.cart_key}" readonly>
                        <button class="qty-btn font-mono" type="button" data-action="increase" data-key="${item.cart_key}">+</button>
                    </div>
                </div>
                <div class="cart-item-price-remove">
                    <button class="remove-item-btn" data-key="${item.cart_key}"><i class="bi bi-x"></i></button>
                    <div class="cart-item-total mt-4 text-dark font-mono">₹${parseFloat(item.line_total).toFixed(2)}</div>
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
function updateCartItem(cartKey, quantity) {
    $.ajax({
        url: 'api/cart_update.php',
        method: 'POST',
        data: {
            cart_key: cartKey,
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
function removeCartItem(cartKey) {
    $.ajax({
        url: 'api/cart_remove.php',
        method: 'POST',
        data: {
            cart_key: cartKey
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
