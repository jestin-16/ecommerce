// assets/js/app.js

$(document).ready(function() {
    // Initial fetch
    loadProducts();
    // Load initial cart
    refreshCart();

    // Delegate add to cart click since cards are injected dynamically
    $('#product-grid').on('click', '.add-to-cart-btn', function() {
        const productId = $(this).data('id');
        const quantity = 1;
        addToCart(productId, quantity);
        
        // Simple animation feedback
        const originalText = $(this).html();
        $(this).html('Added');
        
        // Add pulse animation to badge
        $('#cart-badge').addClass('pulse');
        setTimeout(() => $('#cart-badge').removeClass('pulse'), 350);
        
        setTimeout(() => {
            $(this).html(originalText);
        }, 1500);
    });
});

/**
 * Fetch products from API and render
 * @param {string} category 
 */
function loadProducts(category = '') {
    $('#loading-spinner').removeClass('d-none');
    $('#product-grid').empty();
    
    let url = 'api/get_products.php';
    if (category && category !== 'All') {
        url += '?category=' + encodeURIComponent(category);
    }

    $.ajax({
        url: url,
        method: 'GET',
        dataType: 'json',
        success: function(response) {
            $('#loading-spinner').addClass('d-none');
            if(response.success) {
                renderProducts(response.data);
            } else {
                console.error(response.message);
                $('#product-grid').html('<div class="col-12"><div class="alert alert-danger">Failed to load products.</div></div>');
            }
        },
        error: function(err) {
            $('#loading-spinner').addClass('d-none');
            console.error('AJAX Error:', err);
        }
    });
}

/**
 * Render products into the grid
 * @param {Array} products 
 */
function renderProducts(products) {
    const grid = $('#product-grid');
    grid.empty();

    if (products.length === 0) {
        grid.html(`
            <div class="col-12 empty-state">
                <div class="empty-state-icon">❄</div>
                <h2 class="empty-state-title">No products found</h2>
                <p class="empty-state-subtext">Try a different search term or category</p>
                <button class="btn-link-primary" onclick="$('.category-btn[data-category=\\'All\\']').click()">Reset Filters</button>
            </div>
        `);
        return;
    }

    products.forEach((product, index) => {
        const inStock = parseInt(product.stock) > 0;
        const btnDisabled = inStock ? '' : 'disabled';
        const btnText = inStock ? 'Add to Cart' : 'Out of Stock';
        
        const categoryClass = 'cat-' + product.category.replace(/\s+/g, '-');

        const cardHtml = `
            <div class="col product-card-wrap">
                <div class="product-card">
                    <div class="product-img-wrapper">
                        <span class="category-badge ${categoryClass}">${product.category}</span>
                        <img src="${product.image_url}" class="product-img" alt="${product.name}" loading="lazy">
                    </div>
                    <div class="product-body">
                        <h5 class="product-title">${product.name}</h5>
                        <div class="product-desc">${product.description}</div>
                        <div class="product-price-row">
                            <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                            <span class="product-rating">⭐ 4.5</span>
                        </div>
                    </div>
                    <div class="product-footer">
                        <button class="btn-cart add-to-cart-btn" data-id="${product.id}" ${btnDisabled}>
                            ${btnText}
                        </button>
                    </div>
                </div>
            </div>
        `;
        grid.append(cardHtml);
    });
}

/**
 * Add product to cart via AJAX POST
 */
function addToCart(productId, quantity) {
    $.ajax({
        url: 'api/cart_add.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                refreshCart();
            } else {
                alert(response.message || 'Failed to add item to cart.');
            }
        },
        error: function(err) {
            console.error('AJAX cart add error:', err);
        }
    });
}
