<?php
// Fetch current user's orders
$userId = (int)($data['id'] ?? 0);
$ordersQuery = "";
$userOrders = [];
if ($userId > 0) {
    $oResult = mysqli_query($conn, "
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
            COALESCE(MAX(p.name), 'Product') AS product_name,
            COALESCE(MAX(p.image), '') AS product_image,
            COALESCE(MAX(oi.product_id), 0) AS product_id,
            COALESCE(MAX(r.id), 0) AS already_reviewed
        FROM orders o
        LEFT JOIN order_items oi ON oi.order_id = o.id
        LEFT JOIN products p ON p.id = oi.product_id
        LEFT JOIN reviews r ON r.order_id = o.id AND r.user_id = $userId
        WHERE o.user_id = $userId
        GROUP BY o.id
        ORDER BY o.id DESC
    ");
    while ($oResult && ($oRow = mysqli_fetch_assoc($oResult))) {
        $img = trim($oRow['product_image'] ?? '');
        if ($img === '') $oRow['product_image'] = 'assets/images/no-image.png';
        elseif (!preg_match('#^(https?://|upload/)#i', $img)) $oRow['product_image'] = 'upload/' . $img;
        $userOrders[] = $oRow;
    }
}
?>
<section id="orders" class="content-section" style="display:none;">
<style>
/* ── Orders Section ── */
#orders { padding: 24px 20px 48px; }

.orders-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 28px;
}
.orders-page-title {
    font-size: 22px;
    font-weight: 800;
    color: #1e293b;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}
body.dark-mode .orders-page-title { color: #f1f5f9; }
.orders-page-title i { color: #7c3aed; font-size: 20px; }

.orders-count-pill {
    background: #f1f5f9;
    color: #64748b;
    font-size: 12px;
    font-weight: 700;
    padding: 4px 12px;
    border-radius: 999px;
    border: 1px solid #e2e8f0;
}
body.dark-mode .orders-count-pill { background: #1e293b; color: #94a3b8; border-color: #334155; }

/* ── Order Card ── */
.order-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 20px;
    padding: 20px;
    margin-bottom: 16px;
    transition: box-shadow .2s, transform .2s, opacity .3s;
    position: relative;
    overflow: hidden;
}
.order-card::before {
    content: '';
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, #7c3aed, #4f46e5);
    opacity: 0;
    transition: opacity .2s;
}
.order-card:hover { box-shadow: 0 8px 32px rgba(124,58,237,.1); transform: translateY(-2px); }
.order-card:hover::before { opacity: 1; }
body.dark-mode .order-card { background: #1e293b; border-color: #334155; }

.order-card-top {
    display: flex;
    align-items: center;
    gap: 16px;
    flex-wrap: wrap;
}
.order-product-img {
    width: 72px;
    height: 72px;
    border-radius: 14px;
    object-fit: cover;
    background: #f8fafc;
    border: 1px solid #e5e7eb;
    flex-shrink: 0;
}
body.dark-mode .order-product-img { background: #0f172a; border-color: #334155; }

.order-info { flex: 1; min-width: 0; }
.order-product-name {
    font-size: 15px;
    font-weight: 700;
    color: #1e293b;
    margin: 0 0 4px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
body.dark-mode .order-product-name { color: #f1f5f9; }
.order-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 14px;
    align-items: center;
}
.order-meta-item {
    font-size: 12px;
    color: #64748b;
    display: flex;
    align-items: center;
    gap: 5px;
}
body.dark-mode .order-meta-item { color: #94a3b8; }
.order-meta-item i { font-size: 11px; color: #7c3aed; }

/* Status Badge */
.order-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 5px 12px;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    white-space: nowrap;
}
.status-pending    { background: #fef9c3; color: #854d0e; }
.status-processing { background: #dbeafe; color: #1d4ed8; }
.status-shipped    { background: #e0f2fe; color: #0369a1; }
.status-delivered  { background: #dcfce7; color: #15803d; }
.status-cancelled  { background: #fee2e2; color: #b91c1c; }
.status-default    { background: #f1f5f9; color: #475569; }

body.dark-mode .status-pending    { background: #422006; color: #fde68a; }
body.dark-mode .status-processing { background: #1e3a5f; color: #93c5fd; }
body.dark-mode .status-shipped    { background: #0c2d3f; color: #7dd3fc; }
body.dark-mode .status-delivered  { background: #14532d; color: #86efac; }
body.dark-mode .status-cancelled  { background: #450a0a; color: #fca5a5; }
body.dark-mode .status-default    { background: #1e293b; color: #94a3b8; }

/* ── Order Actions Row ── */
.order-actions-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    margin-top: 16px;
    padding-top: 14px;
    border-top: 1px solid #f1f5f9;
}
body.dark-mode .order-actions-row { border-top-color: #334155; }

.order-total-display {
    font-size: 17px;
    font-weight: 800;
    color: #7c3aed;
}
.order-total-label {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 500;
}

.order-btn-group {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.order-btn {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 13px;
    font-weight: 700;
    border: none;
    cursor: pointer;
    text-decoration: none;
    transition: all .2s;
    white-space: nowrap;
}
.order-btn-cancel {
    background: #fff;
    color: #ef4444;
    border: 1.5px solid #fecaca;
}
.order-btn-cancel:hover {
    background: #fee2e2;
    border-color: #ef4444;
    color: #b91c1c;
}
body.dark-mode .order-btn-cancel { background: #1e293b; border-color: #7f1d1d; color: #fca5a5; }
body.dark-mode .order-btn-cancel:hover { background: #450a0a; }

.order-btn-return {
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    border: none;
}
.order-btn-return:hover {
    background: linear-gradient(135deg, #6d28d9, #4338ca);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(124,58,237,.35);
    color: #fff;
}

.order-btn-track {
    background: #f8fafc;
    color: #475569;
    border: 1.5px solid #e2e8f0;
}
.order-btn-track:hover {
    background: #e2e8f0;
    color: #1e293b;
}
body.dark-mode .order-btn-track { background: #0f172a; border-color: #334155; color: #94a3b8; }
body.dark-mode .order-btn-track:hover { background: #334155; color: #f1f5f9; }

/* ── Empty state ── */
.orders-empty {
    text-align: center;
    padding: 80px 20px;
    color: #94a3b8;
}
.orders-empty-icon {
    width: 96px;
    height: 96px;
    margin: 0 auto 24px;
    background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #cbd5e1;
}
body.dark-mode .orders-empty-icon { background: #1e293b; color: #334155; }
.orders-empty h4 { font-weight: 700; color: #64748b; font-size: 18px; margin-bottom: 6px; }
body.dark-mode .orders-empty h4 { color: #94a3b8; }
.orders-empty p { font-size: 14px; margin-bottom: 24px; }

/* Status tracker bar */
.order-tracker {
    display: flex;
    align-items: center;
    gap: 0;
    margin: 14px 0 0;
    overflow-x: auto;
    padding-bottom: 2px;
}
.tracker-step {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex: 1;
    min-width: 60px;
}
.tracker-dot {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: #e2e8f0;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 9px;
    color: #fff;
    font-weight: 800;
    flex-shrink: 0;
    position: relative;
    z-index: 1;
}
.tracker-dot.done { background: linear-gradient(135deg, #7c3aed, #4f46e5); }
.tracker-dot.active { background: linear-gradient(135deg, #7c3aed, #4f46e5); box-shadow: 0 0 0 4px rgba(124,58,237,.2); }
.tracker-label { font-size: 10px; color: #94a3b8; font-weight: 600; text-align: center; line-height: 1.2; }
.tracker-label.done, .tracker-label.active { color: #7c3aed; }
body.dark-mode .tracker-label.done, body.dark-mode .tracker-label.active { color: #c4b5fd; }
.tracker-line {
    flex: 1;
    height: 2px;
    background: #e2e8f0;
    align-self: center;
    margin: 0 -1px;
    position: relative;
    top: -8px;
}
.tracker-line.done { background: linear-gradient(90deg, #7c3aed, #4f46e5); }
body.dark-mode .tracker-line { background: #334155; }
body.dark-mode .tracker-dot { background: #334155; }

@media (max-width: 576px) {
    #orders { padding: 16px 12px 36px; }
    .order-card { padding: 14px; }
    .order-product-img { width: 56px; height: 56px; }
    .order-btn { padding: 7px 12px; font-size: 12px; }
}

/* ── Review Modal ── */
.order-review-modal-overlay {
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(6px);
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 16px;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s, visibility .25s;
}
.order-review-modal-overlay.open {
    opacity: 1;
    visibility: visible;
}
.order-review-modal {
    background: #fff;
    border-radius: 24px;
    padding: 32px 28px 28px;
    width: 100%;
    max-width: 460px;
    box-shadow: 0 24px 64px rgba(15,23,42,.2);
    transform: translateY(18px) scale(.97);
    transition: transform .25s;
    position: relative;
}
.order-review-modal-overlay.open .order-review-modal {
    transform: translateY(0) scale(1);
}
body.dark-mode .order-review-modal { background: #1e293b; }
.review-modal-close {
    position: absolute;
    top: 14px; right: 14px;
    width: 32px; height: 32px;
    border-radius: 50%;
    border: none;
    background: #f1f5f9;
    color: #64748b;
    font-size: 16px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
}
body.dark-mode .review-modal-close { background: #0f172a; color: #94a3b8; }
.review-modal-close:hover { background: #e2e8f0; }
.review-modal-title {
    font-size: 17px;
    font-weight: 800;
    color: #0f172a;
    margin-bottom: 4px;
}
body.dark-mode .review-modal-title { color: #f1f5f9; }
.review-modal-sub {
    font-size: 13px;
    color: #64748b;
    margin-bottom: 22px;
}
.star-picker {
    display: flex;
    gap: 8px;
    margin-bottom: 18px;
    flex-direction: row-reverse;
    justify-content: flex-end;
}
.star-picker input { display: none; }
.star-picker label {
    font-size: 2.2rem;
    color: #cbd5e1;
    cursor: pointer;
    transition: color .15s, transform .15s;
    line-height: 1;
}
.star-picker label:hover,
.star-picker label:hover ~ label,
.star-picker input:checked ~ label {
    color: #f59e0b;
    transform: scale(1.12);
}
.review-modal-textarea {
    width: 100%;
    min-height: 100px;
    border-radius: 14px;
    border: 1.5px solid #e2e8f0;
    padding: 13px 16px;
    font-size: 14px;
    font-family: inherit;
    resize: vertical;
    color: #0f172a;
    background: #f8fafc;
    margin-bottom: 18px;
    transition: border-color .2s;
    display: block;
    box-sizing: border-box;
}
.review-modal-textarea:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124,58,237,.1);
}
body.dark-mode .review-modal-textarea { background: #0f172a; color: #f1f5f9; border-color: #334155; }
.review-modal-actions { display: flex; gap: 10px; }
.review-modal-submit {
    flex: 1;
    height: 48px;
    border: none;
    border-radius: 14px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    font-weight: 700;
    font-size: 14px;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center; gap: 8px;
    transition: .2s;
    box-shadow: 0 8px 24px rgba(124,58,237,.3);
}
.review-modal-submit:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(124,58,237,.4); }
.review-modal-submit:disabled { opacity: .6; cursor: not-allowed; transform: none; }
.review-modal-msg {
    margin-top: 14px;
    padding: 11px 16px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 13px;
    display: none;
}
.review-modal-msg.success { background: rgba(16,185,129,.1); color: #059669; border: 1px solid rgba(16,185,129,.2); }
.review-modal-msg.error   { background: rgba(239,68,68,.1);  color: #dc2626; border: 1px solid rgba(239,68,68,.2); }

/* ── Delivered Action Buttons ── */
.order-btn-review {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff;
    border: none;
}
.order-btn-review:hover {
    background: linear-gradient(135deg, #d97706, #b45309);
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(245,158,11,.35);
    color: #fff;
}
.order-btn-reviewed {
    background: #f1f5f9;
    color: #64748b;
    border: 1.5px solid #e2e8f0;
    cursor: default;
}
body.dark-mode .order-btn-reviewed { background: #0f172a; border-color: #334155; color: #64748b; }
.order-btn-invoice {
    background: #f0fdf4;
    color: #16a34a;
    border: 1.5px solid #bbf7d0;
}
.order-btn-invoice:hover {
    background: #dcfce7;
    border-color: #4ade80;
    color: #15803d;
}

/* ── Pagination ── */
.orders-pagination {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    margin-top: 28px;
    flex-wrap: wrap;
}
.orders-pag-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    border-radius: 10px;
    border: 1.5px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-size: 13px;
    font-weight: 700;
    cursor: pointer;
    transition: all .2s;
    user-select: none;
}
.orders-pag-btn:hover:not(:disabled):not(.active) {
    background: #f1f5f9;
    border-color: #7c3aed;
    color: #7c3aed;
}
.orders-pag-btn.active {
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    border-color: transparent;
    color: #fff;
    box-shadow: 0 4px 14px rgba(124,58,237,.35);
}
.orders-pag-btn:disabled {
    opacity: .35;
    cursor: not-allowed;
}
body.dark-mode .orders-pag-btn {
    background: #1e293b;
    border-color: #334155;
    color: #94a3b8;
}
body.dark-mode .orders-pag-btn:hover:not(:disabled):not(.active) {
    background: #334155;
    border-color: #7c3aed;
    color: #c4b5fd;
}
body.dark-mode .orders-pag-btn.active {
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    border-color: transparent;
    color: #fff;
}
.orders-pag-info {
    font-size: 12px;
    color: #94a3b8;
    font-weight: 600;
    padding: 0 4px;
}

/* ── Page Loader Overlay ── */
.orders-page-loader {
    position: relative;
    min-height: 120px;
}
.orders-loader-overlay {
    position: absolute;
    inset: 0;
    background: rgba(255,255,255,.75);
    backdrop-filter: blur(3px);
    border-radius: 20px;
    z-index: 10;
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s;
}
body.dark-mode .orders-loader-overlay { background: rgba(15,23,42,.75); }
.orders-loader-overlay.show {
    opacity: 1;
    pointer-events: all;
}
.orders-loader-spinner {
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 3.5px solid #e2e8f0;
    border-top-color: #7c3aed;
    animation: ordersSpinAnim .7s linear infinite;
}
@keyframes ordersSpinAnim {
    to { transform: rotate(360deg); }
}

/* Card fade-in animation */
@keyframes orderCardIn {
    from { opacity: 0; transform: translateY(14px); }
    to   { opacity: 1; transform: translateY(0); }
}
.order-card.animate-in {
    animation: orderCardIn .32s ease both;
}
</style>

<div class="orders-page-header">
    <h2 class="orders-page-title">
        <i class="fas fa-box-open"></i>
        My Orders
    </h2>
    <span class="orders-count-pill" id="ordersCountPill"><?= count($userOrders) ?> order<?= count($userOrders) !== 1 ? 's' : '' ?></span>
</div>

<?php if (empty($userOrders)): ?>
<div class="orders-empty">
    <div class="orders-empty-icon"><i class="fas fa-shopping-bag"></i></div>
    <h4>No orders yet</h4>
    <p>Your placed orders will appear here.</p>
    <button class="btn btn-dark rounded-pill px-4" onclick="showSection('products')">
        <i class="fas fa-shopping-cart me-2"></i>Start Shopping
    </button>
</div>
<?php else: ?>

<!-- Orders list container (with loader overlay wrapper) -->
<div class="orders-page-loader" id="ordersPageLoader">
    <div class="orders-loader-overlay" id="ordersLoaderOverlay">
        <div class="orders-loader-spinner"></div>
    </div>
    <div id="ordersListContainer">
        <!-- Cards injected by JS -->
    </div>
</div>

<!-- Pagination -->
<div class="orders-pagination" id="ordersPagination"></div>

<!-- Hidden data store: all orders as JSON -->
<script id="ordersDataStore" type="application/json">
<?php
$ordersForJs = [];
foreach ($userOrders as $ord) {
    $rawStatus   = strtolower(trim($ord['status'] ?? ''));
    $statusLabel = ucfirst($ord['status'] ?? 'Pending');
    $statusClass = 'status-default';
    $statusIcon  = 'fa-circle-dot';
    if (in_array($rawStatus, ['pending','processing'])) { $statusClass = 'status-pending';   $statusIcon = 'fa-clock'; }
    if ($rawStatus === 'shipped')   { $statusClass = 'status-shipped';    $statusIcon = 'fa-truck'; }
    if ($rawStatus === 'delivered') { $statusClass = 'status-delivered';  $statusIcon = 'fa-circle-check'; }
    if (in_array($rawStatus, ['cancelled','canceled'])) { $statusClass = 'status-cancelled'; $statusIcon = 'fa-ban'; }

    $trackerActive = 0;
    if ($rawStatus === 'processing') $trackerActive = 1;
    if ($rawStatus === 'shipped')    $trackerActive = 2;
    if ($rawStatus === 'delivered')  $trackerActive = 3;

    $ordersForJs[] = [
        'order_id'        => (int)$ord['order_id'],
        'product_name'    => $ord['product_name'],
        'product_image'   => $ord['product_image'],
        'product_id'      => (int)($ord['product_id'] ?? 0),
        'quantity'        => (int)$ord['quantity'],
        'total'           => number_format((float)$ord['total'], 2),
        'status'          => $ord['status'],
        'rawStatus'       => $rawStatus,
        'statusLabel'     => $statusLabel,
        'statusClass'     => $statusClass,
        'statusIcon'      => $statusIcon,
        'trackerActive'   => $trackerActive,
        'isCancelled'     => in_array($rawStatus, ['cancelled','canceled']),
        'isDelivered'     => ($rawStatus === 'delivered'),
        'canCancel'       => in_array($rawStatus, ['pending','processing']),
        'dateStr'         => !empty($ord['created_at']) ? date('d M Y', strtotime($ord['created_at'])) : '—',
        'payMethod'       => strtoupper($ord['payment_method'] ?? 'N/A'),
        'payStatus'       => ucfirst($ord['payment_status'] ?? ''),
        'alreadyReviewed' => (int)($ord['already_reviewed'] ?? 0) > 0,
        'customer_address'=> $ord['customer_address'] ?? '',
    ];
}
echo json_encode($ordersForJs, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE);
?>
</script>

<?php endif; ?>

<!-- ══ REVIEW MODAL ══ -->
<div class="order-review-modal-overlay" id="orderReviewModal" onclick="if(event.target===this)closeReviewModal()">
    <div class="order-review-modal">
        <button class="review-modal-close" onclick="closeReviewModal()"><i class="fas fa-times"></i></button>
        <div class="review-modal-title"><i class="fas fa-star" style="color:#f59e0b;margin-right:8px;"></i>Rate Your Order</div>
        <div class="review-modal-sub" id="reviewModalSub">Share your experience with this product</div>

        <div class="star-picker" id="starPicker">
            <input type="radio" name="modalRating" id="mstar5" value="5"><label for="mstar5" title="5 stars">&#9733;</label>
            <input type="radio" name="modalRating" id="mstar4" value="4"><label for="mstar4" title="4 stars">&#9733;</label>
            <input type="radio" name="modalRating" id="mstar3" value="3"><label for="mstar3" title="3 stars">&#9733;</label>
            <input type="radio" name="modalRating" id="mstar2" value="2"><label for="mstar2" title="2 stars">&#9733;</label>
            <input type="radio" name="modalRating" id="mstar1" value="1"><label for="mstar1" title="1 star">&#9733;</label>
        </div>

        <textarea class="review-modal-textarea" id="reviewModalText" placeholder="Tell others what you liked or didn't like... (optional)"></textarea>

        <div class="review-modal-actions">
            <button class="review-modal-submit" id="reviewModalSubmitBtn" onclick="submitOrderReview()">
                <i class="fas fa-paper-plane"></i> Submit Review
            </button>
        </div>
        <div class="review-modal-msg" id="reviewModalMsg"></div>
    </div>
</div>

<!-- ══ TRACKING MODAL ══ -->
<style>
.track-modal-overlay {
    position: fixed; inset: 0;
    background: rgba(15,23,42,.55);
    backdrop-filter: blur(8px);
    z-index: 9000;
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
    opacity: 0; visibility: hidden;
    transition: opacity .25s, visibility .25s;
}
.track-modal-overlay.open { opacity: 1; visibility: visible; }
.track-modal {
    background: #fff; border-radius: 24px; padding: 32px 28px 28px;
    max-width: 420px; width: 100%;
    box-shadow: 0 24px 64px rgba(15,23,42,.22);
    transform: translateY(18px) scale(.97);
    transition: transform .25s;
    position: relative;
}
.track-modal-overlay.open .track-modal { transform: translateY(0) scale(1); }
body.dark-mode .track-modal { background: #1e293b; }
.track-modal-close {
    position: absolute; top: 14px; right: 14px;
    width: 32px; height: 32px; border-radius: 50%;
    border: none; background: #f1f5f9; color: #64748b;
    font-size: 15px; cursor: pointer;
    display: flex; align-items: center; justify-content: center; transition: .2s;
}
.track-modal-close:hover { background: #e2e8f0; color: #1e293b; }
body.dark-mode .track-modal-close { background: #0f172a; color: #94a3b8; }
.track-modal-title {
    font-size: 18px; font-weight: 800; color: #1e293b;
    display: flex; align-items: center; gap: 10px; margin-bottom: 6px;
}
body.dark-mode .track-modal-title { color: #f1f5f9; }
.track-modal-title i { color: #7c3aed; }
.track-modal-sub { font-size: 13px; color: #64748b; margin-bottom: 24px; }
body.dark-mode .track-modal-sub { color: #94a3b8; }
.track-steps { display: flex; flex-direction: column; gap: 0; }
.track-step { display: flex; gap: 14px; align-items: flex-start; position: relative; }
.track-step-icon {
    width: 40px; height: 40px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; flex-shrink: 0; transition: .2s;
}
.track-step-icon.done { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; box-shadow: 0 4px 14px rgba(124,58,237,.3); }
.track-step-icon.active { background: linear-gradient(135deg, #7c3aed, #4f46e5); color: #fff; box-shadow: 0 0 0 5px rgba(124,58,237,.18); }
.track-step-icon.pending { background: #f1f5f9; color: #cbd5e1; }
body.dark-mode .track-step-icon.pending { background: #334155; color: #475569; }
.track-step-connector { width: 2px; height: 28px; background: #e2e8f0; margin: 2px 0 2px 19px; }
.track-step-connector.done { background: linear-gradient(180deg, #7c3aed, #4f46e5); }
body.dark-mode .track-step-connector { background: #334155; }
.track-step-body { padding: 9px 0 0; flex: 1; }
.track-step-name { font-size: 14px; font-weight: 700; color: #1e293b; }
body.dark-mode .track-step-name { color: #f1f5f9; }
.track-step-name.done, .track-step-name.active { color: #7c3aed; }
body.dark-mode .track-step-name.done, body.dark-mode .track-step-name.active { color: #c4b5fd; }
.track-step-desc { font-size: 12px; color: #94a3b8; margin-top: 2px; padding-bottom: 12px; }
.track-step-date { font-size: 11px; color: #7c3aed; font-weight: 700; margin-top: 2px; }
.track-status-banner { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 999px; font-size: 13px; font-weight: 700; margin-bottom: 20px; }
</style>

<div class="track-modal-overlay" id="trackingModal" onclick="if(event.target===this)closeTrackingModal()">
    <div class="track-modal">
        <button class="track-modal-close" onclick="closeTrackingModal()"><i class="fas fa-times"></i></button>
        <div class="track-modal-title"><i class="fas fa-map-marker-alt"></i> Order Tracking</div>
        <div class="track-modal-sub" id="trackModalSub">Track your order status</div>
        <div id="trackStatusBanner"></div>
        <div class="track-steps" id="trackSteps"></div>
    </div>
</div>

<!-- Page-level toast -->
<div class="page-toast" id="pageToast"></div>

<script>
(function(){
    /* ══ PAGINATION ENGINE ══ */
    const ORDERS_PER_PAGE = 5;
    const TRACKER_STEPS   = ['Ordered','Processing','Shipped','Delivered'];

    let allOrders    = [];
    let currentPage  = 1;

    const dataEl = document.getElementById('ordersDataStore');
    if (dataEl) {
        try { allOrders = JSON.parse(dataEl.textContent || '[]'); } catch(e) { allOrders = []; }
    }

    const totalPages = () => Math.max(1, Math.ceil(allOrders.length / ORDERS_PER_PAGE));

    function esc(str) {
        return String(str ?? '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;')
            .replace(/"/g,'&quot;').replace(/'/g,'&#039;');
    }

    function buildTrackerHTML(trackerActive, isCancelled) {
        if (isCancelled) return '';
        let html = '<div class="order-tracker mt-3">';
        TRACKER_STEPS.forEach(function(stepName, si) {
            const isDone   = si < trackerActive;
            const isActive = si === trackerActive;
            const dotClass = isActive ? 'active' : (isDone ? 'done' : '');
            const lblClass = (isDone || isActive) ? (isDone ? 'done' : 'active') : '';
            if (si > 0) {
                html += '<div class="tracker-line' + (isDone ? ' done' : '') + '"></div>';
            }
            html += '<div class="tracker-step">';
            html += '<div class="tracker-dot ' + dotClass + '">';
            if (isDone || isActive) html += '<i class="fas fa-check" style="font-size:8px;"></i>';
            html += '</div>';
            html += '<span class="tracker-label ' + lblClass + '">' + esc(stepName) + '</span>';
            html += '</div>';
        });
        html += '</div>';
        return html;
    }

    function buildCardHTML(ord) {
        const addrEsc = esc(ord.customer_address ?? '');
        const nameEsc = esc(ord.product_name ?? '');

        let actionsHTML = '';

        if (ord.canCancel) {
            actionsHTML += '<button class="order-btn order-btn-cancel" onclick="cancelOrder(' + ord.order_id + ')"><i class="fas fa-times-circle"></i> Cancel</button>';
        }
        if (!ord.isCancelled) {
            actionsHTML += '<button class="order-btn order-btn-track" onclick="openTrackingModal(' + ord.order_id + ', \'' + esc(ord.statusLabel) + '\', \'' + esc(ord.rawStatus) + '\', \'' + esc(ord.dateStr) + '\')" title="Track order"><i class="fas fa-map-marker-alt"></i> Track</button>';
        }
        if (ord.isDelivered) {
            actionsHTML += '<a href="invoice.php?id=' + ord.order_id + '" target="_blank" class="order-btn order-btn-invoice"><i class="fas fa-file-invoice"></i> Invoice</a>';
            if (ord.alreadyReviewed) {
                actionsHTML += '<button class="order-btn order-btn-reviewed" disabled><i class="fas fa-check-circle"></i> Reviewed</button>';
            } else {
                actionsHTML += '<button class="order-btn order-btn-review" onclick="openReviewModal(' + ord.order_id + ', ' + ord.product_id + ', \'' + nameEsc + '\')"><i class="fas fa-star"></i> Write Review</button>';
            }
            actionsHTML += '<button class="order-btn order-btn-return" onclick="openReturnPage(' + ord.order_id + ', \'' + nameEsc + '\', \'' + addrEsc + '\')"><i class="fas fa-rotate-left"></i> Return / Refund</button>';
        }

        const qty = ord.quantity > 1 ? ' +' + (ord.quantity - 1) + ' more' : '';

        return `
<div class="order-card animate-in">
    <div class="order-card-top">
        <img src="${esc(ord.product_image)}" class="order-product-img" alt="${nameEsc}" onerror="this.src='assets/images/no-image.png'">
        <div class="order-info">
            <p class="order-product-name">${nameEsc}${esc(qty)}</p>
            <div class="order-meta">
                <span class="order-meta-item"><i class="fas fa-hashtag"></i>Order #${ord.order_id}</span>
                <span class="order-meta-item"><i class="fas fa-calendar"></i>${esc(ord.dateStr)}</span>
                <span class="order-meta-item"><i class="fas fa-credit-card"></i>${esc(ord.payMethod)}</span>
                ${ord.payStatus ? '<span class="order-meta-item"><i class="fas fa-check-circle"></i>' + esc(ord.payStatus) + '</span>' : ''}
            </div>
        </div>
        <span class="order-status-badge ${esc(ord.statusClass)}">
            <i class="fas ${esc(ord.statusIcon)}"></i>
            ${esc(ord.statusLabel)}
        </span>
    </div>
    ${buildTrackerHTML(ord.trackerActive, ord.isCancelled)}
    <div class="order-actions-row">
        <div>
            <div class="order-total-label">Order Total</div>
            <div class="order-total-display">₹${esc(ord.total)}</div>
        </div>
        <div class="order-btn-group">${actionsHTML}</div>
    </div>
</div>`;
    }

    function showLoader(show) {
        const el = document.getElementById('ordersLoaderOverlay');
        if (el) el.classList.toggle('show', show);
    }

    function renderPage(page, animate) {
        const container = document.getElementById('ordersListContainer');
        if (!container) return;

        showLoader(true);

        setTimeout(function() {
            const start = (page - 1) * ORDERS_PER_PAGE;
            const slice = allOrders.slice(start, start + ORDERS_PER_PAGE);

            container.innerHTML = slice.map(buildCardHTML).join('');

            // Stagger animation
            if (animate !== false) {
                container.querySelectorAll('.order-card').forEach(function(card, i) {
                    card.style.animationDelay = (i * 60) + 'ms';
                });
            }

            renderPagination(page);
            showLoader(false);

            // Scroll to top of orders section
            const section = document.getElementById('orders');
            if (section) {
                section.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 340);
    }

    function renderPagination(page) {
        const pagEl = document.getElementById('ordersPagination');
        if (!pagEl) return;
        const tp = totalPages();
        if (tp <= 1) { pagEl.innerHTML = ''; return; }

        let html = '';

        // Prev button
        html += '<button class="orders-pag-btn" ' + (page <= 1 ? 'disabled' : '') + ' onclick="ordersGoPage(' + (page - 1) + ')" title="Previous page">'
              + '<i class="fas fa-chevron-left" style="font-size:11px;"></i></button>';

        // Page numbers (show max 5 buttons with ellipsis)
        const range = pageRange(page, tp);
        range.forEach(function(p) {
            if (p === '...') {
                html += '<span class="orders-pag-info">…</span>';
            } else {
                html += '<button class="orders-pag-btn' + (p === page ? ' active' : '') + '" onclick="ordersGoPage(' + p + ')">' + p + '</button>';
            }
        });

        // Next button
        html += '<button class="orders-pag-btn" ' + (page >= tp ? 'disabled' : '') + ' onclick="ordersGoPage(' + (page + 1) + ')" title="Next page">'
              + '<i class="fas fa-chevron-right" style="font-size:11px;"></i></button>';

        // Page info
        const start = (page - 1) * ORDERS_PER_PAGE + 1;
        const end   = Math.min(page * ORDERS_PER_PAGE, allOrders.length);
        html += '<span class="orders-pag-info" style="margin-left:4px;">' + start + '–' + end + ' of ' + allOrders.length + '</span>';

        pagEl.innerHTML = html;
    }

    function pageRange(current, total) {
        // Always show first, last, current ±1
        const delta = 1;
        const range = [];
        const rangeWithDots = [];
        let l;

        for (let i = 1; i <= total; i++) {
            if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
                range.push(i);
            }
        }

        range.forEach(function(i) {
            if (l) {
                if (i - l === 2) { rangeWithDots.push(l + 1); }
                else if (i - l !== 1) { rangeWithDots.push('...'); }
            }
            rangeWithDots.push(i);
            l = i;
        });

        return rangeWithDots;
    }

    window.ordersGoPage = function(page) {
        const tp = totalPages();
        if (page < 1 || page > tp) return;
        currentPage = page;
        renderPage(page, true);
    };

    // Initial render
    if (allOrders.length > 0) {
        renderPage(1, false);
    }

    /* ══ REVIEW MODAL ══ */
    let _reviewOrderId = 0;
    let _reviewProductId = 0;

    window.openReviewModal = function(orderId, productId, productName) {
        _reviewOrderId   = orderId;
        _reviewProductId = productId;
        document.querySelectorAll('input[name="modalRating"]').forEach(r => r.checked = false);
        document.getElementById('reviewModalText').value = '';
        const msg = document.getElementById('reviewModalMsg');
        msg.style.display = 'none';
        msg.className = 'review-modal-msg';
        const btn = document.getElementById('reviewModalSubmitBtn');
        btn.disabled = false;
        btn.style.display = '';
        btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
        document.getElementById('reviewModalSub').textContent = 'Review: ' + (productName || 'your order');
        document.getElementById('starPicker').style.pointerEvents = 'auto';
        document.getElementById('reviewModalText').disabled = false;
        document.getElementById('orderReviewModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeReviewModal = function() {
        document.getElementById('orderReviewModal').classList.remove('open');
        document.body.style.overflow = '';
    };

    window.submitOrderReview = function() {
        const ratingInput = document.querySelector('input[name="modalRating"]:checked');
        const rating = ratingInput ? parseInt(ratingInput.value) : 0;
        const reviewText = document.getElementById('reviewModalText').value.trim();
        const msg = document.getElementById('reviewModalMsg');
        const btn = document.getElementById('reviewModalSubmitBtn');

        if (!rating) {
            msg.className = 'review-modal-msg error';
            msg.textContent = '⚠️ Please select a star rating first.';
            msg.style.display = 'block';
            return;
        }

        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
        msg.style.display = 'none';

        const fd = new FormData();
        fd.append('order_id',    _reviewOrderId);
        fd.append('product_id',  _reviewProductId);
        fd.append('rating',      rating);
        fd.append('review_text', reviewText || 'Good product.');

        fetch('admin/actions/submit_review.php', { method: 'POST', body: fd })
            .then(r => r.text())
            .then(resp => {
                const ok = resp.includes('review_success') || resp.includes('already_reviewed') || resp === '' || resp.length < 500;
                if (resp.includes('already_reviewed')) {
                    msg.className = 'review-modal-msg success';
                    msg.textContent = '✓ You have already reviewed this order.';
                } else if (ok) {
                    msg.className = 'review-modal-msg success';
                    msg.textContent = '✓ Thank you! Your review has been submitted for approval.';
                    document.getElementById('starPicker').style.pointerEvents = 'none';
                    document.getElementById('reviewModalText').disabled = true;
                    btn.style.display = 'none';
                    setTimeout(() => { location.reload(); }, 1800);
                } else {
                    throw new Error('failed');
                }
                msg.style.display = 'block';
            })
            .catch(() => {
                msg.className = 'review-modal-msg error';
                msg.textContent = '✗ Could not submit review. Please try again.';
                msg.style.display = 'block';
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-paper-plane"></i> Submit Review';
            });
    };

    /* ══ TRACKING MODAL ══ */
    const TRACK_STEPS = [
        { key: 'ordered',    label: 'Order Placed',  desc: 'Your order has been confirmed',   icon: 'fa-check' },
        { key: 'processing', label: 'Processing',    desc: 'Your order is being prepared',     icon: 'fa-cogs' },
        { key: 'shipped',    label: 'Shipped',        desc: 'Your order is on its way',         icon: 'fa-truck' },
        { key: 'delivered',  label: 'Delivered',      desc: 'Your order has been delivered',    icon: 'fa-home' },
    ];
    const STATUS_STEP = { pending: 0, processing: 1, shipped: 2, delivered: 3 };

    window.openTrackingModal = function(orderId, statusLabel, rawStatus, dateStr) {
        const stepIdx = STATUS_STEP[rawStatus] ?? 0;
        document.getElementById('trackModalSub').textContent = 'Order #' + orderId + ' · Placed ' + dateStr;

        const bannerEl  = document.getElementById('trackStatusBanner');
        const badgeClass = { pending:'status-pending', processing:'status-processing', shipped:'status-shipped', delivered:'status-delivered' }[rawStatus] || 'status-default';
        bannerEl.innerHTML = '<span class="order-status-badge ' + badgeClass + '" style="margin-bottom:16px;display:inline-flex;">' + statusLabel + '</span>';

        const stepsEl = document.getElementById('trackSteps');
        stepsEl.innerHTML = '';
        TRACK_STEPS.forEach(function(step, i) {
            const isDone   = i < stepIdx;
            const isActive = i === stepIdx;
            const stateClass = isDone ? 'done' : (isActive ? 'active' : 'pending');
            const iconHtml = '<i class="fas ' + (isDone ? 'fa-check' : step.icon) + '"></i>';
            if (i > 0) {
                const conn = document.createElement('div');
                conn.className = 'track-step-connector' + (isDone ? ' done' : '');
                stepsEl.appendChild(conn);
            }
            const stepDiv = document.createElement('div');
            stepDiv.className = 'track-step';
            stepDiv.innerHTML = `
                <div class="track-step-icon ${stateClass}">${iconHtml}</div>
                <div class="track-step-body">
                    <div class="track-step-name ${stateClass}">${step.label}</div>
                    <div class="track-step-desc">${step.desc}</div>
                    ${isActive ? '<div class="track-step-date">' + dateStr + '</div>' : ''}
                </div>`;
            stepsEl.appendChild(stepDiv);
        });

        document.getElementById('trackingModal').classList.add('open');
        document.body.style.overflow = 'hidden';
    };

    window.closeTrackingModal = function() {
        document.getElementById('trackingModal').classList.remove('open');
        document.body.style.overflow = '';
    };

    /* ══ Escape key ══ */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') { closeTrackingModal(); closeReviewModal?.(); }
    });

    /* ══ Page toast (post-redirect) ══ */
    (function() {
        const params = new URLSearchParams(window.location.search);
        const toast  = document.getElementById('pageToast');
        if (!toast) return;
        let msg = '', cls = '';
        if (params.get('return_success')) { msg = '✓ Return request submitted successfully!'; cls = 'success'; }
        if (params.get('return_error'))   { msg = '✗ ' + decodeURIComponent(params.get('return_error')); cls = 'error'; }
        if (msg) {
            toast.className = 'page-toast ' + cls;
            toast.textContent = msg;
            toast.style.display = 'block';
            setTimeout(() => { toast.style.display = 'none'; }, 4500);
        }
    })();
})();
</script>
</section>