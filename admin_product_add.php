<?php
session_start();
require_once 'api/db.php';

// Must be logged in and admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$is_edit = $id > 0;
$product = [
    'id' => '',
    'name' => '',
    'description' => '',
    'price' => '',
    'category' => 'Clothing',
    'stock' => 0,
    'image_url' => 'images/image.png'
];

if ($is_edit) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $db_product = $stmt->fetch();
    if ($db_product) {
        $product = $db_product;

        // Fetch existing variants
        $stmt = $pdo->prepare("SELECT * FROM product_variants WHERE product_id = ?");
        $stmt->execute([$id]);
        $variants = $stmt->fetchAll();

        foreach ($variants as &$variantRow) {
            $stmt = $pdo->prepare("SELECT size, stock FROM variant_stocks WHERE variant_id = ?");
            $stmt->execute([$variantRow['id']]);
            $variantRow['sizes'] = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
        }
        unset($variantRow); // prevent reference carryover into later foreach loops
    } else {
        $is_edit = false; // Not found, fallback to add
    }
}
$variants = $variants ?? [];
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Edit' : 'Add'; ?> Product - Admin BOREAL</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,500;0,600;1,500;1,600&family=DM+Mono:wght@500;600&family=DM+Sans:wght@400;500;600&family=Oswald:wght@400;500;600;700&family=Playfair+Display:ital,wght@0,600;0,700;1,600;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css?v=2">
</head>

