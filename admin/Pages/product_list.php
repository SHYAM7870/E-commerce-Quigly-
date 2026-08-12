<?php
include '../../function.php';
include '../includes/header.php';
include '../includes/sidebar.php';

$limit  = 8;
$page   = max(1, (int)($_GET['page'] ?? 1));
$search = trim($_GET['search'] ?? '');
$offset = ($page - 1) * $limit;

$where = '';
if ($search !== '') {
    $s = mysqli_real_escape_string($conn, $search);
    $where = "WHERE p.name LIKE '%$s%' OR c.name LIKE '%$s%'";
}

$countRow = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT COUNT(*) AS total
    FROM products p LEFT JOIN categories c ON c.id=p.category_id
    $where
"));
$totalRecords = (int)($countRow['total'] ?? 0);
$totalPages = max(1, (int)ceil($totalRecords / $limit));

$result = mysqli_query($conn, "
    SELECT p.*, c.name AS cat_name, s.name AS sub_name
    FROM products p
    LEFT JOIN categories c ON c.id=p.category_id
    LEFT JOIN subcategories s ON s.id=p.subcategory_id
    $where
    ORDER BY p.id DESC
    LIMIT $limit OFFSET $offset
");
?>

<style>
.pl-page{ padding:24px 28px; }
.pl-topbar{
  display:flex;align-items:center;justify-content:space-between;
  gap:14px;flex-wrap:wrap;margin-bottom:22px;
}
.pl-topbar h4{ font-size:18px;font-weight:900;color:#0f172a;margin:0; }
.pl-topbar p{ color:#64748b;font-size:13px;margin:2px 0 0; }
.pl-actions{ display:flex;align-items:center;gap:10px;flex-wrap:wrap; }
.search-input-wrap{
  display:flex;align-items:center;gap:9px;
  border:1px solid #e2e8f0;border-radius:12px;
  background:#f8fafc;padding:0 14px;height:40px;
  transition:.2s;
}
.search-input-wrap:focus-within{
  border-color:#7c3aed;background:#fff;
  box-shadow:0 0 0 3px rgba(124,58,237,.10);
}
.search-input-wrap input{
  border:none;outline:none;background:transparent;
  font-size:13px;font-weight:500;color:#0f172a;width:180px;
}
.search-input-wrap input::placeholder{ color:#94a3b8; }
.pt-card{
  background:#fff;border:1px solid #e9ecef;
  border-radius:20px;overflow:hidden;
  box-shadow:0 4px 18px rgba(15,23,42,.05);
}
.pt-table{ width:100%;border-collapse:collapse; }
.pt-table thead{ background:#f8fafc; }
.pt-table thead th{
  padding:12px 16px;
  font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;
  color:#64748b;border-bottom:1px solid #f1f5f9;white-space:nowrap;
}
.pt-table tbody td{
  padding:14px 16px;font-size:13px;color:#374151;
  border-bottom:1px solid #f8fafc;vertical-align:middle;
}
.pt-table tbody tr:hover{ background:#fafbff; }
.pt-table tbody tr:last-child td{ border-bottom:none; }
.prod-cell{ display:flex;align-items:center;gap:12px; }
.prod-thumb{
  width:52px;height:52px;border-radius:14px;
  object-fit:cover;border:1px solid #f1f5f9;
  flex-shrink:0;
}
.prod-name{ font-weight:700;font-size:13px;color:#0f172a;margin-bottom:2px; }
.prod-id{ font-size:11px;color:#94a3b8;font-weight:600; }
.price-cell .curr{ font-size:16px;font-weight:900;color:#7c3aed; }
.price-cell .orig{
  font-size:11px;color:#94a3b8;text-decoration:line-through;margin-left:4px;
}
.cat-pill{
  display:inline-flex;align-items:center;
  padding:4px 11px;border-radius:999px;
  font-size:11px;font-weight:700;
  background:#ede9fe;color:#6d28d9;
  white-space:nowrap;
}
.subcat-pill{
  display:inline-flex;align-items:center;
  padding:4px 11px;border-radius:999px;
  font-size:11px;font-weight:700;
  background:#f0fdf4;color:#16a34a;
  white-space:nowrap;
}
.act-wrap{ display:flex;gap:6px;align-items:center; }
.act-edit,.act-del,.act-stock{
  width:34px;height:34px;border-radius:10px;
  display:inline-flex;align-items:center;justify-content:center;
  font-size:14px;transition:.2s;text-decoration:none;
  border:none;cursor:pointer;
}
.act-edit{ background:#fef9c3;color:#b45309; }
.act-edit:hover{ background:#f59e0b;color:#fff; }
.act-del{ background:#fee2e2;color:#b91c1c; }
.act-del:hover{ background:#ef4444;color:#fff; }
.act-stock{ background:#dbeafe;color:#1d4ed8; }
.act-stock:hover{ background:#2563eb;color:#fff; }
.pag{ display:flex;justify-content:center;gap:6px;padding:16px;flex-wrap:wrap; }
.pag-link{
  min-width:38px;height:38px;padding:0 12px;
  border-radius:10px;border:1px solid #e2e8f0;
  background:#fff;font-weight:700;font-size:13px;
  display:inline-flex;align-items:center;justify-content:center;
  text-decoration:none;color:#374151;transition:.2s;
}
.pag-link:hover{ border-color:#7c3aed;color:#7c3aed; }
.pag-link.active{ background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border-color:transparent; }
.pag-link.disabled{ opacity:.4;pointer-events:none; }
.pt-empty{ padding:3rem;text-align:center;color:#94a3b8; }
.pt-empty i{ font-size:2.5rem;display:block;margin-bottom:10px; }
.stock-ok{
  display:inline-flex;align-items:center;gap:6px;
  font-size:11px;font-weight:700;
  color:#16a34a;background:#dcfce7;padding:4px 10px;border-radius:999px;
}
.stock-off{
  display:inline-flex;align-items:center;gap:6px;
  font-size:11px;font-weight:700;
  color:#b91c1c;background:#fee2e2;padding:4px 10px;border-radius:999px;
}
</style>

<div class="pl-page">

  <div class="pl-topbar">
    <div>
      <h4>Product List</h4>
      <p><?= $totalRecords ?> product<?= $totalRecords != 1 ? 's' : '' ?> total</p>
    </div>
    <div class="pl-actions">
      <form method="GET" style="display:contents;">
        <div class="search-input-wrap">
          <i class="fas fa-search" style="color:#94a3b8;font-size:13px;"></i>
          <input type="text" name="search" placeholder="Search products…" value="<?= htmlspecialchars($search) ?>">
        </div>
        <button type="submit" class="act-edit" style="width:auto;padding:0 14px;height:40px;border-radius:12px;font-size:13px;font-weight:700;gap:6px;display:inline-flex;align-items:center;">
          <i class="fas fa-search"></i> Search
        </button>
        <?php if ($search): ?>
          <a href="product_list.php" class="act-del" style="width:auto;padding:0 14px;height:40px;border-radius:12px;font-size:13px;font-weight:700;gap:6px;display:inline-flex;align-items:center;text-decoration:none;">
            <i class="fas fa-times"></i> Clear
          </a>
        <?php endif; ?>
      </form>

      <a href="add_product.php"
         style="height:40px;padding:0 16px;border-radius:12px;background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;font-size:13px;font-weight:700;display:inline-flex;align-items:center;gap:7px;text-decoration:none;box-shadow:0 4px 14px rgba(124,58,237,.25);">
        <i class="fas fa-plus"></i> Add Product
      </a>
    </div>
  </div>

  <div class="pt-card">
    <div class="table-responsive">
      <table class="pt-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Price</th>
            <th>Category</th>
            <th>Subcategory</th>
            <th>Stock</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($result && mysqli_num_rows($result) > 0):
            $i = $offset + 1;
            while ($row = mysqli_fetch_assoc($result)):
              $img = trim($row['image'] ?? '');
              $qty = (int)($row['stock_qty'] ?? 0);
              $stockStatus = (int)($row['stock_status'] ?? 1);
              $isAvailable = ($stockStatus === 1 && $qty > 0);
          ?>
          <tr>
            <td style="font-weight:700;color:#7c3aed;"><?= $i++ ?></td>

            <td>
              <div class="prod-cell">
                <img src="../../upload/<?= htmlspecialchars($img) ?>"
                     class="prod-thumb"
                     onerror="this.style.background='#f3f4f6'"
                     alt="">
                <div>
                  <div class="prod-name"><?= htmlspecialchars($row['name']) ?></div>
                  <div class="prod-id">ID #<?= (int)$row['id'] ?></div>
                </div>
              </div>
            </td>

            <td class="price-cell">
              <span class="curr">₹<?= number_format((float)$row['price'], 2) ?></span>
              <?php if (!empty($row['original_price']) && (float)$row['original_price'] > (float)$row['price']): ?>
                <span class="orig">₹<?= number_format((float)$row['original_price'], 2) ?></span>
              <?php endif; ?>
            </td>

            <td><span class="cat-pill"><?= htmlspecialchars($row['cat_name'] ?: '—') ?></span></td>
            <td><span class="subcat-pill"><?= htmlspecialchars($row['sub_name'] ?: '—') ?></span></td>

            <td>
              <?php if ($isAvailable): ?>
                <span class="stock-ok"><i class="fas fa-circle"></i> In Stock (<?= $qty ?>)</span>
              <?php else: ?>
                <span class="stock-off"><i class="fas fa-circle"></i> Out of Stock</span>
              <?php endif; ?>
            </td>

            <td style="text-align:right;">
              <div class="act-wrap" style="justify-content:flex-end;">
                <a href="../actions/toggle_stock.php?id=<?= (int)$row['id'] ?>"
                   class="act-stock"
                   title="Toggle Stock Status">
                  <i class="fas fa-box"></i>
                </a>

                <a href="edit_product.php?id=<?= (int)$row['id'] ?>" class="act-edit" title="Edit">
                  <i class="fas fa-pen"></i>
                </a>

                <a href="../actions/delete_product.php?id=<?= (int)$row['id'] ?>"
                   class="act-del"
                   title="Delete"
                   onclick="return confirm('Delete this product?')">
                  <i class="fas fa-trash"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="7">
              <div class="pt-empty">
                <i class="fas fa-box-open"></i>
                <?= $search ? 'No products match your search.' : 'No products found.' ?>
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <?php if ($totalPages > 1): ?>
    <div class="pag">
      <a class="pag-link <?= $page <= 1 ? 'disabled' : '' ?>"
         href="?page=<?= $page - 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
        <i class="fas fa-chevron-left"></i>
      </a>
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a class="pag-link <?= $p == $page ? 'active' : '' ?>"
           href="?page=<?= $p ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
          <?= $p ?>
        </a>
      <?php endfor; ?>
      <a class="pag-link <?= $page >= $totalPages ? 'disabled' : '' ?>"
         href="?page=<?= $page + 1 ?><?= $search ? '&search=' . urlencode($search) : '' ?>">
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php include '../includes/footer.php'; ?>