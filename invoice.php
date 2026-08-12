<?php
session_start();
include("admin/includes/db.php");

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$orderId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$userQuery = mysqli_query($conn, "
    SELECT id,name,email,number
    FROM quigly_table
    WHERE email='$email'
    LIMIT 1
");

$user = mysqli_fetch_assoc($userQuery);

if (!$user) {
    exit("User not found");
}

$userId = (int)$user['id'];

$orderQuery = mysqli_query($conn, "
    SELECT *
    FROM orders
    WHERE id='$orderId'
    AND user_id='$userId'
    LIMIT 1
");

$order = mysqli_fetch_assoc($orderQuery);

if (!$order) {
    exit("Invoice not found");
}

$itemQuery = mysqli_query($conn, "
    SELECT
        oi.quantity,
        oi.price,
        p.name,
        p.image
    FROM order_items oi
    INNER JOIN products p
    ON p.id = oi.product_id
    WHERE oi.order_id='$orderId'
");

$orderDate = !empty($order['created_at'])
    ? date("d M Y, h:i A", strtotime($order['created_at']))
    : '';

$status = strtolower($order['status']);

$statusClass = 'bg-secondary';

if ($status === 'pending') {
    $statusClass = 'bg-warning text-dark';
} elseif ($status === 'shipped') {
    $statusClass = 'bg-primary';
} elseif ($status === 'delivered') {
    $statusClass = 'bg-success';
} elseif ($status === 'cancelled') {
    $statusClass = 'bg-danger';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>
Invoice #QG<?php echo $orderId; ?>
</title>

<link href="assets/css/bootstrap-5.3.8-dist/css/bootstrap.min.css" rel="stylesheet">

<link
rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
/>

<style>

:root{
    --primary:#7c3aed;
    --secondary:#6d28d9;
    --dark:#0f172a;
    --border:#e2e8f0;
}

body{
    margin:0;
    background:
    radial-gradient(circle at top left,#ddd6fe 0%,transparent 25%),
    radial-gradient(circle at top right,#bfdbfe 0%,transparent 25%),
    #f8fafc;
    font-family:Inter,sans-serif;
    color:#111827;
}

.invoice-wrapper{
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    padding:30px 0;
}

.invoice-card{
    width:100%;
    max-width:1450px;
    min-height:92vh;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    background:rgba(255,255,255,.92);
    backdrop-filter:blur(18px);
    border:1px solid rgba(255,255,255,.5);
    border-radius:35px;
    overflow:hidden;
    box-shadow:
    0 20px 60px rgba(15,23,42,.08),
    0 10px 30px rgba(124,58,237,.08);
}

.invoice-header{
    background:
    radial-gradient(circle at top left,rgba(255,255,255,.12),transparent 35%),
    linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    padding:45px;
}

.brand{
    font-size:2rem;
    font-weight:900;
}

.invoice-title{
    font-size:3rem;
    font-weight:900;
    margin-bottom:8px;
}

.invoice-content{
    flex:1;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    padding:40px;
}

.info-card,
.total-card{
    background:#fff;
    border:1px solid var(--border);
    border-radius:24px;
    padding:25px;
    box-shadow:0 8px 25px rgba(15,23,42,.04);
    height:100%;
}

.info-label{
    font-size:.78rem;
    color:#64748b;
    text-transform:uppercase;
    letter-spacing:.8px;
    margin-bottom:12px;
    font-weight:700;
}

.info-value{
    line-height:1.8;
    font-weight:600;
}

.table{
    border-collapse:separate;
    border-spacing:0 15px;
}

.table thead th{
    border:none;
    color:#64748b;
    font-size:.82rem;
    text-transform:uppercase;
    letter-spacing:.5px;
}

.table tbody tr{
    background:#fff;
    box-shadow:0 8px 25px rgba(15,23,42,.04);
}

.table tbody td{
    border:none;
    padding:18px;
    vertical-align:middle;
}

.table tbody tr td:first-child{
    border-radius:18px 0 0 18px;
}

.table tbody tr td:last-child{
    border-radius:0 18px 18px 0;
}

.product-image{
    width:75px;
    height:75px;
    border-radius:18px;
    object-fit:cover;
    border:1px solid var(--border);
}

.product-name{
    font-weight:700;
}

.total-row,
.total-final{
    display:flex;
    justify-content:space-between;
}

.total-row{
    margin-bottom:16px;
    color:#475569;
    font-weight:600;
}

.total-final{
    font-size:2rem;
    font-weight:900;
    color:var(--primary);
}

.action-buttons{
    display:flex;
    justify-content:space-between;
    gap:15px;
    flex-wrap:wrap;
    margin-top:35px;
}

.premium-btn{
    height:55px;
    padding:0 28px;
    border:none;
    border-radius:18px;
    font-weight:700;
    display:flex;
    align-items:center;
    gap:10px;
    text-decoration:none;
    transition:.3s;
}

.premium-btn:hover{
    transform:translateY(-2px);
}

.btn-back{
    background:#eef2ff;
    color:#4338ca;
}

.btn-print{
    background:linear-gradient(135deg,var(--primary),var(--secondary));
    color:#fff;
    box-shadow:0 15px 35px rgba(124,58,237,.25);
}

.footer-note{
    margin-top:35px;
    text-align:center;
    color:#64748b;
}

@media(max-width:768px){

    .invoice-wrapper{
        padding:15px 0;
    }

    .invoice-card{
        min-height:auto;
        border-radius:25px;
    }

    .invoice-header{
        padding:30px 22px;
    }

    .invoice-content{
        padding:22px;
    }

    .invoice-title{
        font-size:2.1rem;
    }

    .total-final{
        font-size:1.5rem;
    }

    .premium-btn{
        width:100%;
        justify-content:center;
    }

}

@media print{

    body{
        background:#fff;
    }

    .action-buttons{
        display:none;
    }

    .invoice-card{
        box-shadow:none;
        border:none;
    }

}
/* ===== WARRANTY CARD ===== */
.warranty-card {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 40%, #f5f3ff 100%);
    border: 1.5px solid rgba(124,58,237,.15);
    border-radius: 24px;
    padding: 28px 30px;
    margin-top: 28px;
    position: relative;
    overflow: hidden;
}
.warranty-card::before {
    content: '';
    position: absolute;
    top: -30px; right: -30px;
    width: 120px; height: 120px;
    background: radial-gradient(circle, rgba(124,58,237,.12), transparent 70%);
    border-radius: 50%;
}
.warranty-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
}
.warranty-icon-wrap {
    width: 52px; height: 52px;
    border-radius: 16px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    box-shadow: 0 8px 20px rgba(124,58,237,.3);
}
.warranty-icon-wrap i { color: #fff; font-size: 22px; }
.warranty-header-text h4 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 3px;
}
.warranty-header-text p {
    font-size: .85rem;
    color: #64748b;
    margin: 0;
}
.warranty-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 14px;
    margin-bottom: 20px;
}
.warranty-item {
    background: rgba(255,255,255,.75);
    border-radius: 16px;
    padding: 16px;
    border: 1px solid rgba(124,58,237,.1);
    text-align: center;
}
.warranty-item i {
    font-size: 22px;
    color: #7c3aed;
    margin-bottom: 8px;
    display: block;
}
.warranty-item-title {
    font-size: .82rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 3px;
}
.warranty-item-desc {
    font-size: .75rem;
    color: #64748b;
    line-height: 1.4;
}
.warranty-cert-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 10px;
    background: rgba(255,255,255,.6);
    border-radius: 16px;
    padding: 14px 18px;
    border: 1px dashed rgba(124,58,237,.25);
}
.warranty-cert-left {
    font-size: .82rem;
    color: #475569;
    line-height: 1.6;
}
.warranty-cert-left strong { color: #0f172a; }
.warranty-cert-badge {
    display: inline-flex;
    align-items: center;
    gap: 7px;
    padding: 6px 14px;
    border-radius: 999px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    font-size: .78rem;
    font-weight: 700;
    white-space: nowrap;
}

@media print {
    .warranty-card { border: 1.5px solid #a5b4fc; background: #f5f3ff; }
    .review-section { display: none; }
}

/* ===== REVIEW SECTION ===== */
.review-section {
    background: linear-gradient(135deg, rgba(124,58,237,.07), rgba(37,99,235,.07));
    border: 1px solid rgba(124,58,237,.2);
    border-radius: 24px;
    padding: 28px 30px;
    margin-top: 30px;
}

.review-section-header {
    display: flex;
    align-items: center;
    gap: 14px;
    margin-bottom: 20px;
}

.review-section-header > i {
    font-size: 2rem;
    color: #f59e0b;
    flex-shrink: 0;
}

.review-section-title {
    font-size: 1.1rem;
    font-weight: 800;
    color: #0f172a;
}

.review-section-sub {
    font-size: .88rem;
    color: #64748b;
    margin-top: 3px;
}

.review-stars {
    display: flex;
    flex-direction: row-reverse;
    gap: 6px;
    justify-content: flex-start;
    margin-bottom: 18px;
}

.review-stars input { display: none; }

.review-stars label {
    font-size: 2.4rem;
    color: #cbd5e1;
    cursor: pointer;
    transition: color .15s, transform .15s;
}

.review-stars label:hover,
.review-stars label:hover ~ label,
.review-stars input:checked ~ label {
    color: #f59e0b;
    transform: scale(1.1);
}

.review-textarea {
    width: 100%;
    min-height: 90px;
    border-radius: 16px;
    border: 1px solid rgba(124,58,237,.2);
    background: rgba(255,255,255,.7);
    padding: 14px 18px;
    font-size: .95rem;
    resize: vertical;
    font-family: inherit;
    color: #0f172a;
    margin-bottom: 16px;
    transition: border-color .2s;
    display: block;
}

.review-textarea:focus {
    outline: none;
    border-color: #7c3aed;
    box-shadow: 0 0 0 4px rgba(124,58,237,.1);
}

.review-submit-btn {
    height: 50px;
    padding: 0 28px;
    border: none;
    border-radius: 16px;
    background: linear-gradient(135deg, #7c3aed, #2563eb);
    color: #fff;
    font-weight: 700;
    font-size: .95rem;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: .3s;
    box-shadow: 0 10px 30px rgba(124,58,237,.25);
}

.review-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 16px 40px rgba(124,58,237,.35);
}

.review-msg {
    margin-top: 14px;
    padding: 12px 18px;
    border-radius: 14px;
    font-weight: 600;
    font-size: .93rem;
}

.review-msg.success {
    background: rgba(16,185,129,.1);
    color: #059669;
    border: 1px solid rgba(16,185,129,.2);
}

.review-msg.error {
    background: rgba(239,68,68,.1);
    color: #dc2626;
    border: 1px solid rgba(239,68,68,.2);
}

@media print {
    .review-section { display: none; }
}
b{
    color:green;
}
</style>

</head>

<body>

<div class="container invoice-wrapper">

    <div class="invoice-card">

        <div class="invoice-header d-flex justify-content-between align-items-start flex-wrap gap-4">

            <div>

                <div class="brand mb-4">
                    Quigly
                </div>

                <div class="invoice-title">
                    Invoice
                </div>

                <div class="opacity-75">
                    Order #QG<?php echo $orderId; ?>
                </div>

            </div>

            <div class="text-end">

                <div class="badge <?php echo $statusClass; ?> rounded-pill px-4 py-2 fs-6">
                    <?php echo ucfirst($status); ?>
                </div>

                <div class="mt-3 opacity-75">
                    <?php echo $orderDate; ?>
                </div>

            </div>

        </div>

        <div class="invoice-content">

            <div>

                <div class="row g-4 mb-4">

                    <div class="col-md-6">

                        <div class="info-card">

                            <div class="info-label">
                                Customer Details
                            </div>

                            <div class="info-value">

                                <div>
                                    <?php echo htmlspecialchars($user['name']); ?>
                                </div>

                                <div>
                                    <?php echo htmlspecialchars($user['email']); ?>
                                </div>

                                <div>
                                    +91 <?php echo htmlspecialchars($user['number']); ?>
                                </div>

                            </div>

                        </div>

                    </div>

                    <div class="col-md-6">

                        <div class="info-card">

                            <div class="info-label">
                                Delivery Address
                            </div>

                            <div class="info-value">
                                <?php echo nl2br(htmlspecialchars($order['customer_address'])); ?>
                            </div>

                        </div>

                    </div>

                </div>

                <div class="table-responsive">

                    <table class="table align-middle">

                        <thead>

                            <tr>
                                <th>Product</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                                <th>Payment</th>
                            </tr>

                        </thead>

                        <tbody>

                            <?php
                            $grandTotal = 0;

                            while($item = mysqli_fetch_assoc($itemQuery)){

                                $subtotal =
                                (float)$item['price']
                                *
                                (int)$item['quantity'];

                                $grandTotal += $subtotal;
                            ?>

                            <tr>

                                <td>

                                    <div class="d-flex align-items-center gap-3">

                                        <img
                                        src="upload/<?php echo htmlspecialchars($item['image']); ?>"
                                        class="product-image"
                                        >

                                        <div class="product-name">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                        </div>

                                    </div>

                                </td>

                                <td class="fw-bold">
                                    <?php echo (int)$item['quantity']; ?>
                                </td>

                                <td class="fw-bold">
                                    ₹<?php echo number_format((float)$item['price'],2); ?>
                                </td>

                                <td class="fw-bold text-primary">
                                    ₹<?php echo number_format($subtotal,2); ?>
                                </td>
                                <td class="fw-bold">
                                    <b id="b">Completed</b>
                                </td>
                            </tr>

                            <?php } ?>

                        </tbody>

                    </table>

                </div>

            </div>

            <div>

                <div class="row justify-content-end">

                    <div class="col-lg-5">

                        <div class="total-card">

                            <div class="total-row">
                                <span>Subtotal</span>

                                <span>
                                    ₹<?php echo number_format($grandTotal,2); ?>
                                </span>
                            </div>

                            <div class="total-row">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>

                            <div class="total-row">
                                <span>Tax</span>
                                <span>₹0.00</span>
                            </div>

                            <hr>

                            <div class="total-final">

                                <span>Total</span>

                                <span>
                                    ₹<?php echo number_format($grandTotal,2); ?>
                                </span>

                            </div>

                        </div>

                    </div>

                </div>

                <?php if ($status === 'delivered') { ?>
                <div class="warranty-card">
                    <div class="warranty-header">
                        <div class="warranty-icon-wrap">
                            <i class="fas fa-shield-halved"></i>
                        </div>
                        <div class="warranty-header-text">
                            <h4>Product Warranty &amp; Guarantee Certificate</h4>
                            <p>This certificate confirms the warranty coverage for your order #QG<?php echo $orderId; ?></p>
                        </div>
                    </div>

                    <div class="warranty-grid">
                        <div class="warranty-item">
                            <i class="fas fa-shield-alt"></i>
                            <div class="warranty-item-title">1 Year Warranty</div>
                            <div class="warranty-item-desc">Manufacturing defects covered for 12 months</div>
                        </div>
                        <div class="warranty-item">
                            <i class="fas fa-rotate-left"></i>
                            <div class="warranty-item-title">7-Day Returns</div>
                            <div class="warranty-item-desc">Hassle-free returns within 7 days of delivery</div>
                        </div>
                        <div class="warranty-item">
                            <i class="fas fa-tools"></i>
                            <div class="warranty-item-title">Free Repair</div>
                            <div class="warranty-item-desc">Authorized service for warranty claims</div>
                        </div>
                        <div class="warranty-item">
                            <i class="fas fa-headset"></i>
                            <div class="warranty-item-title">24/7 Support</div>
                            <div class="warranty-item-desc">Dedicated customer care anytime</div>
                        </div>
                    </div>

                    <div class="warranty-cert-row">
                        <div class="warranty-cert-left">
                            <strong>Certificate No:</strong> QG-WRN-<?php echo str_pad($orderId, 6, '0', STR_PAD_LEFT); ?><br>
                            <strong>Customer:</strong> <?php echo htmlspecialchars($user['name']); ?><br>
                            <strong>Purchase Date:</strong> <?php echo $orderDate; ?><br>
                            <strong>Warranty Valid Until:</strong> <?php echo date("d M Y", strtotime('+1 year', strtotime($order['created_at']))); ?>
                        </div>
                        <div class="warranty-cert-badge">
                            <i class="fas fa-check-circle"></i> Quigly Certified
                        </div>
                    </div>
                </div>
                <?php } ?>

                <?php if ($status === 'delivered') { ?>
                <div class="review-section">
                    <div class="review-section-header">
                        <i class="fa-solid fa-star"></i>
                        <div>
                            <div class="review-section-title">How was your order?</div>
                            <div class="review-section-sub">Your feedback helps us improve. Takes only 30 seconds.</div>
                        </div>
                    </div>
                    <div class="review-stars" id="reviewStars">
                        <?php for ($s = 5; $s >= 1; $s--) { ?>
                        <input type="radio" name="invoiceRating" id="star<?php echo $s; ?>" value="<?php echo $s; ?>">
                        <label for="star<?php echo $s; ?>" title="<?php echo $s; ?> star<?php echo $s>1?'s':''; ?>">&#9733;</label>
                        <?php } ?>
                    </div>
                    <textarea id="reviewText" class="review-textarea" placeholder="Write your review here... (optional)"></textarea>
                    <button class="review-submit-btn" onclick="submitInvoiceReview()">
                        <i class="fa-solid fa-paper-plane"></i> Submit Review
                    </button>
                    <div id="reviewMsg" class="review-msg" style="display:none;"></div>
                </div>
                <?php } ?>

                <div class="action-buttons">

                    <a
                    href="index.php"
                    class="premium-btn btn-back">

                        <i class="fa-solid fa-arrow-left"></i>
                        Back To Shop

                    </a>

                    <button
                    class="premium-btn btn-print"
                    onclick="window.print()">

                        <i class="fa-solid fa-print"></i>
                        Print Invoice

                    </button>

                </div>

                <div class="footer-note">
                    Thank you for shopping with Quigly ❤️
                </div>

            </div>

        </div>

    </div>

</div>


<script>
function submitInvoiceReview() {
    const ratingInput = document.querySelector('input[name="invoiceRating"]:checked');
    const rating = ratingInput ? ratingInput.value : 0;
    const comment = document.getElementById('reviewText').value.trim();
    const msgEl = document.getElementById('reviewMsg');

    if (!rating) {
        msgEl.className = 'review-msg error';
        msgEl.textContent = 'Please select a star rating.';
        msgEl.style.display = 'block';
        return;
    }

    const btn = document.querySelector('.review-submit-btn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Submitting...';

    const fd = new FormData();
    fd.append('order_id', '<?php echo $orderId; ?>');
    fd.append('product_id', '<?php echo (int)($itemQuery ? mysqli_fetch_assoc(mysqli_query($conn, "SELECT product_id FROM order_items WHERE order_id=$orderId LIMIT 1"))["product_id"] ?? 0 : 0); ?>');
    fd.append('rating', rating);
    fd.append('comment', comment);

    fetch('admin/actions/review_action.php', { method: 'POST', body: fd })
        .then(r => r.text())
        .then(data => {
            if (data.trim() === 'success' || data.includes('success')) {
                msgEl.className = 'review-msg success';
                msgEl.textContent = '✓ Thank you! Your review has been submitted.';
                document.querySelector('.review-stars').style.pointerEvents = 'none';
                document.getElementById('reviewText').disabled = true;
                btn.style.display = 'none';
            } else {
                msgEl.className = 'review-msg error';
                msgEl.textContent = 'Unable to submit review. Please try again.';
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Review';
            }
            msgEl.style.display = 'block';
        })
        .catch(() => {
            msgEl.className = 'review-msg error';
            msgEl.textContent = 'Network error. Please try again.';
            msgEl.style.display = 'block';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Submit Review';
        });
}
</script>

</body>
</html>