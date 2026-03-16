<?php
session_start();
require_once 'api/db.php';
// Fetch all products for the grid
$stmt = $pdo->query("SELECT * FROM products ORDER BY id DESC");
$products = $stmt->fetchAll();

// Pick a featured product for the hero (e.g., the first one)
$featuredProduct = !empty($products) ? $products[0] : null;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BOREAL - Luxury Winter Fashion</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap"
        rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>

<body class="luxury-light-theme">
    <div class="scroll-progress" id="scroll-progress"></div>

    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand" href="index.php">
                <i class="bi bi-asterisk me-2 brand-snow"></i>BOREAL
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#borealNav"
                aria-controls="borealNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-center" id="borealNav">
                <ul class="navbar-nav gap-4">

                    <li class="nav-item"><a class="nav-link text-uppercase fs-7 tracking-wider filter-link" href="#"
                            data-category="Coats & Jackets">Coats & Jackets</a></li>
                    <li class="nav-item"><a class="nav-link text-uppercase fs-7 tracking-wider filter-link" href="#"
                            data-category="Knitwear">Knitwear</a></li>
                    <li class="nav-item"><a class="nav-link text-uppercase fs-7 tracking-wider filter-link" href="#"
                            data-category="Footwear">Footwear</a></li>
                    <li class="nav-item"><a class="nav-link text-uppercase fs-7 tracking-wider filter-link" href="#"
                            data-category="Accessories">Accessories</a></li>
                    <li class="nav-item"><a class="nav-link text-uppercase fs-7 tracking-wider filter-link" href="#"
                            data-category="Sale">Sale</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-4 text-dark d-none d-lg-flex boreal-nav-icons">
                <a href="#" class="text-dark"><i class="bi bi-search fs-5"></i></a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-dark" title="Logout"><i class="bi bi-box-arrow-right fs-5"></i></a>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="admin_dashboard.php" class="text-dark" title="Admin Dashboard"><i
                                class="bi bi-shield-lock fs-5"></i></a>
                    <?php else: ?>
                        <a href="profile.php" class="text-dark" title="Profile"><i class="bi bi-person-check fs-5"></i></a>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="login.php" class="text-dark" title="Login"><i class="bi bi-person fs-5"></i></a>
                <?php endif; ?>
                <a href="#" class="text-dark"><i class="bi bi-heart fs-5"></i></a>
                <a href="#cartOffcanvas" data-bs-toggle="offcanvas" role="button" aria-controls="cartOffcanvas"
                    class="text-dark position-relative cursor-pointer" id="cart-btn">
                    <i class="bi bi-bag fs-5"></i>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-accent"
                        id="cart-badge">0</span>
                </a>
                <a href="#" class="text-dark"><i class="bi bi-list fs-4"></i></a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-boreal py-5">
        <div class="container-fluid px-lg-5 h-100">
            <div class="row h-100 align-items-center mx-0">
                <div class="col-lg-5 pe-lg-5 mb-5 mb-lg-0 z-2 hero-text-col">
                    <p class="hero-collection-kicker text-accent mb-4 fs-6 reveal-delay-1 scroll-reveal">Winter
                        Collection - 2026</p>
                    <h1 class="display-1 fw-bold text-dark lh-1 mb-4 hero-title">
                        <span class="text-reveal-wrap wear-line"><span class="text-reveal">WEAR</span></span><br>
                        <span class="text-reveal-wrap the-line"><span class="text-reveal"
                                style="animation-delay: 0.2s">THE</span></span><br>
                        <span class="text-reveal-wrap frost-line"><span class="text-reveal"
                                style="animation-delay: 0.4s">FROST</span></span>
                    </h1>
                    <p class="text-secondary fs-5 mb-5 mw-400 text-reveal-item hero-intro-copy">
                        Curated for those who embrace the cold season with intention. Discover premium outerwear,
                        refined knitwear, and crafted essentials.
                    </p>
                    <div class="d-flex flex-wrap gap-3 hero-cta-row">
                        <a href="#"
                            class="btn btn-dark btn-lg rounded-0 px-5 py-3 text-uppercase tracking-wider fs-7 hero-primary-cta">Shop
                            Collection <i class="bi bi-arrow-right ms-2"></i></a>
                        <a href="#"
                            class="btn btn-outline-dark btn-lg rounded-0 px-5 py-3 text-uppercase tracking-wider fs-7">View
                            Lookbook</a>
                    </div>
                    <div class="hero-rating-row mt-4 d-flex align-items-center gap-2 text-secondary">
                        <span class="text-accent"><i class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i
                                class="bi bi-star-fill"></i> <i class="bi bi-star-fill"></i> <i
                                class="bi bi-star-fill"></i></span>
                        <span class="fs-7">4.9 - 2,847 reviews</span>
                    </div>
                </div>

                <div class="col-lg-7 position-relative h-100 hero-img-col">
                    <div class="hero-img-container h-100 w-100 overflow-hidden">
                        <span class="hero-season-tag">AW / 26</span>
                        <img src="https://i.pinimg.com/control1/736x/f2/33/6c/f2336c38ebc85877a5c40813abbffa68.jpg"
                            alt="Winter Fashion Model"
                            class="img-fluid w-100 h-100 object-fit-cover object-position-top filter-moody">

                        <!-- Atmospheric Parallax Elements -->
                        <div class="snow-deco" style="top: 15%; left: 10%;" data-speed="0.15"><i class="bi bi-snow"></i>
                        </div>
                        <div class="snow-deco" style="top: 35%; right: 15%;" data-speed="0.4"><i class="bi bi-snow"></i>
                        </div>
                        <div class="snow-deco" style="top: 65%; left: 25%;" data-speed="0.25"><i class="bi bi-snow"></i>
                        </div>
                        <div class="snow-deco d-none d-lg-block" style="top: 80%; right: 40%;" data-speed="0.1"><i
                                class="bi bi-snow"></i></div>
                    </div>

                    <!-- Floating Product Card -->
                    <?php if ($featuredProduct): ?>
                        <div
                            class="floating-product-card p-3 bg-glass position-absolute bottom-0 end-0 me-lg-3 mb-4 me-3 d-none d-md-block shadow-luxury scroll-reveal reveal-delay-4">
                            <div class="floating-card-top-image mb-3">
                                <img src="<?php echo $featuredProduct['image_url']; ?>"
                                    alt="<?php echo $featuredProduct['name']; ?>" class="w-100 h-100 object-fit-cover">
                                <span class="floating-product-chip">FEATURED</span>
                            </div>
                            <div class="floating-product-info text-dark">
                                <p class="text-uppercase tracking-wider fs-8 mb-1 text-accent">
                                    <?php echo $featuredProduct['category']; ?>
                                </p>
                                <h4 class="font-playfair fs-5 mb-2"><?php echo $featuredProduct['name']; ?></h4>
                                <div class="d-flex align-items-center justify-content-between mb-3">
                                    <div
                                        class="qty-selector bg-light border border-secondary rounded-0 d-flex align-items-center px-2 py-1">
                                        <button
                                            class="btn btn-link text-dark text-decoration-none p-0 px-2 fw-bold hero-qty-minus">-</button>
                                        <span class="px-2 font-mono hero-qty-val">1</span>
                                        <button
                                            class="btn btn-link text-dark text-decoration-none p-0 px-2 fw-bold hero-qty-plus">+</button>
                                    </div>
                                    <p class="font-mono fs-4 text-dark mb-0">
                                        ₹<?php echo number_format($featuredProduct['price'], 2); ?></p>
                                </div>
                                <button
                                    class="btn btn-dark rounded-0 w-100 py-2 text-uppercase fs-7 fw-bold add-to-cart-btn"
                                    data-id="<?php echo $featuredProduct['id']; ?>">Add to Cart</button>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Product Grid Section -->
    <section class="products-section py-6 bg-white scroll-reveal">
        <div class="container px-lg-5">
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-md-end mb-5 scroll-reveal reveal-delay-1">
                <div>
                    <h2 class="display-5 fw-bold text-dark font-playfair mb-3">Curated Selection</h2>
                    <p class="text-secondary">Exclusive pieces designed to elevate your winter wardrobe.</p>
                </div>

                <div class="mt-4 mt-md-0">
                    <div class="d-flex flex-wrap gap-2 gap-md-4 luxury-filters">
                        <a href="#"
                            class="category-btn text-dark text-decoration-none text-uppercase fs-7 tracking-wider active-filter"
                            data-category="All">All</a>
                        <a href="#"
                            class="category-btn text-secondary text-decoration-none text-uppercase fs-7 tracking-wider hover-dark"
                            data-category="Coats & Jackets">Coats & Jackets</a>
                        <a href="#"
                            class="category-btn text-secondary text-decoration-none text-uppercase fs-7 tracking-wider hover-dark"
                            data-category="Knitwear">Knitwear</a>
                        <a href="#"
                            class="category-btn text-secondary text-decoration-none text-uppercase fs-7 tracking-wider hover-dark"
                            data-category="Footwear">Footwear</a>
                        <a href="#"
                            class="category-btn text-secondary text-decoration-none text-uppercase fs-7 tracking-wider hover-dark"
                            data-category="Accessories">Accessories</a>
                        <a href="#"
                            class="category-btn text-secondary text-decoration-none text-uppercase fs-7 tracking-wider hover-dark"
                            data-category="Sale">Sale</a>
                    </div>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mt-4" id="product-grid">
                <?php if (empty($products)): ?>
                    <div class="col-12 empty-state">
                        <div class="empty-state-icon text-white"><i class="bi bi-snow"></i></div>
                        <h2 class="empty-state-title text-white font-playfair">No curated pieces found</h2>
                        <p class="empty-state-subtext text-secondary-light">Explore a different category or search term.</p>
                        <button class="btn btn-outline-light rounded-0 px-4 py-2 mt-3" onclick="location.reload()">Reset
                            Collection</button>
                    </div>
                <?php else: ?>
                    <?php foreach ($products as $index => $product):
                        $inStock = (int) $product['stock'] > 0;
                        $btnDisabled = $inStock ? '' : 'disabled';
                        $btnText = $inStock ? 'Add to Cart' : 'Out of Stock';

                        $badgeHtml = '';
                        if ($product['category'] === 'Sale') {
                            $badgeHtml = '<span class="badge bg-danger text-white position-absolute top-0 start-0 m-3 rounded-0 tracking-wider">SALE</span>';
                        } else if ($index === 0) {
                            $badgeHtml = '<span class="badge bg-white text-dark position-absolute top-0 start-0 m-3 rounded-0 tracking-wider">NEW</span>';
                        }

                        $rating = number_format(4.0 + ($product['id'] % 10) / 10, 1);
                        ?>
                        <div class="col product-card-wrap scroll-reveal reveal-delay-<?php echo ($index % 4) + 1; ?>">
                            <div class="card bg-transparent border-0 luxury-product-card h-100">
                                <div class="position-relative overflow-hidden product-image-wrapper mb-3">
                                    <?php echo $badgeHtml; ?>
                                    <button class="btn btn-link text-dark position-absolute top-0 end-0 m-2 wishlist-btn"><i
                                            class="bi bi-heart"></i></button>
                                    <img src="<?php echo $product['image_url']; ?>"
                                        class="card-img-top rounded-0 object-fit-cover product-img-height mix-blend-mode-multiply"
                                        alt="<?php echo $product['name']; ?>" loading="lazy">
                                    <div
                                        class="product-overlay d-flex justify-content-center align-items-center position-absolute top-0 start-0 w-100 h-100 bg-overlay opacity-0 transition-all">
                                        <button
                                            class="btn btn-dark rounded-0 px-4 py-2 text-uppercase fs-8 tracking-wider add-to-cart-btn"
                                            data-id="<?php echo $product['id']; ?>">
                                            <?php echo "VIEW DETAILS"; ?>
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body px-0 pt-2 pb-0">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <p class="text-accent text-uppercase fs-8 tracking-wider mb-0">BOREAL</p>
                                        <div class="rating text-dark fs-8">
                                            <i class="bi bi-star-fill text-accent-warm"></i> <?php echo $rating; ?>
                                        </div>
                                    </div>
                                    <h5 class="card-title text-dark font-playfair fs-5 mb-2"><?php echo $product['name']; ?>
                                    </h5>
                                    <div class="d-flex justify-content-between align-items-center mt-3">
                                        <p class="card-text text-dark font-mono fs-5 mb-0">
                                            ₹<?php echo number_format($product['price'], 2); ?></p>
                                        <div class="color-swatches d-flex gap-1">
                                            <span class="swatch bg-dark border border-secondary rounded-circle d-block"
                                                style="width:12px; height:12px;"></span>
                                            <span class="swatch bg-secondary rounded-circle d-block"
                                                style="width:12px; height:12px;"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="text-center mt-5 pt-3">
                <button
                    class="btn btn-outline-light rounded-0 px-5 py-3 text-uppercase fs-7 tracking-wider hover-dark">View
                    All Products</button>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section py-5 border-top border-bottom border-light bg-white scroll-reveal">
        <div class="container px-lg-5">
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 text-center text-lg-start">
                <div class="col p-4 feature-box scroll-reveal reveal-delay-1">
                    <i class="bi bi-box-seam text-dark fs-2 mb-3 d-inline-block"></i>
                    <h5 class="text-dark text-uppercase tracking-wider fs-7 mb-2">Free Shipping</h5>
                    <p class="text-secondary fs-7 mb-0">Complimentary worldwide express shipping on all orders.
                    </p>
                </div>
                <div class="col p-4 feature-box scroll-reveal reveal-delay-2">
                    <i class="bi bi-shield-check text-dark fs-2 mb-3 d-inline-block"></i>
                    <h5 class="text-dark text-uppercase tracking-wider fs-7 mb-2">Authenticity Guarantee</h5>
                    <p class="text-secondary fs-7 mb-0">Every piece is certified authentic and comes with origin
                        proof.</p>
                </div>
                <div class="col p-4 feature-box scroll-reveal reveal-delay-3">
                    <i class="bi bi-lock text-dark fs-2 mb-3 d-inline-block"></i>
                    <h5 class="text-dark text-uppercase tracking-wider fs-7 mb-2">Secure Payments</h5>
                    <p class="text-secondary fs-7 mb-0">Encrypted transactions for your peace of mind.</p>
                </div>
                <div class="col p-4 feature-box scroll-reveal reveal-delay-4">
                    <i class="bi bi-arrow-return-left text-dark fs-2 mb-3 d-inline-block"></i>
                    <h5 class="text-dark text-uppercase tracking-wider fs-7 mb-2">30-Day Returns</h5>
                    <p class="text-secondary fs-7 mb-0">Effortless returns and exchanges within 30 days.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-boreal py-6 bg-light pt-5 pb-4">
        <div class="container px-lg-5">
            <div class="row g-5 mb-5 pb-4 border-bottom">
                <div class="col-lg-4 pe-lg-5">
                    <a class="text-decoration-none text-uppercase fw-bold fs-3 tracking-wide text-dark mb-4 d-block"
                        href="index.php">
                        BOREAL
                    </a>
                    <p class="text-secondary text-start pe-lg-4 fs-7 mb-4">
                        Redefining winter apparel through avant-garde design, unparalleled warmth, and sustainable
                        luxury practices.
                    </p>
                    <form class="mb-4">
                        <label class="text-dark text-uppercase tracking-wider fs-8 mb-2 d-block">Subscribe to
                            Newsletter</label>
                        <div class="input-group">
                            <input type="email"
                                class="form-control bg-transparent border-secondary text-dark rounded-0 shadow-none ps-0"
                                placeholder="Email Address">
                            <button class="btn btn-outline-dark rounded-0 text-uppercase tracking-wider fs-8 px-3"
                                type="button">Join</button>
                        </div>
                    </form>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="text-dark text-uppercase tracking-wider fs-7 mb-4">Shop</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest filter-link"
                                data-category="All">New
                                Arrivals</a></li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest filter-link"
                                data-category="Coats & Jackets">Coats</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest filter-link"
                                data-category="Knitwear">Knitwear</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest filter-link"
                                data-category="Sale">Sale</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="text-dark text-uppercase tracking-wider fs-7 mb-4">Company</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">About
                                Us</a></li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Sustainability</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Careers</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Press</a>
                        </li>
                    </ul>
                </div>

                <div class="col-lg-2 col-md-4">
                    <h6 class="text-dark text-uppercase tracking-wider fs-7 mb-4">Support</h6>
                    <ul class="list-unstyled d-flex flex-column gap-3">
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Contact</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Shipping</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">Returns</a>
                        </li>
                        <li><a href="#"
                                class="text-secondary text-decoration-none hover-dark fs-7 text-uppercase tracking-widest">FAQ</a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                <p class="text-secondary fs-8 mb-3 mb-md-0 text-uppercase tracking-widest">© 2026 BOREAL. All
                    Rights Reserved.</p>
                <div class="d-flex gap-4">
                    <a href="https://instagram.com/boreal" target="_blank" class="text-dark hover-accent"><i class="bi bi-instagram fs-5"></i></a>
                    <a href="https://twitter.com/boreal" target="_blank" class="text-dark hover-accent"><i class="bi bi-twitter-x fs-5"></i></a>
                    <a href="https://tiktok.com/@boreal" target="_blank" class="text-dark hover-accent"><i class="bi bi-tiktok fs-5"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Cart Offcanvas -->
    <div class="offcanvas offcanvas-end bg-white text-dark shadow-luxury boreal-cart-offcanvas border-start"
        tabindex="-1" id="cartOffcanvas" aria-labelledby="cartOffcanvasLabel">
        <div class="offcanvas-header border-bottom p-4">
            <h5 class="offcanvas-title font-playfair fs-4 d-flex align-items-center gap-2" id="cartOffcanvasLabel">
                Your Collection <span class="badge bg-accent rounded-pill fs-7 ms-2 text-white"
                    id="cart-count-title">0</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>

        <div class="offcanvas-body p-0 d-flex flex-column h-100 position-relative">
            <!-- Cart Items Container (Injected via JS) -->
            <div class="cart-items flex-grow-1 overflow-auto p-4" id="cart-items-container">
            </div>

            <!-- Cart Footer -->
            <div class="cart-footer mt-auto p-4 bg-light border-top">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <span class="fs-6 text-uppercase tracking-wider text-secondary">Subtotal</span>
                    <span class="fs-4 font-mono text-dark fw-bold" id="cart-total">₹0.00</span>
                </div>
                <div class="d-grid gap-3">
                    <a href="checkout.php"
                        class="btn btn-dark rounded-0 py-3 text-uppercase fs-7 fw-bold tracking-wider w-100 text-center text-decoration-none"
                        id="checkout-btn">Proceed to Checkout</a>
                    <button class="btn btn-link text-secondary text-uppercase fs-8 text-decoration-none hover-dark"
                        id="clear-cart-btn">Empty Collection</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div class="modal fade product-detail-modal" id="productDetailModal" tabindex="-1"
        aria-labelledby="productDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered">
            <div class="modal-content border-0 rounded-0 overflow-hidden">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-4 z-3 shadow-none"
                    data-bs-dismiss="modal" aria-label="Close"></button>
                <div class="modal-body p-0">
                    <div class="row g-0">
                        <!-- Left Panel: Images -->
                        <div class="col-lg-7 bg-light overflow-hidden position-relative product-modal-images">
                            <div
                                class="main-image-container position-relative h-100 d-flex align-items-center justify-content-center py-5">
                                <img id="modalProductImage" src="" alt="Product Image" class="img-fluid main-modal-img">
                                <!-- Featured Badge handled via JS -->
                                <span id="modalBadge"
                                    class="badge position-absolute top-0 start-0 m-4 rounded-0 tracking-wider d-none"></span>
                            </div>
                            <div
                                class="thumbnail-strip d-flex gap-2 p-3 position-absolute bottom-0 w-100 justify-content-center bg-overlay-subtle">
                                <!-- Thumbnails injected via JS -->
                            </div>
                        </div>

                        <!-- Right Panel: Info -->
                        <div class="col-lg-5 p-4 p-md-5 d-flex flex-column h-100"
                            style="max-height: 90vh; overflow-y: auto;">
                            <div class="brand-label text-accent text-uppercase fs-8 tracking-widest mb-2">BOREAL</div>
                            <h2 id="modalProductName" class="font-playfair display-6 mb-3"></h2>

                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="rating text-dark fs-7">
                                    <i class="bi bi-star-fill text-accent-warm"></i>
                                    <span id="modalRating">4.8</span>
                                </div>
                                <span class="text-secondary fs-8">|</span>
                                <span class="text-secondary fs-8 tracking-wider text-uppercase">124 Reviews</span>
                            </div>

                            <div class="price-row mb-4">
                                <h3 id="modalProductPrice" class="font-mono fs-3 mb-0"></h3>
                                <p id="modalSalePrice"
                                    class="text-secondary text-decoration-line-through fs-6 mb-0 d-none"></p>
                            </div>

                            <div class="description-wrap mb-4">
                                <p id="modalDescription" class="text-secondary fs-7 lh-lg"></p>
                            </div>

                            <!-- Color Selector -->
                            <div id="modalColorSelector" class="mb-4">
                                <label class="text-dark text-uppercase tracking-wider fs-8 mb-3 d-block fw-bold">Select
                                    Color: <span id="selectedColorName" class="text-secondary fw-normal"></span></label>
                                <div id="colorSwatches" class="d-flex gap-2 flex-wrap">
                                    <!-- Colors injected via JS -->
                                </div>
                            </div>

                            <!-- Size Selector -->
                            <div class="mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label
                                        class="text-dark text-uppercase tracking-wider fs-8 d-block fw-bold mb-0">Select
                                        Size</label>
                                    <button
                                        class="btn btn-link p-0 text-accent text-decoration-none fs-8 text-uppercase tracking-wider"
                                        id="sizeGuideBtn">Size Guide</button>
                                </div>
                                <div id="sizeSelector" class="d-flex gap-2 flex-wrap">
                                    <button class="size-pill" data-size="XS">XS</button>
                                    <button class="size-pill" data-size="S">S</button>
                                    <button class="size-pill" data-size="M">M</button>
                                    <button class="size-pill" data-size="L">L</button>
                                    <button class="size-pill" data-size="XL">XL</button>
                                    <button class="size-pill" data-size="XXL">XXL</button>
                                </div>
                                <div id="sizeError" class="text-danger fs-8 mt-2 d-none">Please select a size to
                                    continue.</div>
                            </div>

                            <!-- Qty and Add to Cart -->
                            <div class="mt-auto">
                                <div class="d-flex gap-3 align-items-center mb-4">
                                    <div class="qty-stepper d-flex align-items-center border">
                                        <button class="qty-minus border-0 bg-transparent px-3 py-2">-</button>
                                        <input type="number" id="modalQty" value="1" min="1" max="10"
                                            class="border-0 bg-transparent text-center fw-bold" style="width: 50px;"
                                            readonly>
                                        <button class="qty-plus border-0 bg-transparent px-3 py-2">+</button>
                                    </div>
                                    <button id="modalAddToCartBtn"
                                        class="btn btn-dark rounded-0 flex-grow-1 py-3 text-uppercase tracking-widest fw-bold fs-7">Add
                                        to Cart</button>
                                </div>

                                <button
                                    class="btn btn-outline-dark rounded-0 w-100 py-3 text-uppercase tracking-widest fw-bold fs-7 mb-4 modal-wishlist-btn"><i
                                        class="bi bi-heart me-2"></i> Add to Wishlist</button>

                                <!-- Trust Badges -->
                                <div class="row g-0 pt-4 border-top">
                                    <div class="col-4 text-center">
                                        <i class="bi bi-truck fs-5 mb-1 d-block"></i>
                                        <span class="fs-9 text-uppercase tracking-wider text-secondary">Free
                                            Shipping</span>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="bi bi-arrow-return-left fs-5 mb-1 d-block"></i>
                                        <span class="fs-9 text-uppercase tracking-wider text-secondary">30-Day
                                            Returns</span>
                                    </div>
                                    <div class="col-4 text-center">
                                        <i class="bi bi-shield-check fs-5 mb-1 d-block"></i>
                                        <span
                                            class="fs-9 text-uppercase tracking-wider text-secondary">Authenticity</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js?v=2"></script>
    <script src="assets/js/cart.js?v=2"></script>
    <script src="assets/js/search-filter.js?v=2"></script>
    <script src="assets/js/product-detail.js?v=2"></script>

    <!-- Wishlist Toast -->
    <div id="wishlist-toast" class="wishlist-toast">
        <i class="bi bi-heart-fill me-2"></i> Item added to wishlist
    </div>
</body>

</html>