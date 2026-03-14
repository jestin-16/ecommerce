<?php
session_start();
require_once 'api/db.php';

// Must be logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$is_edit = $id > 0;
$product = [
    'id' => '', 'name' => '', 'description' => '', 
    'price' => '', 'category' => 'Clothing', 'stock' => 0, 
    'image_url' => 'images/image.png'
];

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $db_product = $stmt->fetch();
    if ($db_product) {
        $product = $db_product;
    } else {
        $is_edit = false; // Not found, fallback to add
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product - Admin BOREAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>
<body class="luxury-dark-theme bg-boreal-darker d-flex flex-column min-vh-100">
    
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top p-3 boreal-navbar bg-boreal-dark border-bottom border-dark-subtle">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-white boreal-brand" href="admin_products.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL <span class="fs-6 text-accent fw-normal text-capitalize ms-2">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-4 text-white ms-auto">
                 <a class="nav-link text-white text-uppercase fs-7 tracking-wider" href="admin_products.php">Cancel</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5 d-flex align-items-center">
        <div class="container px-lg-5">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card bg-boreal-dark border border-dark-subtle rounded-0 shadow-luxury p-4 p-md-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-dark-subtle pb-3">
                             <h2 class="text-white font-playfair mb-1"><?php echo $is_edit ? 'Edit Product' : 'Add New Product'; ?></h2>
                        </div>

                        <div id="form-alert" class="alert d-none rounded-0 border-0 fs-7"></div>

                        <form id="product-form">
                            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
                            <?php if($is_edit): ?><input type="hidden" name="id" value="<?php echo $product['id']; ?>"><?php endif; ?>

                            <div class="row g-4 text-start">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label text-white text-uppercase tracking-wider fs-8">Product Name</label>
                                        <input type="text" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" name="name" required value="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-white text-uppercase tracking-wider fs-8">Description</label>
                                        <textarea class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" name="description" rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                         <label class="form-label text-white text-uppercase tracking-wider fs-8">Image URL</label>
                                         <input type="text" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" placeholder="images/image.png">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                     <div class="mb-3">
                                        <label class="form-label text-white text-uppercase tracking-wider fs-8">Price ($)</label>
                                        <input type="number" step="0.01" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2 font-mono" name="price" required value="<?php echo $product['price']; ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-white text-uppercase tracking-wider fs-8">Stock</label>
                                        <input type="number" class="form-control bg-transparent border-secondary text-white rounded-0 shadow-none ps-2 font-mono" name="stock" required value="<?php echo $product['stock']; ?>">
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label text-white text-uppercase tracking-wider fs-8">Category</label>
                                        <select class="form-select bg-dark border-secondary text-white rounded-0 shadow-none" name="category">
                                            <?php 
                                            $categories = ['Clothing', 'Electronics', 'Books', 'Home'];
                                            foreach($categories as $cat) {
                                                $selected = $product['category'] === $cat ? 'selected' : '';
                                                echo "<option value=\"$cat\" $selected>$cat</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-light rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-wider mt-4" id="save-btn">
                                Save Product
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-boreal py-4 border-top border-dark-subtle mt-auto bg-boreal-deep">
        <div class="container text-center">
             <p class="text-secondary-light fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Admin Services.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#product-form').on('submit', function(e) {
                e.preventDefault();
                
                const btn = $('#save-btn');
                const origText = btn.text();
                btn.html('Saving...').prop('disabled', true);
                
                $.ajax({
                    url: 'api/admin_product_action.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function(response) {
                        const alertEl = $('#form-alert');
                        if(response.success) {
                            alertEl.removeClass('d-none alert-danger').addClass('alert-success bg-dark text-success').html('<i class="bi bi-check-circle me-2"></i>' + response.message + ' Returning...');
                            setTimeout(() => window.location.href = 'admin_products.php', 1500);
                        } else {
                            alertEl.removeClass('d-none alert-success').addClass('alert-danger text-danger bg-dark').html('<i class="bi bi-exclamation-triangle me-2"></i>' + response.message);
                            btn.html(origText).prop('disabled', false);
                        }
                    },
                    error: function() {
                        $('#form-alert').removeClass('d-none alert-success').addClass('alert-danger text-danger bg-dark').text('Server error occurred.');
                        btn.html(origText).prop('disabled', false);
                    }
                });
            });
        });
    </script>
</body>
</html>