<body class="luxury-light-theme bg-white d-flex flex-column min-vh-100">

    <nav class="navbar navbar-expand-lg sticky-top p-3 boreal-navbar bg-white border-bottom border-light">
        <div class="container-fluid px-lg-5">
            <a class="navbar-brand text-uppercase fw-bold fs-3 tracking-wide text-dark boreal-brand"
                href="admin_products.php">
                <i class="bi bi-asterisk me-2"></i>BOREAL <span
                    class="fs-6 text-accent fw-normal text-capitalize ms-2">Admin</span>
            </a>
            <div class="d-flex align-items-center gap-4 text-dark ms-auto">
                <a class="nav-link text-dark text-uppercase fs-7 tracking-wider" href="admin_products.php">Cancel</a>
            </div>
        </div>
    </nav>

    <section class="flex-grow-1 py-5">
        <div class="container px-lg-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card bg-white border border-light rounded-0 shadow-sm p-4 p-md-5">
                        <div
                            class="d-flex justify-content-between align-items-center mb-4 border-bottom border-light pb-3">
                            <h2 class="text-dark font-playfair mb-1">
                                <?php echo $is_edit ? 'Edit Product' : 'Add New Product'; ?>
                            </h2>
                        </div>

                        <div id="form-alert" class="alert d-none rounded-0 border-0 fs-7"></div>

                        <form id="product-form">
                            <input type="hidden" name="action" value="<?php echo $is_edit ? 'edit' : 'add'; ?>">
                            <?php if ($is_edit): ?><input type="hidden" name="id"
                                    value="<?php echo $product['id']; ?>"><?php endif; ?>

                            <div class="row g-4 text-start">
                                <div class="col-md-7">
                                    <div class="mb-3">
                                        <label
                                            class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Product
                                            Name</label>
                                        <input type="text" class="form-control border-light rounded-0 shadow-none ps-2"
                                            name="name" required
                                            value="<?php echo htmlspecialchars($product['name']); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label
                                            class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Description</label>
                                        <textarea class="form-control border-light rounded-0 shadow-none ps-2"
                                            name="description"
                                            rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label
                                            class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Base
                                            Image URL</label>
                                        <input type="text" class="form-control border-light rounded-0 shadow-none ps-2"
                                            name="image_url"
                                            value="<?php echo htmlspecialchars($product['image_url']); ?>"
                                            placeholder="images/image.png">
                                    </div>
                                </div>
                                <div class="col-md-5">
                                    <div class="card bg-light border-0 rounded-0 p-4">
                                        <div class="mb-3">
                                            <label
                                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Price
                                                ($)</label>
                                            <input type="number" step="0.01"
                                                class="form-control border-light rounded-0 shadow-none ps-2 font-mono"
                                                name="price" required value="<?php echo $product['price']; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Total
                                                Stock</label>
                                            <input type="number"
                                                class="form-control border-light rounded-0 shadow-none ps-2 font-mono"
                                                name="stock" required value="<?php echo $product['stock']; ?>">
                                        </div>
                                        <div class="mb-3">
                                            <label
                                                class="form-label text-dark text-uppercase tracking-wider fs-8 fw-bold">Category</label>
                                            <select class="form-select border-light rounded-0 shadow-none"
                                                name="category">
                                                <?php
                                                $categories = ['Coats', 'Knitwear', 'Footwear', 'Accessories', 'Sale'];
                                                foreach ($categories as $cat) {
                                                    $selected = $product['category'] === $cat ? 'selected' : '';
                                                    echo "<option value=\"$cat\" $selected>$cat</option>";
                                                }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Variants Section -->
                            <div class="mt-5 pt-4 border-top border-light">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h4 class="font-playfair text-dark mb-0">Product Variants (Colors)</h4>
                                    <button type="button"
                                        class="btn btn-outline-dark btn-sm rounded-0 text-uppercase fs-9 tracking-wider"
                                        id="add-variant-btn">
                                        <i class="bi bi-plus me-1"></i> Add Variant
                                    </button>
                                </div>

                                <div id="variants-container">
                                    <?php if (empty($variants)): ?>
                                        <div
                                            class="variant-item-placeholder text-center py-4 bg-light border border-dashed text-secondary fs-8 mb-3">
                                            No variants added yet. Add variants to allow color selection.
                                        </div>
                                    <?php endif; ?>

                                    <?php foreach ($variants as $index => $v): ?>
                                        <div
                                            class="variant-row card bg-light border-0 rounded-0 p-3 mb-3 position-relative">
                                            <button type="button"
                                                class="btn-close position-absolute top-0 end-0 m-2 remove-variant"
                                                style="font-size: 0.7rem;"></button>
                                            <input type="hidden" name="variants[<?php echo $index; ?>][id]"
                                                value="<?php echo $v['id']; ?>">
                                            <div class="row g-3">
                                                <div class="col-md-3">
                                                    <label
                                                        class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Color
                                                        Name</label>
                                                    <input type="text" name="variants[<?php echo $index; ?>][color_name]"
                                                        class="form-control form-control-sm border-light rounded-0"
                                                        placeholder="e.g. Midnight Blue"
                                                        value="<?php echo htmlspecialchars($v['color_name']); ?>" required>
                                                </div>
                                                <div class="col-md-2">
                                                    <label
                                                        class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Hex
                                                        Code</label>
                                                    <input type="text" name="variants[<?php echo $index; ?>][color_hex]"
                                                        class="form-control form-control-sm border-light rounded-0"
                                                        placeholder="#000000"
                                                        value="<?php echo htmlspecialchars($v['color_hex']); ?>" required>
                                                </div>
                                                <div class="col-md-3">
                                                    <label
                                                        class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Variant
                                                        Image URL</label>
                                                    <input type="text" name="variants[<?php echo $index; ?>][image_url]"
                                                        class="form-control form-control-sm border-light rounded-0"
                                                        placeholder="images/colors/blue.png"
                                                        value="<?php echo htmlspecialchars($v['image_url']); ?>" required>
                                                </div>
                                                <div class="col-md-4">
                                                    <label
                                                        class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Stock
                                                        per Size (XS, S, M, L, XL, XXL)</label>
                                                    <div class="d-flex gap-1">
                                                        <?php
                                                        $sizes = ['XS', 'S', 'M', 'L', 'XL', 'XXL'];
                                                        foreach ($sizes as $sz):
                                                            $szVal = $v['sizes'][$sz] ?? 0;
                                                            ?>
                                                            <div class="flex-grow-1">
                                                                <input type="number"
                                                                    name="variants[<?php echo $index; ?>][sizes][<?php echo $sz; ?>]"
                                                                    class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center"
                                                                    placeholder="<?php echo $sz; ?>"
                                                                    value="<?php echo $szVal; ?>" title="<?php echo $sz; ?>">
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <button type="submit"
                                class="btn btn-dark rounded-0 w-100 py-3 text-uppercase fs-7 fw-bold tracking-wider mt-5"
                                id="save-btn">
                                Save Product & Variants
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer class="footer-boreal py-4 border-top mt-auto bg-light">
        <div class="container text-center">
            <p class="text-secondary fs-8 mb-0 text-uppercase tracking-widest">© 2026 BOREAL. Admin Services.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        $(document).ready(function () {
            $('#product-form').on('submit', function (e) {
                e.preventDefault();

                const btn = $('#save-btn');
                const origText = btn.text();
                btn.html('Saving...').prop('disabled', true);

                $.ajax({
                    url: 'api/admin_product_action.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        const alertEl = $('#form-alert');
                        if (response.success) {
                            alertEl.removeClass('d-none alert-danger').addClass('alert-success bg-light text-success').html('<i class="bi bi-check-circle me-2"></i>' + response.message + ' Returning...');
                            setTimeout(() => window.location.href = 'admin_products.php', 1500);
                        } else {
                            alertEl.removeClass('d-none alert-success').addClass('alert-danger text-danger bg-light').html('<i class="bi bi-exclamation-triangle me-2"></i>' + response.message);
                            btn.html(origText).prop('disabled', false);
                        }
                    },
                    error: function () {
                        $('#form-alert').removeClass('d-none alert-success').addClass('alert-danger text-danger bg-light').text('Server error occurred.');
                        btn.html(origText).prop('disabled', false);
                    }
                });
            });

            // Dynamic Variant Handling
            let variantIndex = <?php echo count($variants); ?>;
            $('#add-variant-btn').on('click', function () {
                $('.variant-item-placeholder').addClass('d-none');

                const html = `
                    <div class="variant-row card bg-light border-0 rounded-0 p-3 mb-3 position-relative">
                        <button type="button" class="btn-close position-absolute top-0 end-0 m-2 remove-variant" style="font-size: 0.7rem;"></button>
                        <input type="hidden" name="variants[${variantIndex}][id]" value="">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Color Name</label>
                                <input type="text" name="variants[${variantIndex}][color_name]" class="form-control form-control-sm border-light rounded-0" placeholder="e.g. Midnight Blue" required>
                            </div>
                            <div class="col-md-2">
                                <label class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Hex Code</label>
                                <input type="text" name="variants[${variantIndex}][color_hex]" class="form-control form-control-sm border-light rounded-0" placeholder="#000000" required>
                            </div>
                            <div class="col-md-3">
                                <label class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Variant Image URL</label>
                                <input type="text" name="variants[${variantIndex}][image_url]" class="form-control form-control-sm border-light rounded-0" placeholder="images/colors/blue.png" required>
                            </div>
                            <div class="col-md-4">
                                <label class="fs-9 text-uppercase tracking-wider text-secondary d-block mb-1">Stock per Size (XS, S, M, L, XL, XXL)</label>
                                <div class="d-flex gap-1">
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][XS]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="XS" value="0"></div>
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][S]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="S" value="0"></div>
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][M]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="M" value="0"></div>
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][L]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="L" value="0"></div>
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][XL]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="XL" value="0"></div>
                                    <div class="flex-grow-1"><input type="number" name="variants[${variantIndex}][sizes][XXL]" class="form-control form-control-sm border-light rounded-0 px-1 font-mono fs-9 text-center" placeholder="XXL" value="0"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                $('#variants-container').append(html);
                variantIndex++;
            });

            $(document).on('click', '.remove-variant', function () {
                $(this).closest('.variant-row').remove();
                if ($('.variant-row').length === 0) {
                    $('.variant-item-placeholder').removeClass('d-none');
                }
            });
        });
    </script>
</body>

</html>