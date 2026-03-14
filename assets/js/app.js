$(document).ready(function() {
    // Add JS indicator for CSS animations
    $('body').addClass('js-enabled');

    // Initialize Animations
    initAnimations();

    // Load initial cart
    refreshCart();
    
    // Hero Quantity Selector
    $('.hero-qty-plus').on('click', function() {
        let val = parseInt($('.hero-qty-val').text());
        $('.hero-qty-val').text(val + 1);
    });
    
    $('.hero-qty-minus').on('click', function() {
        let val = parseInt($('.hero-qty-val').text());
        if (val > 1) $('.hero-qty-val').text(val - 1);
    });

    // Delegate add to cart click since cards are injected dynamically
    // Also handles the hero's static button
    // Delegate card click to open detail modal
    $(document).on('click', '.luxury-product-card', function(e) {
        // Only trigger if we're not clicking the wishlist btn specifically
        if ($(e.target).closest('.wishlist-btn').length) return;
        
        const productId = $(this).find('.add-to-cart-btn').data('id');
        if (typeof openProductDetail === 'function') {
            openProductDetail(productId);
        }
    });

    // Handle add-to-cart button on card specifically to also open detail
    $(document).on('click', '.add-to-cart-btn', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Prevent card double-trigger
        const productId = $(this).data('id');
        if (typeof openProductDetail === 'function') {
            openProductDetail(productId);
        }
    });
});

/**
 * Global Animation Observer
 */
let revealObserver;

/**
 * Initialize Reveal and Scroll Animations
 */
function initAnimations() {
    // 1. Intersection Observer for Scroll Reveals
    const revealCallback = (entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    };

    revealObserver = new IntersectionObserver(revealCallback, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    });

    // Observe all initial reveal elements
    $('.scroll-reveal, .text-reveal-item').each(function() {
        revealObserver.observe(this);
    });

    // 2. Scroll Progress Bar & Hero Parallax
    $(window).on('scroll', function() {
        // Toggle Navbar scroll state
        if (window.scrollY > 50) {
            $('.boreal-navbar').addClass('navbar-scrolled');
        } else {
            $('.boreal-navbar').removeClass('navbar-scrolled');
        }

        const winScroll = document.body.scrollTop || document.documentElement.scrollTop;
        const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
        const scrolled = (winScroll / height) * 100;
        
        // Update progress bar
        $('#scroll-progress').css('width', scrolled + '%');

        // Hero Parallax effect
        const scrollValue = $(window).scrollTop();
        $('.filter-moody').css('transform', `translateY(${scrollValue * 0.15}px) scale(${1 + scrollValue * 0.0001})`);
        $('.hero-text-col').css('transform', `translateY(${scrollValue * 0.1}px)`);
        
        // Snowflake parallax
        $('.snow-deco').each(function() {
            const speed = $(this).data('speed') || 0.2;
            $(this).css('transform', `translateY(${scrollValue * speed}px) rotate(${scrollValue * 0.05}deg)`);
        });
    });
}

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
                <div class="empty-state-icon text-dark"><i class="bi bi-snow"></i></div>
                <h2 class="empty-state-title text-dark font-playfair">No curated pieces found</h2>
                <p class="empty-state-subtext text-secondary">Explore a different category or search term.</p>
                <button class="btn btn-outline-dark rounded-0 px-4 py-2 mt-3" onclick="$('.active-filter').removeClass('active-filter'); $(this).addClass('active-filter'); loadProducts('All');">Reset Collection</button>
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
        
        const rating = (4.0 + (product.id % 10) / 10).toFixed(1);
        const delayClass = `reveal-delay-${(index % 4) + 1}`;

        const cardHtml = `
            <div class="col product-card-wrap scroll-reveal ${delayClass}">
                <div class="card bg-transparent border-0 luxury-product-card h-100">
                    <div class="position-relative overflow-hidden product-image-wrapper mb-3">
                        ${badgeHtml}
                        <button class="btn btn-link text-dark position-absolute top-0 end-0 m-2 wishlist-btn"><i class="bi bi-heart"></i></button>
                        <img src="${product.image_url}" class="card-img-top rounded-0 object-fit-cover product-img-height mix-blend-mode-multiply" style="filter: sepia(0.2) contrast(1.1);" alt="${product.name}" loading="lazy">
                        <div class="product-overlay d-flex justify-content-center align-items-center position-absolute top-0 start-0 w-100 h-100 bg-overlay opacity-0 transition-all">
                            <button class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fs-8 tracking-wider add-to-cart-btn" data-id="${product.id}">
                                VIEW DETAILS
                            </button>
                        </div>
                    </div>
                    <div class="card-body px-0 pt-2 pb-0">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <p class="text-accent text-uppercase fs-8 tracking-wider mb-0">BOREAL</p>
                            <div class="rating text-dark fs-8">
                                <i class="bi bi-star-fill text-accent-warm"></i> ${rating}
                            </div>
                        </div>
                        <h5 class="card-title text-dark font-playfair fs-5 mb-2">${product.name}</h5>
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <p class="card-text text-dark font-mono fs-5 mb-0">$${parseFloat(product.price).toFixed(2)}</p>
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

    // Observe newly added products for reveal effect
    grid.find('.scroll-reveal').each(function() {
        if (revealObserver) revealObserver.observe(this);
    });
}

/**
 * Add product to cart via AJAX POST
 */
function addToCart(productId, quantity, color = '', size = '', variantId = null) {
    $.ajax({
        url: 'api/cart_add.php',
        method: 'POST',
        data: {
            product_id: productId,
            quantity: quantity,
            color: color,
            size: size,
            variant_id: variantId
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
