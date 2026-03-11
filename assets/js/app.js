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
                <div class="empty-state-icon text-white"><i class="bi bi-snow"></i></div>
                <h2 class="empty-state-title text-white font-playfair">No curated pieces found</h2>
                <p class="empty-state-subtext text-secondary-light">Explore a different category or search term.</p>
                <button class="btn btn-outline-light rounded-0 px-4 py-2 mt-3" onclick="$('.active-filter').removeClass('active-filter'); $(this).addClass('active-filter'); loadProducts('All');">Reset Collection</button>
            </div>
        `);
        return;
    }

    products.forEach((product, index) => {
        const inStock = parseInt(product.stock) > 0;
        const btnDisabled = inStock ? '' : 'disabled';
        const btnText = inStock ? 'Add to Cart' : 'Out of Stock';
        
        let badgeHtml = '';
        if (product.category === 'Sale') {
            badgeHtml = `<span class="badge bg-danger text-white position-absolute top-0 start-0 m-3 rounded-0 tracking-wider">SALE</span>`;
        } else if (index === 0) {
             badgeHtml = `<span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 rounded-0 tracking-wider">NEW</span>`;
        }
        
        // Simple mock rating based on ID for visual flair
        const rating = (4.0 + (product.id % 10) / 10).toFixed(1);

        const cardHtml = `
            <div class="col product-card-wrap">
                <div class="card bg-transparent border-0 luxury-product-card h-100">
                    <div class="position-relative overflow-hidden product-image-wrapper mb-3">
                        ${badgeHtml}
                        <button class="btn btn-link text-white position-absolute top-0 end-0 m-2 wishlist-btn"><i class="bi bi-heart"></i></button>
                        <img src="${product.image_url}" class="card-img-top rounded-0 object-fit-cover product-img-height mix-blend-mode-lighten" alt="${product.name}" loading="lazy">
                        <div class="product-overlay d-flex justify-content-center align-items-center position-absolute top-0 start-0 w-100 h-100 bg-overlay opacity-0 transition-all">
                            <button class="btn btn-outline-light rounded-0 px-4 py-2 text-uppercase fs-8 tracking-wider add-to-cart-btn" data-id="${product.id}" ${btnDisabled}>
                                ${btnText}
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-2 pb-0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <p class="text-accent text-uppercase fs-8 tracking-wider mb-0">BOREAL</p>
                            <div class="rating text-white fs-8">
                                <i class="bi bi-star-fill text-accent-warm"></i> ${rating}
                            </div>
                        </div>
                        <h5 class="card-title text-white font-playfair fs-5 mb-2">${product.name}</h5>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="card-text text-white font-mono fs-5 mb-0">$${parseFloat(product.price).toFixed(2)}</p>
                            <div class="color-swatches d-flex gap-1">
                                <span class="swatch bg-dark border border-secondary rounded-circle d-block" style="width:12px; height:12px;"></span>
                                <span class="swatch bg-secondary rounded-circle d-block" style="width:12px; height:12px;"></span>
                            </div>
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
