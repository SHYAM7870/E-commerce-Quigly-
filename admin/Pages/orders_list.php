<?php
include_once __DIR__ . '/../includes/db.php';
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/sidebar.php';

// Flash messages
$flashMsg = '';
if (isset($_GET['msg'])) {
    $msgs = [
        'cancelled' => ['type'=>'success', 'text'=>'Order cancelled successfully.'],
        'updated'   => ['type'=>'info',    'text'=>'Order status updated.'],
        'invalid'   => ['type'=>'danger',  'text'=>'Invalid request.'],
        'error'     => ['type'=>'danger',  'text'=>'Something went wrong.'],
    ];
    $m = $msgs[$_GET['msg']] ?? null;
    if ($m) $flashMsg = "<div class='alert alert-{$m['type']} rounded-3 mb-4'><i class='fas fa-circle-info me-2'></i>{$m['text']}</div>";
}
// Filter by status tab
$statusTab = in_array($_GET['tab'] ?? '', ['all','pending','shipped','delivered','cancelled'])
    ? ($_GET['tab'] ?? 'all')
    : 'all';

$limit  = 8;
$page   = max(1, (int)($_GET['page'] ?? 1));
$offset = ($page - 1) * $limit;

// Build WHERE for tab
$tabWhere = "WHERE 1=1";
if ($statusTab === 'pending')   $tabWhere = "WHERE LOWER(o.status) IN ('pending','processing')";
if ($statusTab === 'shipped')   $tabWhere = "WHERE LOWER(o.status)='shipped'";
if ($statusTab === 'delivered') $tabWhere = "WHERE LOWER(o.status)='delivered'";
if ($statusTab === 'cancelled') $tabWhere = "WHERE LOWER(o.status) IN ('cancelled','canceled')";

// Tab counts
function tabCount($conn, $where) {
    $sql = "SELECT COUNT(id) AS c FROM orders o $where";
    $r = mysqli_query($conn,$sql);

    if(!$r){
        return 0;
    }

    $row = mysqli_fetch_assoc($r);

    return (int)$row['c'];
}

$counts = [
    'all'       => tabCount($conn, "WHERE 1=1"),
    'pending'   => tabCount($conn, "WHERE LOWER(o.status) IN ('pending','processing')"),
    'shipped'   => tabCount($conn, "WHERE LOWER(o.status)='shipped'"),
    'delivered' => tabCount($conn, "WHERE LOWER(o.status)='delivered'"),
    'cancelled' => tabCount($conn, "WHERE LOWER(o.status) IN ('cancelled','canceled')"),
];

// Paginated orders
$totalRecords = tabCount($conn, $tabWhere);
$totalPages   = max(1, (int)ceil($totalRecords / $limit));

