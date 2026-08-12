<?php
// admin/Pages/reviews_list.php  (ENHANCED)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include("../includes/db.php");

if (!isset($_SESSION['email'])) {
    header("Location: ../login.php");
    exit;
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$filterProductId = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$filterStatus    = isset($_GET['status'])     ? trim($_GET['status'])     : '';
$filterSearch    = isset($_GET['search'])     ? trim($_GET['search'])     : '';

$limit  = 10;
$offset = ($page - 1) * $limit;

// Build WHERE clause
$where_parts = [];
if ($filterProductId > 0)  $where_parts[] = "r.product_id = $filterProductId";
if ($filterStatus !== '')   $where_parts[] = "r.status = '" . mysqli_real_escape_string($conn, $filterStatus) . "'";
if ($filterSearch !== '') {
    $fs = mysqli_real_escape_string($conn, $filterSearch);
    $where_parts[] = "(u.name LIKE '%$fs%' OR u.email LIKE '%$fs%' OR p.name LIKE '%$fs%')";
}

$whereSQL = $where_parts ? ('WHERE ' . implode(' AND ', $where_parts)) : '';

// Total count
$countQuery = mysqli_query($conn, "
    SELECT COUNT(*) AS total_reviews
    FROM reviews r
    LEFT JOIN products p       ON p.id = r.product_id
    LEFT JOIN quigly_table u   ON u.id = r.user_id
    $whereSQL
");
$countRow     = mysqli_fetch_assoc($countQuery);
$totalReviews = (int)($countRow['total_reviews'] ?? 0);
$totalPages   = max(1, (int)ceil($totalReviews / $limit));

// Reviews
$reviewQuery = mysqli_query($conn, "
    SELECT
        r.id,
        r.order_id,
        r.product_id,
        r.user_id,
        r.rating,
        r.review_text,
        r.status,
        r.created_at,
        p.name  AS product_name,
        p.image AS product_image,
        u.name  AS customer_name,
        u.email AS customer_email
    FROM reviews r
    LEFT JOIN products p     ON p.id = r.product_id
    LEFT JOIN quigly_table u ON u.id = r.user_id
    $whereSQL
    ORDER BY r.id DESC
    LIMIT $limit OFFSET $offset
");

// All products for filter dropdown
$allProducts = mysqli_query($conn, "
    SELECT DISTINCT p.id, p.name
    FROM products p
    INNER JOIN reviews r ON r.product_id = p.id
    ORDER BY p.name ASC
");

// Summary stats
$statsRow = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) AS approved,
        SUM(CASE WHEN status='rejected' THEN 1 ELSE 0 END) AS rejected,
        SUM(CASE WHEN status='pending'  THEN 1 ELSE 0 END) AS pending
    FROM reviews
"));

function reviewBadge($status) {
    $s = strtolower(trim((string)$status));
    if ($s === 'approved') return ['class' => 'badge-approved', 'label' => 'Approved'];
    if ($s === 'rejected') return ['class' => 'badge-rejected', 'label' => 'Rejected'];
    return ['class' => 'badge-pending', 'label' => 'Pending'];
}
?>

<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<style>
.reviews-page { padding: 1.5rem 1.5rem 3rem; }

.stats-bar {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 14px;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 18px 20px;
    text-align: center;
    box-shadow: 0 4px 14px rgba(0,0,0,.05);
}

.stat-card .stat-num {
    font-size: 2rem;
    font-weight: 900;
    line-height: 1;
    margin-bottom: 5px;
}

.stat-card .stat-label {
    font-size: .82rem;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .6px;
}

.filter-bar {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 18px;
    padding: 16px 20px;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 14px rgba(0,0,0,.04);
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: flex-end;
}

.filter-bar .filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
    flex: 1;
    min-width: 160px;
}

.filter-bar label {
    font-size: .78rem;
    font-weight: 700;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.filter-bar select,
.filter-bar input {
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 9px 13px;
    font-size: .9rem;
    outline: none;
    transition: .2s;
}

.filter-bar select:focus,
.filter-bar input:focus {
    border-color: #7c3aed;
    box-shadow: 0 0 0 3px rgba(124,58,237,.10);
}

.rev-table-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 18px rgba(0,0,0,.06);
}

.rev-table { width: 100%; border-collapse: collapse; }
.rev-table thead { background: linear-gradient(135deg,rgba(124,58,237,.08),rgba(37,99,235,.06)); }
.rev-table thead th {
    padding: 14px 16px;
    font-size: .78rem;
    font-weight: 800;
    color: #374151;
    text-transform: uppercase;
    letter-spacing: .5px;
    border-bottom: 1px solid #e5e7eb;
}

.rev-table tbody tr { border-bottom: 1px solid #f3f4f6; transition: .15s; }
.rev-table tbody tr:hover { background: rgba(124,58,237,.025); }
.rev-table tbody td { padding: 14px 16px; vertical-align: middle; }

.product-cell { display: flex; align-items: center; gap: 12px; }
.product-thumb {
    width: 48px; height: 48px;
    border-radius: 12px;
    overflow: hidden;
    background: #f3f4f6;
    flex-shrink: 0;
    object-fit: cover;
}

.badge-approved { background: rgba(16,185,129,.12); color: #059669; border: 1px solid rgba(16,185,129,.2); }
.badge-rejected { background: rgba(239,68,68,.12);  color: #dc2626; border: 1px solid rgba(239,68,68,.2); }
.badge-pending  { background: rgba(245,158,11,.12); color: #d97706; border: 1px solid rgba(245,158,11,.2); }

.rev-badge {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 12px; border-radius: 999px;
    font-size: .78rem; font-weight: 800;
}

.stars-sm { color: #f59e0b; font-size: .82rem; }

.action-btns { display: flex; gap: 8px; }
.act-btn {
    padding: 6px 14px; border-radius: 10px;
    font-size: .8rem; font-weight: 700;
    border: none; cursor: pointer;
    transition: .2s;
    text-decoration: none;
    display: inline-flex; align-items: center; gap: 5px;
}
.act-approve { background: rgba(16,185,129,.12); color: #059669; }
.act-approve:hover { background: #10b981; color: #fff; }
.act-reject  { background: rgba(239,68,68,.12);  color: #dc2626; }
.act-reject:hover  { background: #ef4444; color: #fff; }

.pagination-wrap { display: flex; justify-content: center; gap: 8px; margin-top: 1.5rem; flex-wrap: wrap; }
.pag-btn {
    min-width: 40px; height: 40px; padding: 0 14px;
    border-radius: 12px; border: 1px solid #e5e7eb;
    background: #fff; font-weight: 700; font-size: .88rem;
    display: inline-flex; align-items: center; justify-content: center;
    text-decoration: none; color: #374151;
    transition: .2s;
}
.pag-btn.active { background: linear-gradient(135deg,#7c3aed,#2563eb); color: #fff; border-color: transparent; }
.pag-btn:hover:not(.active):not(.disabled) { border-color: #7c3aed; color: #7c3aed; }
.pag-btn.disabled { opacity: .4; pointer-events: none; }

@media(max-width:768px) {
    .stats-bar { grid-template-columns: repeat(2,1fr); }
    .filter-bar { flex-direction: column; }
    .rev-table thead { display: none; }
    .rev-table tbody tr { display: block; padding: 14px; }
    .rev-table tbody td { display: block; padding: 4px 0; border: none; }
    .rev-table tbody td::before { content: attr(data-label); font-weight: 800; font-size: .78rem; color: #9ca3af; display: block; margin-bottom: 3px; }
}
</style>

<div class="reviews-page">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <h3 class="fw-bold mb-1">Customer Reviews</h3>
            <p class="text-muted mb-0">Manage and approve customer feedback across all products.</p>
        </div>
    </div>

    <!-- Stats bar -->
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-num" style="color:#7c3aed;"><?= (int)($statsRow['total'] ?? 0) ?></div>
            <div class="stat-label">Total</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#059669;"><?= (int)($statsRow['approved'] ?? 0) ?></div>
            <div class="stat-label">Approved</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#d97706;"><?= (int)($statsRow['pending'] ?? 0) ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card">
            <div class="stat-num" style="color:#dc2626;"><?= (int)($statsRow['rejected'] ?? 0) ?></div>
            <div class="stat-label">Rejected</div>
        </div>
    </div>

    <!-- Filter bar -->
    <form method="GET" class="filter-bar">
        <div class="filter-group">
            <label>Filter by Product</label>
            <select name="product_id" onchange="this.form.submit()">
                <option value="">— All Products —</option>
                <?php if ($allProducts && mysqli_num_rows($allProducts) > 0):
                    while ($p = mysqli_fetch_assoc($allProducts)): ?>
                    <option value="<?= (int)$p['id'] ?>" <?= ($filterProductId === (int)$p['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($p['name']) ?>
                    </option>
                <?php endwhile; endif; ?>
            </select>
        </div>

        <div class="filter-group">
            <label>Status</label>
            <select name="status" onchange="this.form.submit()">
                <option value="">— All Status —</option>
                <option value="pending"  <?= $filterStatus === 'pending'  ? 'selected' : '' ?>>Pending</option>
                <option value="approved" <?= $filterStatus === 'approved' ? 'selected' : '' ?>>Approved</option>
                <option value="rejected" <?= $filterStatus === 'rejected' ? 'selected' : '' ?>>Rejected</option>
            </select>
        </div>

        <div class="filter-group">
            <label>Search Customer / Product</label>
            <input type="text" name="search" value="<?= htmlspecialchars($filterSearch) ?>" placeholder="Type to search...">
        </div>

        <div style="display:flex;gap:8px;align-self:flex-end;">
            <button type="submit" class="act-btn act-approve px-4" style="height:40px;">
                <i class="fas fa-search"></i> Search
            </button>
            <a href="reviews_list.php" class="act-btn act-reject px-4" style="height:40px;">
                <i class="fas fa-times"></i> Reset
            </a>
        </div>
    </form>

    <?php if ($filterProductId > 0):
        // Show product-level review summary when a product is selected
        $prodSummary = mysqli_query($conn, "
            SELECT p.name, p.image,
                   ROUND(AVG(r.rating),1) AS avg_rating,
                   COUNT(*) AS total,
                   SUM(CASE WHEN r.status='approved' THEN 1 ELSE 0 END) AS approved_ct
            FROM products p
            LEFT JOIN reviews r ON r.product_id = p.id
            WHERE p.id = $filterProductId
            GROUP BY p.id
        ");
        $ps = mysqli_fetch_assoc($prodSummary);
        if ($ps):
            $img = trim($ps['image'] ?? '');
            if ($img && !preg_match('#^(https?://|upload/)#i', $img)) $img = 'upload/' . $img;
    ?>
    <div class="rev-table-card mb-3 p-4 d-flex align-items-center gap-4" style="flex-wrap:wrap;">
        <?php if ($img): ?>
            <img src="../../<?= htmlspecialchars($img) ?>" style="width:68px;height:68px;border-radius:16px;object-fit:cover;border:1px solid #e5e7eb;">
        <?php endif; ?>
        <div>
            <div class="fw-bold fs-5 mb-1"><?= htmlspecialchars($ps['name']) ?></div>
            <div class="d-flex gap-3 flex-wrap" style="font-size:.9rem;color:#6b7280;">
                <span>⭐ Avg rating: <strong style="color:#0f172a;"><?= $ps['avg_rating'] ?: 'N/A' ?></strong></span>
                <span>Total reviews: <strong style="color:#0f172a;"><?= (int)$ps['total'] ?></strong></span>
                <span>Approved: <strong style="color:#059669;"><?= (int)$ps['approved_ct'] ?></strong></span>
            </div>
        </div>
        <div class="ms-auto">
            <span style="font-size:.82rem;font-weight:700;color:#7c3aed;background:rgba(124,58,237,.08);padding:6px 14px;border-radius:999px;border:1px solid rgba(124,58,237,.12);">
                Showing all reviews for this product
            </span>
        </div>
    </div>
    <?php endif; endif; ?>

    <!-- Table -->
    <div class="rev-table-card">
        <div class="table-responsive">
            <table class="rev-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Review Text</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th style="text-align:right;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($reviewQuery && mysqli_num_rows($reviewQuery) > 0):
                        $i = $offset + 1;
                        while ($row = mysqli_fetch_assoc($reviewQuery)):
                            $badge = reviewBadge($row['status']);
                            $img   = trim($row['product_image'] ?? '');
                            if ($img && !preg_match('#^(https?://|upload/)#i', $img)) $img = 'upload/' . $img;
                            $stars = '';
                            for ($s = 1; $s <= 5; $s++) {
                                $stars .= $s <= (int)$row['rating'] ? '★' : '☆';
                            }
                    ?>
                    <tr>
                        <td data-label="#"><?= $i++ ?></td>
                        <td data-label="Product">
                            <div class="product-cell">
                                <?php if ($img): ?>
                                    <img src="../../<?= htmlspecialchars($img) ?>" class="product-thumb" alt="">
                                <?php else: ?>
                                    <div class="product-thumb d-flex align-items-center justify-content-center text-muted"><i class="fas fa-image"></i></div>
                                <?php endif; ?>
                                <div>
                                    <div class="fw-bold" style="font-size:.9rem;"><?= htmlspecialchars($row['product_name'] ?: 'Product') ?></div>
                                    <div class="text-muted" style="font-size:.78rem;">Order #<?= (int)$row['order_id'] ?></div>
                                </div>
                            </div>
                        </td>
                        <td data-label="Customer">
                            <div class="fw-bold" style="font-size:.9rem;"><?= htmlspecialchars($row['customer_name'] ?: 'User') ?></div>
                            <div class="text-muted" style="font-size:.78rem;"><?= htmlspecialchars($row['customer_email'] ?: '') ?></div>
                        </td>
                        <td data-label="Rating">
                            <div class="stars-sm"><?= $stars ?></div>
                            <div style="font-size:.78rem;font-weight:700;color:#374151;"><?= (int)$row['rating'] ?>/5</div>
                        </td>
                        <td data-label="Review" style="max-width:280px;">
                            <div style="font-size:.85rem;color:#475569;line-height:1.5;
                                white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:260px;"
                                title="<?= htmlspecialchars($row['review_text']) ?>">
                                <?= htmlspecialchars($row['review_text']) ?>
                            </div>
                        </td>
                        <td data-label="Status">
                            <span class="rev-badge <?= $badge['class'] ?>"><?= htmlspecialchars($badge['label']) ?></span>
                        </td>
                        <td data-label="Date" style="font-size:.82rem;white-space:nowrap;">
                            <?= !empty($row['created_at']) ? date("d M Y, h:i A", strtotime($row['created_at'])) : '' ?>
                        </td>
                        <td data-label="Action" style="text-align:right;">
                            <div class="action-btns justify-content-end">
                                <?php if (strtolower($row['status']) !== 'approved'): ?>
                                    <a href="../actions/review_action.php?id=<?= (int)$row['id'] ?>&action=approve&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                       class="act-btn act-approve"><i class="fas fa-check"></i> Approve</a>
                                <?php endif; ?>
                                <?php if (strtolower($row['status']) !== 'rejected'): ?>
                                    <a href="../actions/review_action.php?id=<?= (int)$row['id'] ?>&action=reject&redirect=<?= urlencode($_SERVER['REQUEST_URI']) ?>"
                                       class="act-btn act-reject"><i class="fas fa-times"></i> Reject</a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="8" style="text-align:center;padding:2.5rem;color:#9ca3af;">
                        <i class="fas fa-comment-slash" style="font-size:2rem;display:block;margin-bottom:10px;"></i>
                        No reviews found.
                    </td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="pagination-wrap">
        <?php
        $baseUrl = '?' . http_build_query(array_filter([
            'product_id' => $filterProductId ?: null,
            'status'     => $filterStatus ?: null,
            'search'     => $filterSearch ?: null,
        ]));
        $sep = $baseUrl === '?' ? '' : '&';
        ?>
        <a class="pag-btn <?= $page <= 1 ? 'disabled' : '' ?>"
           href="<?= $page > 1 ? $baseUrl . $sep . 'page=' . ($page - 1) : '#' ?>">
            <i class="fa-solid fa-angle-left"></i>
        </a>
        <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a class="pag-btn <?= $p === $page ? 'active' : '' ?>"
               href="<?= $baseUrl . $sep . 'page=' . $p ?>">
                <?= $p ?>
            </a>
        <?php endfor; ?>
        <a class="pag-btn <?= $page >= $totalPages ? 'disabled' : '' ?>"
           href="<?= $page < $totalPages ? $baseUrl . $sep . 'page=' . ($page + 1) : '#' ?>">
            <i class="fa-solid fa-angle-right"></i>
        </a>
    </div>
    <?php endif; ?>

</div>

<?php include("../includes/footer.php"); ?>
