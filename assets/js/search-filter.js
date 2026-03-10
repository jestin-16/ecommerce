// assets/js/search-filter.js

$(document).ready(function () {
    const $searchInput = $('#search-input');
    const $categoryBtns = $('.category-btn');

    // Debounced Search Handler
    $searchInput.on('input', debounce(function () {
        const term = $(this).val().trim();

        // If typing in search, reset category buttons visual state
        $categoryBtns.removeClass('active');
        $('#current-category-title').text(term ? `Search Results for "${term}"` : 'All Products');

        if (term.length > 0) {
            searchProducts(term);
        } else {
            // Re-highlight "All" when input is cleared
            $('[data-category="All"]').addClass('active');
            loadProducts('All');
        }
    }, 400));

    // Category Filter Handler
    $categoryBtns.on('click', function () {
        const category = $(this).data('category');

        // Update active state
        $categoryBtns.removeClass('active');
        $(this).addClass('active');

        // Clear search input
        $searchInput.val('');

        // Update Title
        $('#current-category-title').text(category === 'All' ? 'All Products' : category);

        // Load products based on category
        loadProducts(category);
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
