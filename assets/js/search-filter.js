// assets/js/search-filter.js

/**
 * Debounce function to delay execution
 * @param {Function} func - Function to debounce
 * @param {Number} delay - Delay in milliseconds
 * @returns {Function} Debounced function
 */
function debounce(func, delay) {
    let timeoutId;
    return function (...args) {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => {
            func.apply(this, args);
        }, delay);
    };
}

$(document).ready(function () {
    const $searchInput = $('#search-input');
    const $categoryBtns = $('.category-btn');
    const $navbarLinks = $('.navbar-category-link');

    // Debounced Search Handler
    $searchInput.on('input', debounce(function () {
        const term = $(this).val().trim();

        // If typing in search, reset category buttons visual state
        $categoryBtns.removeClass('active-filter text-white').addClass('text-secondary hover-white');
        $('#current-category-title').text(term ? `Search Results for "${term}"` : 'All Products');

        if (term.length > 0) {
            searchProducts(term);
        } else {
            // Re-highlight "All" when input is cleared
            $('[data-category="All"]').removeClass('text-secondary hover-white').addClass('active-filter text-white');
            loadProducts('All');
        }
    }, 400));

    // Category Filter Handler (Filter buttons)
    $categoryBtns.on('click', function () {
        const category = $(this).data('category');

        // Update active state
        $categoryBtns.removeClass('active-filter text-white').addClass('text-secondary hover-white');
        $(this).removeClass('text-secondary hover-white').addClass('active-filter text-white');

        // Clear search input
        $searchInput.val('');

        // Update Title
        $('#current-category-title').text(category === 'All' ? 'All Products' : category);

        // Load products based on category
        loadProducts(category);
    });

    // Navbar category link handler
    $navbarLinks.on('click', function (e) {
        e.preventDefault();
        const category = $(this).data('category');
        
        // Update filter buttons state
        $categoryBtns.removeClass('active');
        $(`[data-category="${category}"]`).addClass('active');
        
        // Clear search
        $searchInput.val('');
        
        // Update title
        $('#current-category-title').text(category === 'All' ? 'All Products' : category);
        
        // Load products
        loadProducts(category);
        
        // Close mobile nav if open
        const navCollapse = document.querySelector('#borealNav');
        if (navCollapse && navCollapse.classList.contains('show')) {
            const bsCollapse = new bootstrap.Collapse(navCollapse);
            bsCollapse.hide();
        }
        
        // Scroll to products section
        setTimeout(() => {
            document.querySelector('.products-section')?.scrollIntoView({ behavior: 'smooth' });
        }, 100);
    });
});

/**
 * Fetch products matching search term and render
 * @param {string} term 
 */
function searchProducts(term) {
    $('#loading-spinner').removeClass('d-none');
    $('#product-grid').empty();

    $.ajax({
        url: 'api/search_products.php',
        method: 'GET',
        data: { q: term },
        dataType: 'json',
        success: function (response) {
            $('#loading-spinner').addClass('d-none');
            if (response.success) {
                renderProducts(response.data);
            } else {
                console.error(response.message);
                $('#product-grid').html('<div class="col-12"><div class="alert alert-danger">Search failed.</div></div>');
            }
        },
        error: function (err) {
            $('#loading-spinner').addClass('d-none');
            console.error('AJAX Search Error:', err);
        }
    });
}
