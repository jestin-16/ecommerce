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
        $(this).html('<i class="bi bi-check2"></i> Added');
        $(this).removeClass('btn-primary').addClass('btn-success');
        
        setTimeout(() => {
            $(this).html('<i class="bi bi-cart-plus"></i> Add to Cart');
            $(this).removeClass('btn-success').addClass('btn-primary');
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
        grid.html('<div class="col-12 text-center py-5 text-muted"><i class="bi bi-inbox fs-1"></i><p class="mt-2">No products found.</p></div>');
        return;
    }

    products.forEach((product, index) => {
        // Staggered animation delay
        const delay = (index * 0.1) % 1; // max 1s delay
        
        const inStock = parseInt(product.stock) > 0;
        const stockClass = inStock ? 'in-stock' : 'out-of-stock';
        const stockText = inStock ? 'In Stock' : 'Out of Stock';
        const btnDisabled = inStock ? '' : 'disabled';
        const btnClass = inStock ? 'btn-primary' : 'btn-secondary';

        const categoryClass = 'cat-' + product.category.replace(/\s+/g, '-');

        const cardHtml = `
            <div class="col fade-up" style="animation-delay: ${delay}s">
                <div class="card product-card h-100">
                    <div class="product-img-wrapper">
                        <span class="category-badge ${categoryClass}">${product.category}</span>
                        <img src="${product.image_url}" class="card-img-top product-img" alt="${product.name}" loading="lazy">
                    </div>
                    <div class="card-body product-body">
                        <h5 class="card-title product-title">${product.name}</h5>
                        <p class="card-text product-desc">${product.description}</p>
                        <div class="product-footer">
                            <span class="product-price">$${parseFloat(product.price).toFixed(2)}</span>
                            <span class="stock-status ${stockClass}"><i class="bi ${inStock ? 'bi-check-circle-fill' : 'bi-x-circle-fill'}"></i> ${stockText}</span>
                        </div>
                        <div class="mt-auto">
                            <button class="btn ${btnClass} add-to-cart-btn" data-id="${product.id}" ${btnDisabled}>
                                <i class="bi bi-cart-plus"></i> Add to Cart
                            </button>
                        </div>
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
