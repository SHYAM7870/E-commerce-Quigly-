<?php
include_once 'includes/db.php';
include 'includes/header.php';
include 'includes/sidebar.php';

// Stats
$usersCount        = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM quigly_table"))['t']??0);
$productsCount     = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM products"))['t']??0);
$categoriesCount   = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM categories"))['t']??0);
$ordersCount       = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM orders"))['t']??0);
$pendingOrders     = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM orders WHERE LOWER(status) IN ('pending','processing')"))['t']??0);
$pendingReviews    = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM reviews WHERE status='pending'"))['t']??0);
$totalRevenue      = (float)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COALESCE(SUM(total),0) AS t FROM orders WHERE LOWER(status)='delivered'"))['t']??0);

// Recent orders
$recentOrders = mysqli_query($conn,"
    SELECT o.id AS order_id, o.total, o.status, o.created_at,
           o.customer_name, o.customer_phone,
           oi.quantity, p.name AS product_name, p.image AS product_image
    FROM orders o
    INNER JOIN order_items oi ON oi.order_id = o.id
    INNER JOIN products p ON p.id = oi.product_id
    ORDER BY o.id DESC LIMIT 6
");
?>

<style>
/* ── Page wrapper ── */
.dash-page { padding: 24px 28px; }
@media(max-width:576px) { .dash-page { padding: 16px 14px; } }

/* ── Stat cards ── */
.stat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 28px;
}
@media(max-width:1100px) { .stat-grid { grid-template-columns: repeat(2, 1fr); } }
@media(max-width:500px)  { .stat-grid { grid-template-columns: 1fr; } }

.stat-card {
  border-radius: 20px;
  padding: 20px 22px;
  display: flex; align-items: center; gap: 16px;
  background: var(--adm-surface);
  border: 1px solid var(--adm-border);
  box-shadow: var(--adm-shadow);
  transition: transform .22s, box-shadow .22s, background var(--transition), border-color var(--transition);
  position: relative; overflow: hidden;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--adm-shadow-md); }
.stat-card::before {
  content: ''; position: absolute; right: -14px; top: -14px;
  width: 80px; height: 80px; border-radius: 50%;
  opacity: .08;
}

.stat-icon {
  width: 52px; height: 52px; border-radius: 16px;
  display: flex; align-items: center; justify-content: center;
  font-size: 22px; flex-shrink: 0;
}
.stat-num { font-size: 28px; font-weight: 900; letter-spacing: -.04em; line-height: 1; }
.stat-lbl { font-size: 12px; font-weight: 600; color: var(--adm-text-muted); margin-top: 3px; text-transform: uppercase; letter-spacing: .5px; }
.stat-badge {
  position: absolute; top: 14px; right: 14px;
  padding: 3px 10px; border-radius: 999px;
  font-size: 10px; font-weight: 800;
}

