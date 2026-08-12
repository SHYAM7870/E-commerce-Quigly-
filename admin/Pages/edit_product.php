<?php
include("../includes/db.php");
include("../includes/header.php");
include("../includes/sidebar.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header("Location: product_list.php");
    exit;
}

$query = "SELECT * FROM products WHERE id='$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "<div class='container-fluid py-4'><div class='alert alert-danger'>Product not found.</div></div>";
    include("../includes/footer.php");
    exit;
}
?>

<div class="container-fluid py-3">
    <h4 class="mt-3 mb-4 fw-bold text-primary border-start border-4 ps-3">
        Edit Product
    </h4>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="../actions/update_product.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= (int)$data['id']; ?>">

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" value="<?= htmlspecialchars($data['name']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Price</label>
                        <input type="number" step="0.01" name="price" id="price" value="<?= htmlspecialchars($data['price']); ?>" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Original Price</label>
                        <input type="number" step="0.01" name="original_price" id="original_price" value="<?= htmlspecialchars($data['original_price'] ?? ''); ?>" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Discount (%)</label>
                        <input type="number" name="discount" id="discount" value="<?= (int)($data['discount'] ?? 0); ?>" class="form-control" readonly>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stock Quantity</label>
                        <input type="number" name="stock_qty" value="<?= (int)($data['stock_qty'] ?? 0); ?>" class="form-control" min="0">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Stock Status</label>
                        <select name="stock_status" class="form-control">
                            <option value="1" <?= ((int)($data['stock_status'] ?? 1) === 1) ? 'selected' : ''; ?>>Enabled</option>
                            <option value="0" <?= ((int)($data['stock_status'] ?? 1) === 0) ? 'selected' : ''; ?>>Disabled</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Category</label>
                        <select name="category_id" id="category" class="form-control" required>
                            <option value="">Select Category</option>
                            <?php
                            $cat = mysqli_query($conn, "SELECT * FROM categories ORDER BY id DESC");
                            while ($c = mysqli_fetch_assoc($cat)) {
                                $selected = ((int)$c['id'] === (int)$data['category_id']) ? "selected" : "";
                                echo "<option value='" . (int)$c['id'] . "' $selected>" . htmlspecialchars($c['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Subcategory</label>
                        <select name="subcategory_id" id="subcategory" class="form-control" required>
                            <option value="">Select Subcategory</option>
                            <?php
                            $sub = mysqli_query($conn, "SELECT * FROM subcategories WHERE category_id=" . (int)$data['category_id']);
                            while ($s = mysqli_fetch_assoc($sub)) {
                                $selected = ((int)$s['id'] === (int)$data['subcategory_id']) ? "selected" : "";
                                echo "<option value='" . (int)$s['id'] . "' $selected>" . htmlspecialchars($s['name']) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="5"><?= htmlspecialchars($data['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Featured</label>
                        <select name="featured" class="form-control">
                            <option value="0" <?= ((int)($data['featured'] ?? 0) === 0) ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?= ((int)($data['featured'] ?? 0) === 1) ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Trending</label>
                        <select name="trending" class="form-control">
                            <option value="0" <?= ((int)($data['trending'] ?? 0) === 0) ? 'selected' : ''; ?>>No</option>
                            <option value="1" <?= ((int)($data['trending'] ?? 0) === 1) ? 'selected' : ''; ?>>Yes</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Current Image</label><br>
                        <img src="../../upload/<?= htmlspecialchars($data['image']); ?>" width="100" class="rounded border" alt="">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Change Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="mt-4">
                    <button class="btn btn-success">Update Product</button>
                    <a href="product_list.php" class="btn btn-secondary ms-2">Back</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById("category").addEventListener("change", function(){
    let cat_id = this.value;
    fetch("../actions/fetch_subcat.php?cat_id=" + cat_id)
        .then(res => res.text())
        .then(data => {
            document.getElementById("subcategory").innerHTML = data;
        });
});

function calcDiscount() {
    const price = parseFloat(document.getElementById("price").value) || 0;
    const original = parseFloat(document.getElementById("original_price").value) || 0;
    let discount = 0;

    if (original > 0 && price > 0 && original > price) {
        discount = Math.round(((original - price) / original) * 100);
    }

    document.getElementById("discount").value = discount;
}

document.getElementById("price").addEventListener("input", calcDiscount);
document.getElementById("original_price").addEventListener("input", calcDiscount);
calcDiscount();
</script>

<?php include("../includes/footer.php"); ?>