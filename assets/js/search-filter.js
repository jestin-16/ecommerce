// assets/js/search-filter.js

$(document).ready(function () {
    const $searchInput = $('#search-input');
    const $categoryBtns = $('.category-btn');

    // Debounced Search Handler
    $searchInput.on('input', debounce(function () {
        const term = $(this).val().trim();

        // If typing in search, reset category buttons visual state
        $categoryBtns.removeClass('active-filter bg-dark text-white').addClass('text-dark opacity-50');
        $('#current-category-title').text(term ? `Search Results for "${term}"` : 'All Products');

        if (term.length > 0) {
            searchProducts(term);
        } else {
            // Re-highlight "All" when input is cleared
            $('[data-category="All"]').removeClass('opacity-50').addClass('active-filter bg-dark text-white');
            loadProducts('All');
        }
    }, 400));

    // Category Filter Handler (Grid Buttons & Navbar Links)
    $(document).on('click', '.category-btn, .filter-link', function (e) {
        e.preventDefault();
        const category = $(this).data('category');

        // Update active state for grid buttons
        $('.category-btn').removeClass('active-filter active-filter-luxury').css('opacity', '0.6');
        $(`.category-btn[data-category="${category}"]`).removeClass('text-secondary').addClass('active-filter active-filter-luxury').css('opacity', '1');

        // Update active state for navbar links
        $('.filter-link').removeClass('active');
        $(`.filter-link[data-category="${category}"]`).addClass('active');

        // Clear search input
        $searchInput.val('');

        // Update Title if it exists
        const $title = $('#current-category-title');
        if ($title.length) {
            $title.text(category === 'All' ? 'All Products' : category);
        }

        // Load products based on category
        if (typeof loadProducts === 'function') {
            loadProducts(category);
        }
        
        // Scroll to products if clicked from navbar
        if ($(this).hasClass('filter-link')) {
            $('html, body').animate({
                scrollTop: $('.products-section').offset().top - 100
            }, 800);
        }
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