$orders = mysqli_query($conn, "
    SELECT
        o.id AS order_id,
        o.total,
        o.status,
        o.created_at,
        o.customer_name,
        o.customer_phone,
        o.customer_address,
        o.payment_method,
        o.payment_status,
        COALESCE(SUM(oi.quantity), 1) AS quantity,
        COALESCE(MAX(oi.price), o.total) AS item_price,
        COALESCE(MAX(p.name), 'Product') AS product_name,
        COALESCE(MAX(p.image), '') AS product_image
    FROM orders o
    LEFT JOIN order_items oi ON oi.order_id = o.id
    LEFT JOIN products p ON p.id = oi.product_id
    $tabWhere
    GROUP BY o.id
    ORDER BY o.id DESC
    LIMIT $limit OFFSET $offset
");

function statusMeta($st) {
    $s = strtolower(trim($st));
    $map = [
        'pending'    => ['cls'=>'sp-pending',  'icon'=>'fa-clock',        'label'=>'Pending'],
        'processing' => ['cls'=>'sp-pending',  'icon'=>'fa-arrows-rotate','label'=>'Processing'],
        'shipped'    => ['cls'=>'sp-shipped',  'icon'=>'fa-truck-fast',   'label'=>'Shipped'],
        'delivered'  => ['cls'=>'sp-delivered','icon'=>'fa-circle-check', 'label'=>'Delivered'],
        'cancelled'  => ['cls'=>'sp-cancelled','icon'=>'fa-circle-xmark', 'label'=>'Cancelled'],
        'canceled'   => ['cls'=>'sp-cancelled','icon'=>'fa-circle-xmark', 'label'=>'Cancelled'],
    ];
    return $map[$s] ?? ['cls'=>'sp-pending','icon'=>'fa-circle','label'=>$st];
}
?>

<style>
.ol-page{ padding:24px 28px; }

/* ── Top bar ── */
.ol-topbar{
  display:flex;align-items:center;justify-content:space-between;
  gap:12px;flex-wrap:wrap;margin-bottom:20px;
}
.ol-topbar h4{ font-size:18px;font-weight:900;color:#0f172a;margin:0; }
.ol-topbar p{ color:#64748b;font-size:13px;margin:2px 0 0; }

.chip{
  display:inline-flex;align-items:center;gap:7px;
  padding:6px 14px;border-radius:999px;
  font-size:12px;font-weight:800;
  background:linear-gradient(135deg,#7c3aed,#4f46e5);
  color:#fff;
  box-shadow:0 4px 14px rgba(124,58,237,.22);
}

/* ── Status tabs ── */
.status-tabs{
  display:flex;gap:6px;flex-wrap:wrap;margin-bottom:18px;
}
.stab{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 14px;border-radius:12px;
  border:1px solid #e2e8f0;background:#fff;
  font-size:12px;font-weight:700;color:#64748b;
  text-decoration:none;transition:.2s;
  white-space:nowrap;
}
.stab:hover{ border-color:#7c3aed;color:#7c3aed; }
.stab.active{
  background:linear-gradient(135deg,#7c3aed,#4f46e5);
  color:#fff;border-color:transparent;
  box-shadow:0 4px 14px rgba(124,58,237,.2);
}
.stab .cnt{
  min-width:18px;height:18px;padding:0 5px;border-radius:999px;
  background:rgba(255,255,255,.22);color:#fff;
  font-size:10px;font-weight:800;
  display:inline-flex;align-items:center;justify-content:center;
}
.stab:not(.active) .cnt{
  background:rgba(124,58,237,.10);color:#7c3aed;
}

/* ── Table card ── */
.ot-card{
  background:#fff;border:1px solid #e9ecef;
  border-radius:20px;overflow:hidden;
  box-shadow:0 4px 18px rgba(15,23,42,.05);
}
.ot-table{ width:100%;border-collapse:collapse; }
.ot-table thead{ background:#f8fafc; }
.ot-table thead th{
  padding:12px 16px;
  font-size:11px;font-weight:800;letter-spacing:.6px;text-transform:uppercase;
  color:#64748b;border-bottom:1px solid #f1f5f9;white-space:nowrap;
}
.ot-table tbody td{
  padding:14px 16px;font-size:13px;color:#374151;
  border-bottom:1px solid #f8fafc;vertical-align:middle;
}
.ot-table tbody tr:hover{ background:#fafbff; }
.ot-table tbody tr:last-child td{ border-bottom:none; }
.ot-table tbody tr.new-row{ background:#faf5ff; }
.ot-table tbody tr.new-row:hover{ background:#f3e8ff!important; }

/* ── Product cell ── */
.prod-cell{ display:flex;align-items:center;gap:12px; }
.ord-thumb{
  width:50px;height:50px;border-radius:13px;
  object-fit:cover;border:1px solid #f1f5f9;flex-shrink:0;
}

/* ── Status pill ── */
.sp{
  display:inline-flex;align-items:center;gap:5px;
  padding:5px 11px;border-radius:999px;
  font-size:11px;font-weight:800;white-space:nowrap;
}
.sp-pending  { background:#fef9c3;color:#854d0e; }
.sp-shipped  { background:#dbeafe;color:#1d4ed8; }
.sp-delivered{ background:#dcfce7;color:#15803d; }
.sp-cancelled{ background:#fee2e2;color:#b91c1c; }

/* ── Action buttons ── */
.act-wrap{ display:flex;gap:5px;align-items:center;justify-content:flex-end;flex-wrap:wrap; }
.ab{
  height:32px;padding:0 12px;border-radius:9px;border:none;cursor:pointer;
  font-size:11px;font-weight:700;display:inline-flex;align-items:center;gap:5px;
  text-decoration:none;transition:.2s;white-space:nowrap;
}
.ab-view   { background:#dbeafe;color:#1d4ed8; }
.ab-view:hover{ background:#2563eb;color:#fff; }
.ab-cancel { background:#fee2e2;color:#b91c1c; }
.ab-cancel:hover{ background:#ef4444;color:#fff; }

/* ── Status dropdown ── */
.dd-wrap{ position:relative; }
.dd-btn{
  height:32px;padding:0 12px;border-radius:9px;
  border:1px solid #e2e8f0;background:#f8fafc;
  font-size:11px;font-weight:700;color:#374151;
  cursor:pointer;display:inline-flex;align-items:center;gap:5px;
  transition:.2s;
}
.dd-btn:hover{ border-color:#7c3aed;color:#7c3aed; }
.dd-menu{
  position:absolute;right:0;top:calc(100%+5px);
  background:#fff;border:1px solid #e9ecef;
  border-radius:12px;min-width:150px;
  box-shadow:0 12px 35px rgba(15,23,42,.14);
  z-index:999;display:none;padding:5px;
}
.dd-wrap:hover .dd-menu,
.dd-wrap:focus-within .dd-menu{ display:block; }
.dd-item{
  display:block;padding:8px 12px;border-radius:8px;
  font-size:12px;font-weight:600;color:#374151;
  text-decoration:none;transition:.15s;
}
.dd-item:hover{ background:#f3e8ff;color:#7c3aed; }

/* ── Pagination ── */
.pag{ display:flex;justify-content:center;gap:6px;padding:16px;flex-wrap:wrap; }
.pag-link{
  min-width:36px;height:36px;padding:0 12px;
  border-radius:10px;border:1px solid #e2e8f0;background:#fff;
  font-weight:700;font-size:12px;
  display:inline-flex;align-items:center;justify-content:center;
  text-decoration:none;color:#374151;transition:.2s;
}
.pag-link:hover{ border-color:#7c3aed;color:#7c3aed; }
.pag-link.active{ background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;border-color:transparent; }
.pag-link.disabled{ opacity:.4;pointer-events:none; }

/* ── Empty ── */
.ot-empty{ padding:3rem;text-align:center;color:#94a3b8; }
.ot-empty i{ font-size:2.5rem;display:block;margin-bottom:10px; }

@keyframes blink{0%,100%{opacity:1;}50%{opacity:.2;}}
.blink-dot{ animation:blink 1s infinite; }
</style>

<div class="ol-page">

  <?= $flashMsg ?>

  <!-- Top bar -->
  <div class="ol-topbar">
    <div>
      <h4>Orders</h4>
      <p>Manage and update customer orders.</p>
    </div>
    <div class="chip">
      <i class="fas fa-shopping-bag"></i>
      <?= $counts['all'] ?> Total Orders
    </div>
  </div>

  <!-- Status tabs -->
  <div class="status-tabs">
    <?php
    $tabs = [
      'all'       => ['label'=>'All',       'icon'=>'fa-list'],
      'pending'   => ['label'=>'Pending',   'icon'=>'fa-clock'],
      'shipped'   => ['label'=>'Shipped',   'icon'=>'fa-truck-fast'],
      'delivered' => ['label'=>'Delivered', 'icon'=>'fa-circle-check'],
      'cancelled' => ['label'=>'Cancelled', 'icon'=>'fa-circle-xmark'],
    ];
    foreach ($tabs as $key => $t):
      $isActive = $statusTab === $key;
    ?>
    <a href="?tab=<?= $key ?>"
       class="stab <?= $isActive?'active':'' ?>">
      <i class="fas <?= $t['icon'] ?>"></i>
      <?= $t['label'] ?>
      <span class="cnt"><?= $counts[$key] ?></span>
    </a>
    <?php endforeach; ?>
  </div>

  <!-- Table -->
  <div class="ot-card">
    <div class="table-responsive">
      <table class="ot-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Product</th>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Status</th>
            <th>Total</th>
            <th style="text-align:right;">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($orders && mysqli_num_rows($orders) > 0):
            $i = $offset+1;
            while ($order = mysqli_fetch_assoc($orders)):
              $meta  = statusMeta($order['status']??'Pending');
              $isNew = in_array(strtolower($order['status']??''),['pending','processing']);
          ?>
          <tr class="<?= $isNew?'new-row':'' ?>">
            <td style="font-weight:700;color:#7c3aed;"><?= $i++ ?></td>

            <td>
              <div class="prod-cell">
                <img src="../../upload/<?= htmlspecialchars($order['product_image']) ?>"
                     class="ord-thumb" alt="">
                <div>
                  <div style="font-weight:700;font-size:13px;"><?= htmlspecialchars($order['product_name']) ?></div>
                  <div style="font-size:11px;color:#94a3b8;">Qty: <?= (int)$order['quantity'] ?></div>
                </div>
              </div>
            </td>

            <td style="font-weight:700;color:#374151;">#QG<?= (int)$order['order_id'] ?></td>

            <td>
              <div style="font-weight:700;"><?= htmlspecialchars($order['customer_name']) ?></div>
              <div style="font-size:11px;color:#94a3b8;"><?= htmlspecialchars($order['customer_phone']) ?></div>
            </td>

            <td>
              <span class="sp <?= $meta['cls'] ?>">
                <?php if($isNew): ?>
                  <span class="blink-dot" style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;"></span>
                <?php else: ?>
                  <i class="fas <?= $meta['icon'] ?>"></i>
                <?php endif; ?>
                <?= htmlspecialchars($meta['label']) ?>
              </span>
            </td>

            <td style="font-weight:800;font-size:15px;color:#7c3aed;">
              ₹<?= number_format((float)$order['total'],2) ?>
            </td>

            <td>
              <div class="act-wrap">

                <!-- View btn (opens modal) -->
                <button class="ab ab-view view-btn"
                  data-id="<?= (int)$order['order_id'] ?>"
                  data-product="<?= htmlspecialchars($order['product_name'],ENT_QUOTES) ?>"
                  data-image="<?= htmlspecialchars($order['product_image'],ENT_QUOTES) ?>"
                  data-cname="<?= htmlspecialchars($order['customer_name'],ENT_QUOTES) ?>"
                  data-cphone="<?= htmlspecialchars($order['customer_phone'],ENT_QUOTES) ?>"
                  data-caddr="<?= htmlspecialchars($order['customer_address'],ENT_QUOTES) ?>"
                  data-status="<?= htmlspecialchars($order['status'],ENT_QUOTES) ?>"
                  data-payment="<?= htmlspecialchars($order['payment_method'],ENT_QUOTES) ?>"
                  data-pstatus="<?= htmlspecialchars($order['payment_status'],ENT_QUOTES) ?>"
                  data-total="<?= number_format((float)$order['total'],2) ?>"
                  data-qty="<?= (int)$order['quantity'] ?>"
                  data-price="<?= number_format((float)$order['item_price'],2) ?>"
                  data-date="<?= htmlspecialchars(date('d M Y, h:i A',strtotime($order['created_at'])),ENT_QUOTES) ?>"
                  onclick="openModal(this)">
                  <i class="fas fa-eye"></i> View
                </button>

                <!-- Status dropdown -->
                <div class="dd-wrap">
                  <button class="dd-btn">
                    <i class="fas fa-pen"></i> Status <i class="fas fa-chevron-down" style="font-size:9px;"></i>
                  </button>
                  <div class="dd-menu">
                    <?php foreach(['Pending','Shipped','Delivered'] as $s): ?>
                    <a class="dd-item"
                       href="../actions/update_order_status.php?id=<?= (int)$order['order_id'] ?>&status=<?= $s ?>&redirect=orders_list.php?tab=<?= $statusTab ?>"
                       onclick="return confirm('Mark as <?= $s ?>?')">
                      <i class="fas <?= ['Pending'=>'fa-clock','Shipped'=>'fa-truck-fast','Delivered'=>'fa-circle-check'][$s] ?> me-2"></i><?= $s ?>
                    </a>
                    <?php endforeach; ?>
                  </div>
                </div>

                <!-- Cancel -->
                <?php if (!in_array(strtolower($order['status']??''),['delivered','cancelled','canceled'])): ?>
                <a href="../actions/cancel_order.php?id=<?= (int)$order['order_id'] ?>"
                   class="ab ab-cancel"
                   onclick="return confirm('Cancel this order?')">
                  <i class="fas fa-xmark"></i>
                </a>
                <?php endif; ?>

              </div>
            </td>
          </tr>
          <?php endwhile; else: ?>
          <tr>
            <td colspan="7">
              <div class="ot-empty">
                <i class="fas fa-box-open"></i>
                No orders in this category.
              </div>
            </td>
          </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pag">
      <a class="pag-link <?= $page<=1?'disabled':'' ?>"
         href="?tab=<?= $statusTab ?>&page=<?= $page-1 ?>">
        <i class="fas fa-chevron-left"></i>
      </a>
      <?php for($p=1;$p<=$totalPages;$p++): ?>
        <a class="pag-link <?= $p==$page?'active':'' ?>"
           href="?tab=<?= $statusTab ?>&page=<?= $p ?>"><?= $p ?></a>
      <?php endfor; ?>
      <a class="pag-link <?= $page>=$totalPages?'disabled':'' ?>"
         href="?tab=<?= $statusTab ?>&page=<?= $page+1 ?>">
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>
    <?php endif; ?>
  </div>

</div>

<!-- ─── Order Detail Modal ─────────────────────────────── -->
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content border-0 rounded-4 shadow-lg overflow-hidden">

      <div class="modal-header" style="background:linear-gradient(135deg,#7c3aed,#4f46e5);color:#fff;">
        <div>
          <h5 class="modal-title fw-bold mb-0" id="modalTitle">Order Details</h5>
          <div class="small opacity-75" id="modalSubtitle"></div>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body p-4">
        <div class="row g-4">
          <div class="col-md-5">
            <img id="mImg" src="" alt=""
                 style="width:100%;height:240px;object-fit:cover;border-radius:16px;border:1px solid #f1f5f9;">
          </div>
          <div class="col-md-7">
            <h5 id="mProduct" class="fw-bold mb-3"></h5>
            <div class="row g-2">
              <?php foreach([
                ['mStatus','Status'],['mPayment','Payment Method'],['mPStatus','Payment Status'],
                ['mDate','Ordered On'],['mQty','Quantity'],['mPrice','Item Price'],['mTotal','Total'],
              ] as [$id,$lbl]): ?>
              <div class="col-6">
                <div style="background:#f8fafc;border-radius:12px;padding:10px 14px;">
                  <div style="font-size:10px;color:#94a3b8;font-weight:700;text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px;"><?= $lbl ?></div>
                  <div id="<?= $id ?>" style="font-size:13px;font-weight:700;color:#0f172a;"></div>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
            <hr>
            <h6 class="fw-bold mb-2 mt-3">Customer Details</h6>
            <div id="mCust" style="font-size:13px;color:#374151;line-height:1.7;"></div>
          </div>
        </div>
      </div>

      <div class="modal-footer border-0 pb-4 px-4">
        <button type="button" class="btn btn-light rounded-3 px-4" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openModal(btn){
  const st  = btn.dataset.status || 'Pending';
  const stl = st.toLowerCase();
  const clr = {delivered:'#16a34a',shipped:'#1d4ed8',cancelled:'#b91c1c',canceled:'#b91c1c'}[stl]||'#854d0e';
  const bg  = {delivered:'#dcfce7',shipped:'#dbeafe',cancelled:'#fee2e2',canceled:'#fee2e2'}[stl]||'#fef9c3';

  document.getElementById('modalTitle').textContent   = 'Order #QG'+btn.dataset.id;
  document.getElementById('modalSubtitle').textContent = btn.dataset.product;
  document.getElementById('mImg').src       = '../../upload/'+btn.dataset.image;
  document.getElementById('mProduct').textContent = btn.dataset.product;
  document.getElementById('mDate').textContent    = btn.dataset.date;
  document.getElementById('mQty').textContent     = btn.dataset.qty;
  document.getElementById('mPrice').textContent   = '₹'+btn.dataset.price;
  document.getElementById('mTotal').textContent   = '₹'+btn.dataset.total;
  document.getElementById('mPayment').textContent = btn.dataset.payment;
  document.getElementById('mPStatus').textContent = btn.dataset.pstatus;
  document.getElementById('mStatus').innerHTML    =
    `<span style="background:${bg};color:${clr};padding:3px 10px;border-radius:999px;font-size:11px;">${st}</span>`;
  document.getElementById('mCust').innerHTML      =
    `<strong>${btn.dataset.cname}</strong><br>${btn.dataset.cphone}<br><span style="color:#94a3b8;">${btn.dataset.caddr}</span>`;

  new bootstrap.Modal(document.getElementById('orderModal')).show();
}
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
