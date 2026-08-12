<?php
session_start();
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/admin/includes/feature_utils.php';

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

$userId = (int)$user['id'];
$addresses = mysqli_query($conn, "SELECT * FROM saved_addresses WHERE user_id={$userId} ORDER BY is_default DESC, id DESC");

$editId = (int)($_GET['edit'] ?? 0);
$edit = null;
if ($editId > 0) {
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM saved_addresses WHERE id={$editId} AND user_id={$userId} LIMIT 1"));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Addresses | Quigly</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body{background:linear-gradient(180deg,#f8fafc,#eef2ff);font-family:Inter,sans-serif}
        .page-shell{max-width:1320px;margin:0 auto;padding:30px 16px 60px}
        .hero{background:linear-gradient(135deg,#0f172a,#1e1b4b 50%,#7c3aed);color:#fff;border-radius:30px;padding:30px;box-shadow:0 24px 60px rgba(15,23,42,.18)}
        .hero h1{font-weight:900}
        .cardx{background:#fff;border:1px solid #e5e7eb;border-radius:24px;box-shadow:0 10px 30px rgba(15,23,42,.05)}
        .address-card{border:1px solid #e5e7eb;border-radius:20px;padding:18px;background:#fff}
        .default-badge{background:#dcfce7;color:#166534;border-radius:999px;padding:.3rem .7rem;font-size:.78rem;font-weight:800}
        .muted{color:#64748b}
    </style>
</head>
<body>
<div class="page-shell">
    <div class="hero mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-3 align-items-end">
            <div>
                <div class="badge bg-white text-dark rounded-pill mb-3">Address Book</div>
                <h1 class="display-6 mb-2">Save, choose, and reuse delivery addresses</h1>
                <p class="mb-0 text-white-50">Set a default address for faster checkout, then switch anytime.</p>
            </div>
            <a href="index.php" class="btn btn-light fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Back to Store</a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="cardx p-4">
                <h4 class="fw-black mb-3"><?= $edit ? 'Edit Address' : 'Add New Address' ?></h4>
                <form method="POST" action="address_action.php">
                    <input type="hidden" name="id" value="<?= qf_int($edit['id'] ?? 0) ?>">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Label</label>
                        <input name="label" class="form-control" value="<?= qf_escape($edit['label'] ?? 'Home') ?>" placeholder="Home / Office / Other" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Recipient Name</label>
                            <input name="recipient_name" class="form-control" value="<?= qf_escape($edit['recipient_name'] ?? $user['name']) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input name="phone" class="form-control" value="<?= qf_escape($edit['phone'] ?? $user['number']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 1</label>
                        <input name="address_line1" class="form-control" value="<?= qf_escape($edit['address_line1'] ?? '') ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 2</label>
                        <input name="address_line2" class="form-control" value="<?= qf_escape($edit['address_line2'] ?? '') ?>">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input name="city" class="form-control" value="<?= qf_escape($edit['city'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">State</label>
                            <input name="state" class="form-control" value="<?= qf_escape($edit['state'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">PIN</label>
                            <input name="pincode" class="form-control" value="<?= qf_escape($edit['pincode'] ?? '') ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Landmark</label>
                        <input name="landmark" class="form-control" value="<?= qf_escape($edit['landmark'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Country</label>
                        <input name="country" class="form-control" value="<?= qf_escape($edit['country'] ?? 'India') ?>">
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_default" id="defaultAddr" <?= (($edit['is_default'] ?? 0) ? 'checked' : '') ?>>
                        <label class="form-check-label" for="defaultAddr">Set as default address</label>
                    </div>
                    <button class="btn btn-dark w-100 py-3 fw-bold">Save Address</button>
                </form>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="cardx p-4">
                <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-3">
                    <div>
                        <h4 class="fw-black mb-1">Your Saved Addresses</h4>
                        <p class="muted mb-0">Choose a default address for checkout.</p>
                    </div>
                    <span class="badge text-bg-primary"><?= $addressCount ?> saved</span>
                </div>
                <div class="row g-3">
                    <?php $addressCount = ($addresses instanceof mysqli_result) ? mysqli_num_rows($addresses) : 0; ?>
                    <?php if ($addressCount > 0): ?>
                        <?php while ($a = mysqli_fetch_assoc($addresses)): ?>
                            <div class="col-12">
                                <div class="address-card">
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <div>
                                            <div class="fw-bold fs-5"><?= qf_escape($a['label']) ?> <?php if ((int)$a['is_default'] === 1): ?><span class="default-badge ms-2">Default</span><?php endif; ?></div>
                                            <div class="muted"><?= qf_escape($a['recipient_name']) ?> • <?= qf_escape($a['phone']) ?></div>
                                        </div>
                                        <div class="d-flex gap-2 flex-wrap">
                                            <a class="btn btn-sm btn-outline-primary" href="?edit=<?= qf_int($a['id']) ?>">Edit</a>
                                            <a class="btn btn-sm btn-outline-danger" href="address_action.php?delete=1&id=<?= qf_int($a['id']) ?>" onclick="return confirm('Delete this address?')">Delete</a>
                                            <?php if ((int)$a['is_default'] !== 1): ?>
                                                <a class="btn btn-sm btn-dark" href="address_action.php?set_default=1&id=<?= qf_int($a['id']) ?>">Set Default</a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="small">
                                        <?= qf_escape($a['address_line1']) ?><?php if (!empty($a['address_line2'])): ?>, <?= qf_escape($a['address_line2']) ?><?php endif; ?>, <?= qf_escape($a['city']) ?>, <?= qf_escape($a['state']) ?> - <?= qf_escape($a['pincode']) ?>, <?= qf_escape($a['country']) ?>
                                    </div>
                                    <?php if (!empty($a['landmark'])): ?>
                                        <div class="small text-muted mt-1">Landmark: <?= qf_escape($a['landmark']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="alert alert-light border mb-0">No saved addresses yet. Add your first address on the left.</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