/* Individual colours */
.sc-purple .stat-icon { background: #ede9fe; color: #7c3aed; }
.sc-purple .stat-num  { color: #7c3aed; }
.sc-purple::before    { background: #7c3aed; }

.sc-blue .stat-icon { background: #dbeafe; color: #2563eb; }
.sc-blue .stat-num  { color: #2563eb; }
.sc-blue::before    { background: #2563eb; }

.sc-green .stat-icon { background: #dcfce7; color: #16a34a; }
.sc-green .stat-num  { color: #16a34a; }
.sc-green::before    { background: #16a34a; }

.sc-orange .stat-icon { background: #ffedd5; color: #ea580c; }
.sc-orange .stat-num  { color: #ea580c; }
.sc-orange::before    { background: #ea580c; }

.sc-indigo .stat-icon { background: #e0e7ff; color: #4338ca; }
.sc-indigo .stat-num  { color: #4338ca; }
.sc-indigo::before    { background: #4338ca; }

.sc-yellow .stat-icon { background: #fef9c3; color: #ca8a04; }
.sc-yellow .stat-num  { color: #ca8a04; }
.sc-yellow::before    { background: #ca8a04; }

.sc-teal .stat-icon { background: #ccfbf1; color: #0d9488; }
.sc-teal .stat-num  { color: #0d9488; }
.sc-teal::before    { background: #0d9488; }

.sc-red .stat-icon { background: #fee2e2; color: #dc2626; }
.sc-red .stat-num  { color: #dc2626; }
.sc-red::before    { background: #dc2626; }

/* ── Section title ── */
.sec-title {
  font-size: 16px; font-weight: 800; color: var(--adm-text);
  margin: 0 0 14px; display: flex; align-items: center; gap: 10px;
}
.sec-title span {
  display: inline-block; width: 4px; height: 18px; border-radius: 2px;
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
}

/* ── Quick actions ── */
.qa-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
  gap: 12px;
  margin-bottom: 28px;
}
.qa-btn {
  display: flex; flex-direction: column; align-items: center; gap: 8px;
  padding: 16px 12px;
  border-radius: 16px; border: 1px solid var(--adm-border);
  background: var(--adm-surface); color: var(--adm-text-muted);
  text-decoration: none; font-size: 12px; font-weight: 700;
  transition: var(--transition); cursor: pointer;
  box-shadow: var(--adm-shadow);
}
.qa-btn:hover {
  border-color: var(--adm-accent); color: var(--adm-accent);
  background: var(--adm-accent-bg);
  transform: translateY(-2px);
  box-shadow: var(--adm-shadow-md);
}
.qa-btn i { font-size: 20px; }

/* ── Orders table card ── */
.data-card {
  background: var(--adm-surface); border: 1px solid var(--adm-border);
  border-radius: 20px; overflow: hidden;
  box-shadow: var(--adm-shadow);
  margin-bottom: 28px;
}
.data-card-head {
  padding: 16px 20px; border-bottom: 1px solid var(--adm-border);
  display: flex; align-items: center; justify-content: space-between;
}
.data-card-head h6 { margin: 0; font-size: 14px; font-weight: 800; color: var(--adm-text); }

.dt { width: 100%; border-collapse: collapse; }
.dt thead { background: var(--adm-surface2); }
.dt thead th {
  padding: 11px 16px; font-size: 11px; font-weight: 800;
  color: var(--adm-text-muted); text-transform: uppercase; letter-spacing: .6px;
  border-bottom: 1px solid var(--adm-border); white-space: nowrap;
}
.dt tbody td {
  padding: 13px 16px; font-size: 13px; color: var(--adm-text);
  border-bottom: 1px solid var(--adm-border); vertical-align: middle;
}
.dt tbody tr:hover { background: var(--adm-surface2); }
.dt tbody tr:last-child td { border-bottom: none; }

.order-img {
  width: 44px; height: 44px; border-radius: 12px;
  object-fit: cover; border: 1px solid var(--adm-border);
}
.status-pill {
  display: inline-flex; align-items: center; gap: 5px;
  padding: 4px 11px; border-radius: 999px;
  font-size: 11px; font-weight: 800;
  white-space: nowrap;
}
.sp-pending   { background: #fef9c3; color: #854d0e; }
.sp-processing{ background: #fef9c3; color: #854d0e; }
.sp-shipped   { background: #dbeafe; color: #1d4ed8; }
.sp-delivered { background: #dcfce7; color: #15803d; }
.sp-cancelled { background: #fee2e2; color: #b91c1c; }

/* ── Highlighted new orders ── */
.new-order-row { background: var(--adm-accent-bg); }
.new-order-row:hover { background: var(--adm-accent-glow) !important; }
</style>

<div class="dash-page">

  <!-- Page header -->
  <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
    <div>
      <h4 style="font-size:20px;font-weight:900;color:var(--adm-text);margin:0;">Dashboard</h4>
      <p style="color:var(--adm-text-muted);font-size:13px;margin:2px 0 0;">Welcome back, <?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></p>
    </div>
    <div style="font-size:12px;color:var(--adm-text-faint);font-weight:600;">
      <?= date('l, d F Y') ?>
    </div>
  </div>

  <!-- Stat cards -->
  <div class="stat-grid">

    <div class="stat-card sc-purple">
      <div class="stat-icon"><i class="fas fa-users"></i></div>
      <div>
        <div class="stat-num"><?= $usersCount ?></div>
        <div class="stat-lbl">Customers</div>
      </div>
    </div>

    <div class="stat-card sc-blue">
      <div class="stat-icon"><i class="fas fa-boxes-stacked"></i></div>
      <div>
        <div class="stat-num"><?= $productsCount ?></div>
        <div class="stat-lbl">Products</div>
      </div>
    </div>

    <div class="stat-card sc-orange">
      <div class="stat-icon"><i class="fas fa-shopping-bag"></i></div>
      <div>
        <div class="stat-num"><?= $ordersCount ?></div>
        <div class="stat-lbl">Total Orders</div>
      </div>
      <?php if ($pendingOrders > 0): ?>
      <span class="stat-badge" style="background:#fef9c3;color:#854d0e;"><?= $pendingOrders ?> Pending</span>
      <?php endif; ?>
    </div>

    <div class="stat-card sc-green">
      <div class="stat-icon"><i class="fas fa-indian-rupee-sign"></i></div>
      <div>
        <div class="stat-num">₹<?= number_format($totalRevenue,0) ?></div>
        <div class="stat-lbl">Revenue</div>
      </div>
    </div>

    <div class="stat-card sc-indigo">
      <div class="stat-icon"><i class="fas fa-layer-group"></i></div>
      <div>
        <div class="stat-num"><?= $categoriesCount ?></div>
        <div class="stat-lbl">Categories</div>
      </div>
    </div>

    <div class="stat-card <?= $pendingReviews > 0 ? 'sc-yellow' : 'sc-teal' ?>">
      <div class="stat-icon"><i class="fas fa-star"></i></div>
      <div>
        <div class="stat-num"><?= $pendingReviews ?></div>
        <div class="stat-lbl">Pending Reviews</div>
      </div>
      <?php if ($pendingReviews > 0): ?>
      <span class="stat-badge" style="background:#fef9c3;color:#854d0e;">Action needed</span>
      <?php endif; ?>
    </div>

    <div class="stat-card sc-red">
      <div class="stat-icon"><i class="fas fa-clock"></i></div>
      <div>
        <div class="stat-num"><?= $pendingOrders ?></div>
        <div class="stat-lbl">New Orders</div>
      </div>
    </div>

    <div class="stat-card sc-teal">
      <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
      <div>
        <?php
        $deliveredCount = (int)(mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) AS t FROM orders WHERE LOWER(status)='delivered'"))['t']??0);
        ?>
        <div class="stat-num"><?= $deliveredCount ?></div>
        <div class="stat-lbl">Delivered</div>
      </div>
    </div>

  </div>

  <!-- Quick actions -->
  <h6 class="sec-title"><span></span>Quick Actions</h6>
  <div class="qa-grid">
    <a href="Pages/add_product.php"     class="qa-btn"><i class="fas fa-plus-circle"></i>Add Product</a>
    <a href="Pages/add_category.php"    class="qa-btn"><i class="fas fa-layer-group"></i>Add Category</a>
    <a href="Pages/add_subcategory.php" class="qa-btn"><i class="fas fa-sitemap"></i>Add Subcategory</a>
    <a href="Pages/user_list.php"       class="qa-btn"><i class="fas fa-users"></i>View Users</a>
    <a href="Pages/orders_list.php"     class="qa-btn"><i class="fas fa-shopping-bag"></i>View Orders</a>
    <a href="Pages/reviews_list.php"    class="qa-btn"><i class="fas fa-star"></i>Reviews</a>
  </div>

  <!-- Recent orders -->
  <h6 class="sec-title"><span></span>Recent Orders</h6>
  <div class="data-card">
    <div class="data-card-head">
      <h6>Latest 6 orders</h6>
      <a href="Pages/orders_list.php" style="font-size:12px;font-weight:700;color:#7c3aed;text-decoration:none;">
        View all &rarr;
      </a>
    </div>
    <div style="overflow-x:auto;">
      <table class="dt">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Customer</th>
            <th>Qty</th>
            <th>Total</th>
            <th>Status</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($recentOrders && mysqli_num_rows($recentOrders) > 0):
            $i=1; while ($o = mysqli_fetch_assoc($recentOrders)):
              $st  = $o['status'] ?? 'Pending';
              $stl = strtolower($st);
              $isNew = in_array($stl,['pending','processing']);
          ?>
          <tr class="<?= $isNew ? 'new-order-row' : '' ?>">
            <td style="font-weight:700;color:#7c3aed;">#<?= $i++ ?></td>
            <td>
              <div style="display:flex;align-items:center;gap:10px;">
                <img src="../upload/<?= htmlspecialchars($o['product_image']) ?>"
                     class="order-img" alt="">
                <div>
                  <div style="font-weight:700;font-size:13px;"><?= htmlspecialchars($o['product_name']) ?></div>
                  <div style="font-size:11px;color:#94a3b8;">Order #QG<?= (int)$o['order_id'] ?></div>
                </div>
              </div>
            </td>
            <td>
              <div style="font-weight:700;"><?= htmlspecialchars($o['customer_name']) ?></div>
              <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($o['customer_phone']) ?></div>
            </td>
            <td style="font-weight:700;"><?= (int)$o['quantity'] ?></td>
            <td style="font-weight:800;color:#7c3aed;">₹<?= number_format((float)$o['total'],2) ?></td>
            <td>
              <span class="status-pill sp-<?= $stl ?>">
                <?php if($isNew): ?><span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;animation:blink 1s infinite;"></span><?php endif; ?>
                <?= htmlspecialchars($st) ?>
              </span>
            </td>
            <td style="font-size:11px;color:#94a3b8;white-space:nowrap;">
              <?= !empty($o['created_at']) ? date('d M Y', strtotime($o['created_at'])) : '' ?>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr><td colspan="7" style="text-align:center;padding:2rem;color:#94a3b8;">No orders yet</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<style>
@keyframes blink{0%,100%{opacity:1;}50%{opacity:.3;}}
</style>

<?php include 'includes/footer.php'; ?>
