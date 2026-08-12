<?php
session_start();
if (empty($_SESSION['email']) || (($_SESSION['role'] ?? '') !== 'admin')) {
    header('Location: /Quigly/login.php?error=Please+login');
    exit;
}
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/feature_utils.php';

mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS return_requests (
        id                  INT AUTO_INCREMENT PRIMARY KEY,
        order_id            INT NOT NULL,
        order_item_id       INT DEFAULT NULL,
        user_id             INT NOT NULL,
        product_id          INT DEFAULT NULL,
        request_type        VARCHAR(20) NOT NULL DEFAULT 'return',
        preferred_resolution VARCHAR(30) NOT NULL DEFAULT 'full_refund',
        reason              VARCHAR(100) NOT NULL,
        details             TEXT,
        pickup_address      TEXT,
        proof_images        TEXT DEFAULT NULL,
        admin_note          TEXT,
        status              VARCHAR(30) NOT NULL DEFAULT 'pending',
        created_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at          DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

$filterStatus = strtolower(trim($_GET['status'] ?? ''));
$allowed = ['pending','approved','rejected','pickup_scheduled','received','refunded','completed'];
if (!in_array($filterStatus, $allowed, true)) $filterStatus = '';

$whereSql = $filterStatus !== '' ? "WHERE rr.status = '" . mysqli_real_escape_string($conn, $filterStatus) . "'" : '';

$countsByStatus = [];
$cq = mysqli_query($conn, "SELECT status, COUNT(*) AS c FROM return_requests GROUP BY status");
while ($cq && ($crow = mysqli_fetch_assoc($cq))) {
    $countsByStatus[strtolower($crow['status'])] = (int)$crow['c'];
}
$totalCount = array_sum($countsByStatus);

$requests = [];
$rq = mysqli_query($conn, "
    SELECT
        rr.*,
        qt.name   AS customer_name,
        qt.email  AS customer_email,
        qt.number AS customer_phone,
        o.total   AS order_total,
        o.status  AS order_status,
        p.name    AS product_name,
        p.image   AS product_image
    FROM return_requests rr
    LEFT JOIN quigly_table qt ON qt.id = rr.user_id
    LEFT JOIN orders        o  ON o.id  = rr.order_id
    LEFT JOIN products      p  ON p.id  = rr.product_id
    {$whereSql}
    ORDER BY rr.id DESC
");
while ($rq && ($row = mysqli_fetch_assoc($rq))) {
    $img = trim($row['product_image'] ?? '');
    if ($img === '') $row['product_image'] = '../../assets/images/no-image.png';
    elseif (!preg_match('#^(https?://|upload/)#i', $img)) $row['product_image'] = '../../upload/' . $img;
    else $row['product_image'] = '../../' . $img;
    $requests[] = $row;
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/sidebar.php';

function statusMeta(string $s): array {
    return match(strtolower($s)) {
        'approved'         => ['bg-success',         'fa-check-circle',    'Approved'],
        'rejected'         => ['bg-danger',           'fa-times-circle',    'Rejected'],
        'pickup_scheduled' => ['bg-primary',          'fa-truck',           'Pickup Scheduled'],
        'received'         => ['bg-info text-dark',   'fa-box-open',        'Received'],
        'refunded'         => ['bg-success',          'fa-money-bill-wave', 'Refunded'],
        'completed'        => ['bg-secondary',        'fa-check-double',    'Completed'],
        default            => ['bg-warning text-dark','fa-clock',           'Pending'],
    };
}
?>

<style>
/* ── Page ── */
.rr-page { padding: 28px 24px 60px; }
.rr-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:12px; margin-bottom:24px; }
.rr-title  { font-size:22px; font-weight:800; color:#1e293b; margin:0; display:flex; align-items:center; gap:10px; }
.rr-title i { color:#7c3aed; }
body.dark-mode .rr-title { color:#f1f5f9; }

/* ── Tabs ── */
.rr-tabs { display:flex; gap:8px; flex-wrap:wrap; margin-bottom:24px; }
.rr-tab  { padding:6px 16px; border-radius:999px; font-size:13px; font-weight:600; text-decoration:none; border:1.5px solid #e2e8f0; color:#64748b; background:#fff; transition:.2s; }
.rr-tab:hover  { background:#f1f5f9; color:#1e293b; text-decoration:none; }
.rr-tab.active { background:#7c3aed; color:#fff; border-color:#7c3aed; }
body.dark-mode .rr-tab { background:#1e293b; border-color:#334155; color:#94a3b8; }
body.dark-mode .rr-tab.active { background:#7c3aed; color:#fff; border-color:#7c3aed; }

/* ── Cards ── */
.rr-card { background:#fff; border:1px solid #e5e7eb; border-radius:20px; padding:20px 22px; margin-bottom:16px; transition:.2s; position:relative; overflow:hidden; }
.rr-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,#7c3aed,#4f46e5); }
.rr-card:hover { box-shadow:0 8px 30px rgba(124,58,237,.1); transform:translateY(-2px); }
body.dark-mode .rr-card { background:#1e293b; border-color:#334155; }

.rr-card-top { display:flex; gap:16px; align-items:flex-start; flex-wrap:wrap; }
.rr-prod-img { width:64px; height:64px; border-radius:14px; object-fit:cover; border:1px solid #e5e7eb; flex-shrink:0; }
.rr-meta { flex:1; min-width:200px; }
.rr-prod-name { font-weight:700; font-size:15px; color:#1e293b; margin-bottom:4px; }
body.dark-mode .rr-prod-name { color:#f1f5f9; }
.rr-meta-row { display:flex; flex-wrap:wrap; gap:8px; margin-top:6px; }
.rr-chip { font-size:12px; font-weight:600; padding:3px 10px; border-radius:999px; background:#f1f5f9; color:#64748b; border:1px solid #e2e8f0; }
body.dark-mode .rr-chip { background:#0f172a; border-color:#334155; color:#94a3b8; }

.rr-status-col { text-align:right; display:flex; flex-direction:column; align-items:flex-end; gap:8px; }
.rr-badge { display:inline-flex; align-items:center; gap:6px; padding:5px 14px; border-radius:999px; font-size:12px; font-weight:700; }

.rr-body { margin-top:16px; display:grid; grid-template-columns:1fr 1fr; gap:16px; }
@media(max-width:640px) { .rr-body { grid-template-columns:1fr; } }
.rr-field-label { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.04em; margin-bottom:3px; }
.rr-field-val   { font-size:14px; color:#334155; }
body.dark-mode .rr-field-val { color:#cbd5e1; }
.rr-details-text { background:#f8fafc; border-radius:12px; padding:10px 14px; font-size:13px; color:#475569; border:1px solid #e2e8f0; margin-top:4px; }
body.dark-mode .rr-details-text { background:#0f172a; border-color:#334155; color:#94a3b8; }

.rr-actions { margin-top:18px; display:flex; gap:10px; flex-wrap:wrap; align-items:center; }
.rr-select { border-radius:12px; border:1.5px solid #e2e8f0; padding:8px 14px; font-size:13px; font-weight:600; background:#fff; color:#334155; cursor:pointer; }
body.dark-mode .rr-select { background:#0f172a; border-color:#334155; color:#f1f5f9; }
.rr-note { flex:1; min-width:180px; border-radius:12px; border:1.5px solid #e2e8f0; padding:8px 14px; font-size:13px; background:#fff; color:#334155; }
body.dark-mode .rr-note { background:#0f172a; border-color:#334155; color:#f1f5f9; }
.rr-btn { height:40px; padding:0 20px; border-radius:12px; border:none; font-weight:700; font-size:13px; cursor:pointer; transition:.2s; display:inline-flex; align-items:center; gap:7px; }
.rr-btn-save { background:linear-gradient(135deg,#7c3aed,#4f46e5); color:#fff; box-shadow:0 4px 14px rgba(124,58,237,.3); }
.rr-btn-save:hover { transform:translateY(-1px); box-shadow:0 8px 20px rgba(124,58,237,.4); }
.rr-btn-view { background:#f1f5f9; color:#475569; border:1.5px solid #e2e8f0; height:36px; }
.rr-btn-view:hover { background:#ede9fe; color:#7c3aed; border-color:#c4b5fd; }
body.dark-mode .rr-btn-view { background:#0f172a; color:#94a3b8; border-color:#334155; }
body.dark-mode .rr-btn-view:hover { background:#1e1b4b; color:#a78bfa; border-color:#4c1d95; }

.rr-empty { text-align:center; padding:60px 20px; color:#94a3b8; }
.rr-empty i { font-size:48px; margin-bottom:16px; display:block; }

.toast-msg { position:fixed; bottom:24px; right:24px; z-index:99999; background:#10b981; color:#fff; border-radius:14px; padding:14px 22px; font-weight:700; font-size:14px; box-shadow:0 8px 30px rgba(0,0,0,.15); display:none; animation:slideUp .3s ease; position: relative; }
@media (max-width: 640px) {
    .rr-card-top { flex-direction: column; align-items: stretch; }
    .rr-status-col { text-align: left; align-items: flex-start; flex-direction: row; justify-content: space-between; width: 100%; margin-top: 10px; }
    .rr-actions { flex-direction: column; align-items: stretch; }
    .rr-select, .rr-note, .rr-btn { width: 100% !important; }
}
@keyframes slideUp { from{transform:translateY(20px);opacity:0} to{transform:translateY(0);opacity:1} }

/* ════════════════════════════════════════════
   MODAL  — injected into <body> via JS so it
   is never clipped by a parent stacking context
   z-index must beat sidebar (1040) & navbar (9999)
   ════════════════════════════════════════════ */
#rrModalOverlay {
    display: none;
    position: fixed;
    inset: 0;
    /* above sidebar (1040) and any navbar (9999) */
    z-index: 10050;
    background: rgba(2, 6, 23, 0.72);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
    /* CENTER perfectly */
    align-items: center;
    justify-content: center;
    padding: 16px;
}
#rrModalOverlay.rr-open {
    display: flex;
    animation: rrOverlayIn .18s ease both;
}
@keyframes rrOverlayIn { from{opacity:0} to{opacity:1} }

#rrModalBox {
    background: #fff;
    border-radius: 24px;
    width: 100%;
    max-width: 700px;
    max-height: 88vh;
    overflow-y: auto;
    box-shadow: 0 32px 80px rgba(0,0,0,.35);
    animation: rrBoxIn .22s cubic-bezier(.34,1.46,.64,1) both;
    position: relative;
    scrollbar-width: thin;
    scrollbar-color: #c4b5fd transparent;
}
body.dark-mode #rrModalBox { background: #1e293b; }
@keyframes rrBoxIn {
    from { transform: scale(.88) translateY(28px); opacity:0; }
    to   { transform: scale(1)   translateY(0);    opacity:1; }
}
#rrModalBox::before {
    content:'';
    display:block;
    height:4px;
    border-radius:24px 24px 0 0;
    background:linear-gradient(90deg,#7c3aed,#4f46e5,#06b6d4);
}

/* Modal header */
.mm-hdr {
    display:flex; align-items:flex-start; gap:16px;
    padding:20px 24px 16px;
    border-bottom:1px solid #f1f5f9;
    position:sticky; top:0; background:#fff; z-index:2;
}
body.dark-mode .mm-hdr { background:#1e293b; border-bottom-color:#334155; }
.mm-hdr-img { width:70px; height:70px; border-radius:16px; object-fit:cover; border:2px solid #e2e8f0; flex-shrink:0; }
body.dark-mode .mm-hdr-img { border-color:#334155; }
.mm-hdr-info { flex:1; min-width:0; }
.mm-hdr-title { font-size:17px; font-weight:800; color:#1e293b; margin:0 0 5px; line-height:1.3; }
body.dark-mode .mm-hdr-title { color:#f1f5f9; }
.mm-hdr-sub { font-size:12px; color:#94a3b8; font-weight:600; }
.mm-close {
    width:36px; height:36px; border-radius:50%; border:none;
    background:#f1f5f9; color:#64748b; font-size:15px;
    cursor:pointer; display:flex; align-items:center; justify-content:center;
    flex-shrink:0; transition:.2s;
}
.mm-close:hover { background:#fee2e2; color:#ef4444; transform:scale(1.1); }
body.dark-mode .mm-close { background:#0f172a; color:#94a3b8; }
body.dark-mode .mm-close:hover { background:#450a0a; color:#fca5a5; }

/* Modal body */
.mm-body { padding:20px 24px 28px; }

.mm-status-row { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:10px; }
.mm-amount { font-size:22px; font-weight:900; color:#7c3aed; }

.mm-section {
    font-size:11px; font-weight:800; color:#7c3aed;
    text-transform:uppercase; letter-spacing:.08em;
    margin:22px 0 12px; display:flex; align-items:center; gap:8px;
}
.mm-section::after { content:''; flex:1; height:1px; background:linear-gradient(90deg,#ede9fe,transparent); }
body.dark-mode .mm-section::after { background:linear-gradient(90deg,#3b0764,transparent); }

.mm-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
@media(max-width:500px){ .mm-grid { grid-template-columns:1fr; } }
.mm-fl { font-size:11px; font-weight:700; color:#94a3b8; text-transform:uppercase; letter-spacing:.05em; margin-bottom:3px; }
.mm-fv { font-size:14px; font-weight:600; color:#334155; line-height:1.5; }
body.dark-mode .mm-fv { color:#cbd5e1; }

.mm-textblock {
    background:#f8fafc; border:1px solid #e2e8f0;
    border-radius:14px; padding:12px 16px;
    font-size:13px; color:#475569; line-height:1.6;
}
body.dark-mode .mm-textblock { background:#0f172a; border-color:#334155; color:#94a3b8; }

.mm-notebox {
    background:#faf5ff; border:1px solid #ede9fe;
    border-left:3px solid #7c3aed; border-radius:14px;
    padding:12px 16px; font-size:13px; color:#6d28d9; line-height:1.6;
}
body.dark-mode .mm-notebox { background:#1e1b4b; border-color:#4c1d95; color:#a78bfa; }

.mm-imgs { display:flex; flex-wrap:wrap; gap:10px; margin-top:6px; }
.mm-imgs a img {
    width:88px; height:88px; object-fit:cover;
    border-radius:12px; border:2px solid #e2e8f0;
    transition:.2s; cursor:zoom-in;
}
.mm-imgs a img:hover { transform:scale(1.08); border-color:#7c3aed; box-shadow:0 4px 16px rgba(124,58,237,.3); }

hr.mm-div { border:none; border-top:1px solid #f1f5f9; margin:6px 0; }
body.dark-mode hr.mm-div { border-color:#334155; }
</style>

<div class="rr-page">

    <div class="rr-header">
        <h2 class="rr-title"><i class="fas fa-rotate-left"></i> Return & Refund Requests</h2>
        <span class="badge bg-secondary rounded-pill px-3 py-2 fs-6"><?= $totalCount ?> Total</span>
    </div>

    <div class="rr-tabs">
        <a href="return_requests.php"                         class="rr-tab <?= $filterStatus===''               ? 'active':'' ?>">All <span class="ms-1 opacity-75">(<?= $totalCount ?>)</span></a>
        <a href="return_requests.php?status=pending"          class="rr-tab <?= $filterStatus==='pending'          ? 'active':'' ?>">Pending <span class="ms-1 opacity-75">(<?= $countsByStatus['pending'] ?? 0 ?>)</span></a>
        <a href="return_requests.php?status=approved"         class="rr-tab <?= $filterStatus==='approved'         ? 'active':'' ?>">Approved <span class="ms-1 opacity-75">(<?= $countsByStatus['approved'] ?? 0 ?>)</span></a>
        <a href="return_requests.php?status=pickup_scheduled" class="rr-tab <?= $filterStatus==='pickup_scheduled' ? 'active':'' ?>">Pickup Scheduled</a>
        <a href="return_requests.php?status=received"         class="rr-tab <?= $filterStatus==='received'         ? 'active':'' ?>">Received</a>
        <a href="return_requests.php?status=refunded"         class="rr-tab <?= $filterStatus==='refunded'         ? 'active':'' ?>">Refunded</a>
        <a href="return_requests.php?status=rejected"         class="rr-tab <?= $filterStatus==='rejected'         ? 'active':'' ?>">Rejected <span class="ms-1 opacity-75">(<?= $countsByStatus['rejected'] ?? 0 ?>)</span></a>
        <a href="return_requests.php?status=completed"        class="rr-tab <?= $filterStatus==='completed'        ? 'active':'' ?>">Completed</a>
    </div>

    <?php if (isset($_GET['updated'])): ?>
    <div class="toast-msg" id="toastMsg">✅ Status updated successfully</div>
    <script>
        const _t = document.getElementById('toastMsg');
        _t.style.display = 'block';
        setTimeout(() => { _t.style.opacity='0'; _t.style.transition='opacity .5s'; setTimeout(()=>_t.remove(),500); }, 3000);
    </script>
    <?php endif; ?>

    <?php if (empty($requests)): ?>
    <div class="rr-empty">
        <i class="fas fa-inbox"></i>
        <h5>No return requests found</h5>
        <p class="mb-0">They will appear here once customers submit them.</p>
    </div>
    <?php else: ?>

    <?php foreach ($requests as $req):
        [$badgeCls, $badgeIcon, $badgeLabel] = statusMeta($req['status'] ?? 'pending');
        $typeLabel   = ucfirst(str_replace('_',' ',$req['request_type']        ?? 'Return'));
        $prefLabel   = ucfirst(str_replace('_',' ',$req['preferred_resolution'] ?? 'Refund'));
        $reasonLabel = ucfirst(str_replace('_',' ',$req['reason'] ?? ''));
        $createdAt   = !empty($req['created_at']) ? date('d M Y, h:i A', strtotime($req['created_at'])) : '—';
        $updatedAt   = !empty($req['updated_at']) ? date('d M Y, h:i A', strtotime($req['updated_at'])) : '—';

        $modalData = json_encode([
            'id'               => (int)$req['id'],
            'product_name'     => $req['product_name']   ?? 'Product',
            'product_image'    => $req['product_image'],
            'order_id'         => (int)$req['order_id'],
            'order_total'      => number_format((float)($req['order_total'] ?? 0), 2),
            'order_status'     => ucfirst($req['order_status'] ?? '—'),
            'customer_name'    => $req['customer_name']  ?? '—',
            'customer_email'   => $req['customer_email'] ?? '—',
            'customer_phone'   => $req['customer_phone'] ?? '—',
            'request_type'     => $typeLabel,
            'preferred_resolution' => $prefLabel,
            'reason'           => $reasonLabel,
            'status'           => $req['status'] ?? 'pending',
            'badge_cls'        => $badgeCls,
            'badge_icon'       => $badgeIcon,
            'badge_label'      => $badgeLabel,
            'details'          => $req['details']        ?? '',
            'pickup_address'   => $req['pickup_address'] ?? '',
            'admin_note'       => $req['admin_note']     ?? '',
            'proof_images'     => json_decode($req['proof_images'] ?? '[]', true) ?: [],
            'created_at'       => $createdAt,
            'updated_at'       => $updatedAt,
        ], JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
    ?>
    <div class="rr-card">
        <div class="rr-card-top">
            <img src="<?= htmlspecialchars($req['product_image']) ?>"
                 class="rr-prod-img" alt="product"
                 onerror="this.src='../../assets/images/no-image.png'">

            <div class="rr-meta">
                <div class="rr-prod-name"><?= htmlspecialchars($req['product_name'] ?? 'Product') ?></div>
                <div class="rr-meta-row">
                    <span class="rr-chip"><i class="fas fa-hashtag me-1"></i>Request #<?= (int)$req['id'] ?></span>
                    <span class="rr-chip"><i class="fas fa-shopping-bag me-1"></i>Order #<?= (int)$req['order_id'] ?></span>
                    <span class="rr-chip"><i class="fas fa-user me-1"></i><?= htmlspecialchars($req['customer_name'] ?? 'Customer') ?></span>
                    <span class="rr-chip"><i class="fas fa-calendar me-1"></i><?= $createdAt ?></span>
                    <span class="rr-chip"><i class="fas fa-rotate-left me-1"></i><?= $typeLabel ?></span>
                    <span class="rr-chip"><i class="fas fa-money-bill-wave me-1"></i><?= $prefLabel ?></span>
                </div>
            </div>

            <div class="rr-status-col">
                <span class="rr-badge badge <?= $badgeCls ?>">
                    <i class="fas <?= $badgeIcon ?>"></i> <?= $badgeLabel ?>
                </span>
                <div class="fw-bold" style="font-size:15px;color:#7c3aed;">
                    ₹<?= number_format((float)($req['order_total'] ?? 0), 2) ?>
                </div>
                <button class="rr-btn rr-btn-view"
                        onclick='rrOpenModal(<?= htmlspecialchars($modalData, ENT_QUOTES, "UTF-8") ?>)'>
                    <i class="fas fa-eye"></i> View Details
                </button>
            </div>
        </div>

        <!-- Admin Action Form -->
        <form method="POST" action="../actions/return_action.php" class="rr-actions">
            <input type="hidden" name="id" value="<?= (int)$req['id'] ?>">
            <select name="status" class="rr-select">
                <?php
                $statuses = ['pending'=>'Pending','approved'=>'Approved','pickup_scheduled'=>'Pickup Scheduled',
                             'received'=>'Received','refunded'=>'Refunded','rejected'=>'Rejected','completed'=>'Completed'];
                foreach ($statuses as $val => $lbl):
                    $sel = strtolower($req['status'] ?? '') === $val ? 'selected' : '';
                ?>
                <option value="<?= $val ?>" <?= $sel ?>><?= $lbl ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="admin_note" class="rr-note"
                   placeholder="Admin note (optional)"
                   value="<?= htmlspecialchars($req['admin_note'] ?? '') ?>">
            <button type="submit" class="rr-btn rr-btn-save">
                <i class="fas fa-save"></i> Update
            </button>
        </form>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>

</div><!-- /.rr-page -->

<!-- ══════════════════════════════════════════
     MODAL HTML — appended to <body> by JS
     so it is NEVER trapped inside a stacking
     context created by the sidebar or content
     wrapper. z-index:10050 beats sidebar:1040
     ══════════════════════════════════════════ -->
<script>
(function () {
    /* ── Build modal DOM once and append to <body> ── */
    const overlay = document.createElement('div');
    overlay.id = 'rrModalOverlay';
    overlay.innerHTML = `
    <div id="rrModalBox">
        <div class="mm-hdr">
            <img id="mm-img" src="" alt="product" class="mm-hdr-img"
                 onerror="this.src='../../assets/images/no-image.png'">
            <div class="mm-hdr-info">
                <p class="mm-hdr-title" id="mm-prod-name"></p>
                <p class="mm-hdr-sub"  id="mm-sub"></p>
            </div>
            <button class="mm-close" id="mm-close-btn" title="Close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="mm-body">
            <div class="mm-status-row">
                <span class="badge rr-badge" id="mm-badge"></span>
                <span class="mm-amount" id="mm-amount"></span>
            </div>
            <hr class="mm-div">

            <div class="mm-section"><i class="fas fa-rotate-left"></i> Request Info</div>
            <div class="mm-grid">
                <div><div class="mm-fl">Request Type</div>      <div class="mm-fv" id="mm-type"></div></div>
                <div><div class="mm-fl">Preferred Resolution</div><div class="mm-fv" id="mm-pref"></div></div>
                <div><div class="mm-fl">Reason</div>            <div class="mm-fv" id="mm-reason"></div></div>
                <div><div class="mm-fl">Order Status</div>      <div class="mm-fv" id="mm-orderstatus"></div></div>
                <div><div class="mm-fl">Submitted</div>         <div class="mm-fv" id="mm-created"></div></div>
                <div><div class="mm-fl">Last Updated</div>      <div class="mm-fv" id="mm-updated"></div></div>
            </div>

            <div class="mm-section"><i class="fas fa-user"></i> Customer Info</div>
            <div class="mm-grid">
                <div><div class="mm-fl">Name</div>  <div class="mm-fv" id="mm-cname"></div></div>
                <div><div class="mm-fl">Email</div> <div class="mm-fv" id="mm-cemail"></div></div>
                <div><div class="mm-fl">Phone</div> <div class="mm-fv" id="mm-cphone"></div></div>
            </div>

            <div id="mm-pickup-sec">
                <div class="mm-section"><i class="fas fa-map-marker-alt"></i> Pickup Address</div>
                <div class="mm-textblock" id="mm-pickup"></div>
            </div>

            <div id="mm-details-sec">
                <div class="mm-section"><i class="fas fa-comment-alt"></i> Customer Details</div>
                <div class="mm-textblock" id="mm-details"></div>
            </div>

            <div id="mm-note-sec">
                <div class="mm-section"><i class="fas fa-shield-alt"></i> Admin Note</div>
                <div class="mm-notebox" id="mm-note"></div>
            </div>

            <div id="mm-imgs-sec">
                <div class="mm-section"><i class="fas fa-images"></i> Proof Images</div>
                <div class="mm-imgs" id="mm-imgs"></div>
            </div>
        </div>
    </div>`;

    document.body.appendChild(overlay);

    /* Close button */
    document.getElementById('mm-close-btn').addEventListener('click', rrClose);

    /* Click backdrop to close */
    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) rrClose();
    });

    /* Escape key */
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') rrClose();
    });

    /* ── Helpers ── */
    function esc(str) {
        return String(str)
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#39;');
    }
    function nl2br(str) { return esc(str).replace(/\n/g,'<br>'); }
    function show(id, html) { document.getElementById(id).innerHTML = html; }
    function text(id, val)  { document.getElementById(id).textContent = val; }
    function secShow(secId, condition) {
        document.getElementById(secId).style.display = condition ? '' : 'none';
    }

    /* ── Open ── */
    window.rrOpenModal = function(d) {
        /* Header */
        document.getElementById('mm-img').src = d.product_image || '../../assets/images/no-image.png';
        text('mm-prod-name', d.product_name);
        text('mm-sub', 'Request #' + d.id + '  ·  Order #' + d.order_id);

        /* Badge */
        const badge = document.getElementById('mm-badge');
        badge.className = 'badge rr-badge ' + d.badge_cls;
        badge.innerHTML = '<i class="fas ' + d.badge_icon + '"></i> ' + esc(d.badge_label);

        /* Amount */
        text('mm-amount', '₹' + d.order_total);

        /* Request info */
        text('mm-type',        d.request_type);
        text('mm-pref',        d.preferred_resolution);
        text('mm-reason',      d.reason);
        text('mm-orderstatus', d.order_status);
        text('mm-created',     d.created_at);
        text('mm-updated',     d.updated_at);

        /* Customer */
        text('mm-cname',  d.customer_name);
        text('mm-cemail', d.customer_email);
        text('mm-cphone', d.customer_phone || '—');

        /* Pickup address */
        secShow('mm-pickup-sec', d.pickup_address && d.pickup_address.trim());
        show('mm-pickup', nl2br(d.pickup_address || ''));

        /* Details */
        secShow('mm-details-sec', d.details && d.details.trim());
        show('mm-details', nl2br(d.details || ''));

        /* Admin note */
        secShow('mm-note-sec', d.admin_note && d.admin_note.trim());
        show('mm-note', nl2br(d.admin_note || ''));

        /* Proof images */
        const imgsEl = document.getElementById('mm-imgs');
        imgsEl.innerHTML = '';
        if (d.proof_images && d.proof_images.length) {
            d.proof_images.forEach(function(src) {
                const a   = document.createElement('a');
                a.href    = '../../' + src;
                a.target  = '_blank';
                const img = document.createElement('img');
                img.src   = '../../' + src;
                img.alt   = 'proof';
                img.onerror = function(){ this.parentElement.style.display='none'; };
                a.appendChild(img);
                imgsEl.appendChild(a);
            });
        }
        secShow('mm-imgs-sec', d.proof_images && d.proof_images.length > 0);

        /* Open */
        document.getElementById('rrModalBox').scrollTop = 0;
        overlay.classList.add('rr-open');
        document.body.style.overflow = 'hidden';
    };

    function rrClose() {
        overlay.classList.remove('rr-open');
        document.body.style.overflow = '';
    }
    window.rrClose = rrClose;
})();
</script>

<?php include_once __DIR__ . '/../includes/footer.php'; ?>
    