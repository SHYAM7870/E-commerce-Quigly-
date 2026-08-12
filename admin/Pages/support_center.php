<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/feature_utils.php';

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email = mysqli_real_escape_string($conn, $_SESSION['email']);
$userQ = mysqli_query($conn, "SELECT id, name, email, number FROM quigly_table WHERE email='{$email}' LIMIT 1");
$user = mysqli_fetch_assoc($userQ);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$ticketQ = mysqli_query($conn, "SELECT * FROM support_tickets WHERE user_email='{$email}' ORDER BY id DESC LIMIT 6");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Support Center | Quigly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body{background:radial-gradient(circle at top left,#dbeafe,transparent 22%),radial-gradient(circle at top right,#ddd6fe,transparent 22%),#f8fafc;font-family:Inter,sans-serif}
        .wrap{max-width:1280px;margin:0 auto;padding:28px 16px 60px}
        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 45%,#7c3aed 100%);color:#fff;border-radius:32px;padding:30px;box-shadow:0 24px 60px rgba(15,23,42,.18)}
        .glass{background:rgba(255,255,255,.82);backdrop-filter:blur(14px);border:1px solid rgba(255,255,255,.3);border-radius:26px;box-shadow:0 16px 44px rgba(15,23,42,.08)}
        .mini{font-size:.9rem;color:#64748b}
        .ticket{border:1px solid #e5e7eb;border-radius:18px;padding:14px 16px;background:#fff}
    </style>
</head>
<body>
<div class="wrap">
    <div class="hero mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-3 align-items-end">
            <div>
                <div class="badge bg-white text-dark rounded-pill mb-3">24/7 Support</div>
                <h1 class="display-6 fw-bold mb-2">Premium help desk for every order</h1>
                <p class="mb-0 text-white-50">Ask about orders, delivery, payments, account issues, or product help.</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="../../index.php" class="btn btn-light fw-bold">Back to Store</a>
                <a href="../../index.php?section=orders" class="btn btn-outline-light fw-bold">My Orders</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="glass p-4 h-100">
                <h4 class="fw-bold mb-3">Need quick help?</h4>
                <div class="mb-3">
                    <div class="fw-semibold">Fast response</div>
                    <div class="mini">Most questions are answered within working hours.</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold">Order tracking</div>
                    <div class="mini">Attach order details and get specific help.</div>
                </div>
                <div class="mb-3">
                    <div class="fw-semibold">Account support</div>
                    <div class="mini">Login, address, profile, and payment issues.</div>
                </div>
                <div class="alert alert-primary mb-0">
                    Email: support@quigly.com<br>
                    Phone: +91 00000 00000
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="glass p-4">
                <h4 class="fw-bold mb-2">Create Ticket</h4>
                <p class="mini mb-3">Tell us what is wrong and we will respond from the admin panel.</p>
                <form method="POST" action="../actions/support_action.php">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Subject</label>
                        <input type="text" name="subject" class="form-control form-control-lg" placeholder="Order issue, refund request, account problem..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Message</label>
                        <textarea name="message" class="form-control" rows="7" placeholder="Describe your issue in detail..." required></textarea>
                    </div>
                    <button class="btn btn-dark w-100 py-3 fw-bold">Submit Ticket</button>
                </form>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="glass p-4 h-100">
                <h5 class="fw-bold mb-3">Recent Tickets</h5>
                <?php if ($ticketQ && mysqli_num_rows($ticketQ) > 0): ?>
                    <div class="d-grid gap-3">
                        <?php while ($t = mysqli_fetch_assoc($ticketQ)): ?>
                            <div class="ticket">
                                <div class="fw-bold"><?= qf_escape($t['subject']) ?></div>
                                <div class="mini"><?= qf_escape($t['message']) ?></div>
                                <div class="mt-2"><span class="badge text-bg-secondary"><?= qf_escape($t['status'] ?? 'pending') ?></span></div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php else: ?>
                    <div class="alert alert-light border mb-0">No tickets yet.</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</body>
</html>
