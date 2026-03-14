// assets/js/product-detail.js

$(document).ready(function() {
    let currentProduct = null;
    let selectedVariant = null;
    let selectedSize = null;

    // Delegate click on product cards (including dynamically generated ones)
    $(document).on('click', '.luxury-product-card, .floating-card-light', function(e) {
        // Prevent clicking certain specific buttons if they have other behaviors
        if ($(e.target).closest('.wishlist-btn').length) return;
        
        let productId = $(this).find('.add-to-cart-btn').data('id');
        if (!productId) {
             productId = $(this).data('id'); // Fallback for specialized cards
        }
        
        if (productId) {
            openProductDetail(productId);
        }
    });

    /**
     * Open product detail modal and fetch data
     */
    function openProductDetail(productId) {
        // Show loading state or modal immediately
        $('#productDetailModal').modal('show');
        resetModal();

        $.ajax({
            url: `api/get_product.php?id=${productId}`,
            method: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    populateModal(response.data);
                    // Update URL
                    history.pushState({ productId: productId }, '', `?product=${productId}`);
                } else {
                    alert(response.message);
                    $('#productDetailModal').modal('hide');
                }
            },
            error: function(err) {
                console.error('Error fetching product:', err);
            }
        });
    }

    /**
     * Reset modal to clean state
     */
    function resetModal() {
        currentProduct = null;
        selectedVariant = null;
        selectedSize = null;
        $('#modalProductName, #modalDescription, #modalProductPrice').text('');
        $('#modalProductImage').attr('src', '');
        $('#colorSwatches, .thumbnail-strip').empty();
        $('.size-pill').removeClass('selected disabled');
        $('#sizeError').addClass('d-none');
        $('#modalQty').val(1);
        $('#selectedColorName').text('');
    }

    /**
     * Populate modal with product and variant data
     */
    function populateModal(data) {
        currentProduct = data.product;
        const variants = data.variants;

        $('#modalProductName').text(currentProduct.name);
        $('#modalDescription').text(currentProduct.description || 'No description available for this luxury piece.');
        $('#modalProductPrice').text('$' + parseFloat(currentProduct.price).toFixed(2));
        $('#modalProductImage').attr('src', currentProduct.image_url);

        // Category Badge
        if (currentProduct.category === 'Sale') {
            $('#modalBadge').text('SALE').removeClass('d-none').addClass('bg-danger');
        } else {
            $('#modalBadge').addClass('d-none');
        }

        // Color Swatches
        if (variants && variants.length > 0) {
            $('#modalColorSelector').removeClass('d-none');
            variants.forEach(variant => {
                const swatch = $(`<button class="color-swatch" title="${variant.color_name}" style="background-color: ${variant.color_hex};"></button>`);
                swatch.on('click', function() {
                    $('.color-swatch').removeClass('active');
                    $(this).addClass('active');
                    switchVariant(variant);
                });
                $('#colorSwatches').append(swatch);
            });
            // Select first variant by default
            $('#colorSwatches .color-swatch').first().trigger('click');
        } else {
            $('#modalColorSelector').addClass('d-none');
            // Populate thumb with just the main image
            addThumbnail(currentProduct.image_url);
        }

        // Update Stock/Size visibility based on total product stock if no variant selected
        updateSizeAvailability(currentProduct.stock);
    }

    function switchVariant(variant) {
        selectedVariant = variant;
        $('#selectedColorName').text(variant.color_name);

        // Smooth image switch
        const mainImg = $('#modalProductImage');
        mainImg.css('opacity', '0.5');
        setTimeout(() => {
            mainImg.attr('src', variant.image_url);
            mainImg.css('opacity', '1');
        }, 200);

        // Update thumbnails
        $('.thumbnail-strip').empty();
        addThumbnail(variant.image_url);
        if (currentProduct.image_url !== variant.image_url) {
             addThumbnail(currentProduct.image_url);
        }

        // Update size availability based on variant stock
        updateSizeAvailability(variant.stock);
    }

    function addThumbnail(url) {
        const thumb = $(`<img src="${url}" class="modal-thumb" alt="Thumbnail">`);
        thumb.on('click', function() {
            $('#modalProductImage').attr('src', url);
            $('.modal-thumb').removeClass('active');
            $(this).addClass('active');
        });
        $('.thumbnail-strip').append(thumb);
        if ($('.modal-thumb').length === 1) thumb.addClass('active');
    }

    function updateSizeAvailability(stock) {
        stock = parseInt(stock);
        // Simple mock logic: if stock is very low, mock some sizes as out of stock
        $('.size-pill').each(function() {
            const size = $(this).data('size');
            $(this).removeClass('disabled');
            
            // Randomly disable some for demo if global stock is low
            if (stock < 5 && (size === 'XXL' || size === 'XS')) {
                $(this).addClass('disabled');
            } else if (stock === 0) {
                $(this).addClass('disabled');
            }
        });
    }

    // Size Selection
    $(document).on('click', '.size-pill:not(.disabled)', function() {
        $('.size-pill').removeClass('selected');
        $(this).addClass('selected');
        selectedSize = $(this).data('size');
        $('#sizeError').addClass('d-none');
    });

    // Quantity Stepper
    $('.qty-plus').on('click', function() {
        let val = parseInt($('#modalQty').val());
        if (val < 10) $('#modalQty').val(val + 1);
    });

    $('.qty-minus').on('click', function() {
        let val = parseInt($('#modalQty').val());
        if (val > 1) $('#modalQty').val(val - 1);
    });

    // Add to Cart from Modal
    $('#modalAddToCartBtn').on('click', function() {
        if (!selectedSize) {
            $('#sizeError').removeClass('d-none');
            return;
        }

        const quantity = parseInt($('#modalQty').val());
        const productId = currentProduct.id;
        const variantId = selectedVariant ? selectedVariant.id : null;
        const colorName = selectedVariant ? selectedVariant.color_name : '';
        
        // Call global addToCart from app.js
        if (typeof addToCart === 'function') {
            addToCart(productId, quantity, colorName, selectedSize, variantId);
            $('#productDetailModal').modal('hide');
        } else {
            console.error('addToCart function not found');
        }
    });

    // Handle initial product from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productUrlId = urlParams.get('product');
    if (productUrlId) {
        openProductDetail(productUrlId);
    }

    // Handle back button / history
    $(window).on('popstate', function(e) {
        if (e.originalEvent.state && e.originalEvent.state.productId) {
            openProductDetail(e.originalEvent.state.productId);
        } else {
            $('#productDetailModal').modal('hide');
        }
    });

    // Reset URL when modal closes
    $('#productDetailModal').on('hidden.bs.modal', function () {
        const url = new URL(window.location);
        url.searchParams.delete('product');
        window.history.replaceState({}, '', url);
    });
});
