<?php
session_start();
require_once 'api/db.php';

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$totalItems = 0;
$subtotal = 0;

foreach ($cart as $productId => $item) {
    if ($item['qty'] <= 0) continue;
    $totalItems += $item['qty'];
    $subtotal += ($item['price'] * $item['qty']);
}

// Redirect back if empty or zero subtotal
if (empty($cart) || $subtotal <= 0) {
    header("Location: index.php");
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - BOREAL</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body class="luxury-light-theme d-flex flex-column min-vh-100">
    
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar bg-white border-bottom">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="index.php">
                BOREAL
            </a>
            <div class="d-flex align-items-center gap-4 text-dark d-none d-lg-flex boreal-nav-icons ms-auto">
                 <a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="index.php">Return to Shop</a>
            </div>
        </div>
    </nav>

    <!-- Checkout Main Section -->
    <section class="flex-grow-1 py-6">
        <div class="container px-lg-5 mt-5">
            <h1 class="text-dark font-playfair mb-5 text-center">Secure Checkout</h1>
            
            <div class="row g-5">
                <div class="col-lg-7">
                    <div class="card bg-white border rounded-0 p-4 p-md-5">
                        <h4 class="text-dark font-playfair mb-4">Shipping Information</h4>
                        <div id="checkout-alert" class="alert d-none rounded-0 border-0 fs-7"></div>
                        <form id="checkout-form">
                            <div class="mb-4">
                                <label for="address" class="form-label text-dark text-uppercase tracking-wider fs-8">Complete Shipping Address</label>
                                <textarea class="form-control bg-transparent border text-dark rounded-0 shadow-none ps-2" id="address" name="address" rows="3" required placeholder="Street, City, Postal Code, Country"></textarea>
                            </div>
                            <!-- Mock Payment info just for visual completeness -->
                            <h4 class="text-dark font-playfair mb-4 mt-5">Payment Method</h4>
                            <div class="mb-3">
                                <label class="form-label text-dark text-uppercase tracking-wider fs-8">Card Number</label>
                                <input type="text" class="form-control bg-transparent border text-dark rounded-0 shadow-none ps-2" placeholder="**** **** **** ****" required>
                            </div>
                            <div class="row mb-4">
                                <div class="col-6">
                                     <label class="form-label text-dark text-uppercase tracking-wider fs-8">Expiry</label>
                                     <input type="text" class="form-control bg-transparent border text-dark rounded-0 shadow-none ps-2" placeholder="MM/YY" required>
                                </div>
                                <div class="col-6">
                                     <label class="form-label text-dark text-uppercase tracking-wider fs-8">CVC</label>
                                     <input type="text" class="form-control bg-transparent border text-dark rounded-0 shadow-none ps-2" placeholder="***" required>
                                </div>
                            </div>

                            <button type="submit" id="place-order-btn" class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-wider mt-4">Place Order (₹<?php echo number_format($subtotal, 2); ?>)</button>
                        </form>
                    </div>
                </div>
                
                <div class="col-lg-5">
                    <div class="card bg-white border rounded-0 p-4 p-md-5 sticky-top" style="top: 100px;">
                        <h4 class="text-dark font-playfair mb-4">Order Summary</h4>
                        
                        <div class="order-items-scroll" style="max-height: 350px; overflow-y: auto;">
                            <?php foreach($cart as $cartKey => $item): if ($item['qty'] <= 0) continue; ?>
                            <div class="d-flex align-items-center gap-3 mb-3 border-bottom pb-3">
                                <div>
                                    <h6 class="text-dark font-playfair mb-0"><?php echo htmlspecialchars($item['name']); ?></h6>
                                    <small class="text-secondary tracking-wider fs-8">QTY: <?php echo $item['qty']; ?> <?php echo isset($item['size']) && $item['size'] ? " | SIZE: ".$item['size'] : ""; ?> <?php echo isset($item['color']) && $item['color'] ? " | COLOR: ".$item['color'] : ""; ?></small>
                                </div>
                                <div class="ms-auto text-dark font-mono">
                                    ₹<?php echo number_format($item['price'] * $item['qty'], 2); ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="mt-4 pt-3 border-top">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary text-uppercase tracking-wider fs-8">Subtotal</span>
                                <span class="text-dark font-mono">₹<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary text-uppercase tracking-wider fs-8">Shipping (Standard)</span>
                                <span class="text-dark font-mono">FREE</span>
                            </div>
                            <div class="d-flex justify-content-between mt-3 pt-3 border-top">
                                <span class="text-white text-uppercase fw-bold tracking-wider fs-6">Total</span>
                                <span class="text-accent font-mono fw-bold fs-5">₹<?php echo number_format($subtotal, 2); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-4 border-top mt-auto bg-light">
        <div class="container text-center">
            <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Secure Checkout.</p>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#checkout-form').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $('#place-order-btn');
                const originalText = btn.text();
                btn.html('<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Processing...').prop('disabled', true);
                
                $.ajax({
                    url: 'api/place_order.php',
                    method: 'POST',
                    data: {
                        address: $('#address').val()
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#checkout-alert').removeClass('d-none alert-danger').addClass('alert-success border text-dark').html('<i class="bi bi-check-circle me-2 text-success"></i>' + response.message + ' Redirecting...');
                            btn.html('<i class="bi bi-check"></i> Success');
                            setTimeout(() => {
                                window.location.href = 'index.php'; // Or an order success page
                            }, 2000);
                        } else {
                            $('#checkout-alert').removeClass('d-none alert-success').addClass('alert-danger').html('<i class="bi bi-exclamation-triangle me-2"></i>' + response.message);
                            btn.html(originalText).prop('disabled', false);
                        }
                    },
                    error: function() {
                        $('#checkout-alert').removeClass('d-none alert-success').addClass('alert-danger').text('An error occurred while processing your order.');
                        btn.html(originalText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
