<?php
session_start();
require_once __DIR__ . '/admin/includes/db.php';
if (!function_exists('qf_escape')) {
    function qf_escape($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
}
if (!function_exists('qf_int')) {
    function qf_int($v): int { return (int)($v ?? 0); }
}

// Auto-create table
mysqli_query($conn, "
    CREATE TABLE IF NOT EXISTS saved_addresses (
        id             INT AUTO_INCREMENT PRIMARY KEY,
        user_id        INT          NOT NULL,
        label          VARCHAR(50)  NOT NULL DEFAULT 'Home',
        recipient_name VARCHAR(150) NOT NULL,
        phone          VARCHAR(20)  NOT NULL,
        address_line1  VARCHAR(250) NOT NULL,
        address_line2  VARCHAR(250) DEFAULT '',
        city           VARCHAR(100) NOT NULL,
        state          VARCHAR(100) NOT NULL,
        pincode        VARCHAR(10)  NOT NULL,
        country        VARCHAR(100) NOT NULL DEFAULT 'India',
        landmark       VARCHAR(200) DEFAULT '',
        is_default     TINYINT(1)  DEFAULT 0,
        is_active      TINYINT(1)  DEFAULT 1,
        created_at     TIMESTAMP   DEFAULT CURRENT_TIMESTAMP,
        INDEX(user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

if (!isset($_SESSION['email'])) {
    header('Location: login.php');
    exit;
}

$email  = mysqli_real_escape_string($conn, $_SESSION['email']);
$userQ  = mysqli_query($conn, "SELECT id, name, email, number FROM quigly_table WHERE email='{$email}' LIMIT 1");
$user   = mysqli_fetch_assoc($userQ);
if (!$user) {
    session_destroy();
    header('Location: login.php');
    exit;
}
$userId = (int)$user['id'];

$addrResult = mysqli_query($conn, "SELECT * FROM saved_addresses WHERE user_id={$userId} AND is_active=1 ORDER BY is_default DESC, id DESC");
$addressCount = $addrResult ? mysqli_num_rows($addrResult) : 0;

$editId = (int)($_GET['edit'] ?? 0);
$edit   = null;
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
        body{background:radial-gradient(circle at top left,#dbeafe,transparent 22%),radial-gradient(circle at top right,#ddd6fe,transparent 22%),#f8fafc;font-family:Inter,sans-serif}
        .page-shell{max-width:1280px;margin:0 auto;padding:30px 16px 60px}
        .hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 50%,#7c3aed 100%);color:#fff;border-radius:30px;padding:30px;box-shadow:0 24px 60px rgba(15,23,42,.18)}
        .hero h1{font-weight:900}
        .cardx{background:#fff;border:1px solid #e5e7eb;border-radius:24px;box-shadow:0 10px 30px rgba(15,23,42,.05)}
        .address-card{border:1px solid #e5e7eb;border-radius:20px;padding:18px;background:#fff;transition:.2s}
        .address-card:hover{border-color:#c4b5fd;box-shadow:0 8px 22px rgba(124,58,237,.08)}
        .default-badge{background:#dcfce7;color:#166534;border-radius:999px;padding:.3rem .7rem;font-size:.78rem;font-weight:800}
        .muted{color:#64748b}
        .label-tag{display:inline-flex;align-items:center;gap:5px;padding:3px 12px;border-radius:999px;font-size:.78rem;font-weight:700}
        .label-home{background:#ede9fe;color:#7c3aed}
        .label-office,.label-work{background:#dbeafe;color:#2563eb}
        .label-other{background:#f1f5f9;color:#475569}
        .form-control:focus{border-color:#7c3aed;box-shadow:0 0 0 4px rgba(124,58,237,.08)}
        .btn-primary-custom{background:linear-gradient(135deg,#7c3aed,#2563eb);color:#fff;border:none;border-radius:14px;padding:12px;font-weight:700;font-size:1rem;width:100%;transition:.2s}
        .btn-primary-custom:hover{opacity:.88;color:#fff}
        .error-msg{background:#fee2e2;color:#dc2626;border-radius:12px;padding:12px 16px;font-weight:600;font-size:.88rem;margin-bottom:16px}
    </style>
</head>
<body>
<div class="page-shell">
    <div class="hero mb-4">
        <div class="d-flex justify-content-between flex-wrap gap-3 align-items-end">
            <div>
                <div class="badge bg-white text-dark rounded-pill mb-3"><i class="fas fa-map-marker-alt me-1"></i>Address Book</div>
                <h1 class="display-6 mb-2">Saved Delivery Addresses</h1>
                <p class="mb-0 text-white-50">Set a default for faster checkout, switch any time.</p>
            </div>
            <a href="index.php" class="btn btn-light fw-bold"><i class="fa-solid fa-arrow-left me-2"></i>Back to Store</a>
        </div>
    </div>

    <?php if (isset($_GET['error'])): ?>
    <div class="error-msg"><i class="fas fa-exclamation-circle me-2"></i>Please fill in all required fields before saving.</div>
    <?php endif; ?>

    <div class="row g-4">
        <!-- ── ADD / EDIT FORM ── -->
        <div class="col-lg-5">
            <div class="cardx p-4">
                <h4 class="fw-black mb-1"><?= $edit ? 'Edit Address' : 'Add New Address' ?></h4>
                <p class="muted small mb-3"><?= $edit ? 'Update the details below.' : 'Fill in your delivery details.' ?></p>

                <form method="POST" action="address_action.php">
                    <input type="hidden" name="id" value="<?= qf_int($edit['id'] ?? 0) ?>">

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Label</label>
                        <input name="label" class="form-control" value="<?= qf_escape($edit['label'] ?? 'Home') ?>" placeholder="Home / Office / Other" required>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-7 mb-3">
                            <label class="form-label fw-semibold">Recipient Name *</label>
                            <input name="recipient_name" class="form-control" value="<?= qf_escape($edit['recipient_name'] ?? $user['name']) ?>" required>
                        </div>
                        <div class="col-md-5 mb-3">
                            <label class="form-label fw-semibold">Phone *</label>
                            <input name="phone" class="form-control" value="<?= qf_escape($edit['phone'] ?? $user['number']) ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 1 *</label>
                        <input name="address_line1" class="form-control" value="<?= qf_escape($edit['address_line1'] ?? '') ?>" placeholder="House/Flat no, Street, Colony" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Address Line 2</label>
                        <input name="address_line2" class="form-control" value="<?= qf_escape($edit['address_line2'] ?? '') ?>" placeholder="Area, Sector (optional)">
                    </div>
                    <div class="row g-3">
                        <div class="col-4 mb-3">
                            <label class="form-label fw-semibold">City *</label>
                            <input name="city" class="form-control" value="<?= qf_escape($edit['city'] ?? '') ?>" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label fw-semibold">State *</label>
                            <input name="state" class="form-control" value="<?= qf_escape($edit['state'] ?? '') ?>" required>
                        </div>
                        <div class="col-4 mb-3">
                            <label class="form-label fw-semibold">PIN *</label>
                            <input name="pincode" class="form-control" value="<?= qf_escape($edit['pincode'] ?? '') ?>" maxlength="6" required>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Landmark</label>
                            <input name="landmark" class="form-control" value="<?= qf_escape($edit['landmark'] ?? '') ?>" placeholder="Near...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-semibold">Country</label>
                            <input name="country" class="form-control" value="<?= qf_escape($edit['country'] ?? 'India') ?>">
                        </div>
                    </div>
                    <div class="form-check mb-4">
                        <input class="form-check-input" type="checkbox" name="is_default" id="defaultAddr" style="accent-color:#7c3aed" <?= (($edit['is_default'] ?? 0) ? 'checked' : '') ?>>
                        <label class="form-check-label fw-semibold" for="defaultAddr">Set as default delivery address</label>
                    </div>
                    <button type="submit" class="btn-primary-custom">
                        <i class="fas fa-save me-2"></i><?= $edit ? 'Update Address' : 'Save Address' ?>
                    </button>
                    <?php if ($edit): ?>
                    <a href="saved_addresses.php" class="btn btn-outline-secondary w-100 mt-2">Cancel</a>
                    <?php endif; ?>
                </form>
            </div>
        </div>

        <!-- ── ADDRESS LIST ── -->
        <div class="col-lg-7">
            <div class="cardx p-4">
                <div class="d-flex justify-content-between flex-wrap gap-2 align-items-center mb-4">
                    <div>
                        <h4 class="fw-black mb-1">Your Saved Addresses</h4>
                        <p class="muted small mb-0">Your default address is used automatically at checkout.</p>
                    </div>
                    <span class="badge text-bg-primary rounded-pill fs-6"><?= $addressCount ?> saved</span>
                </div>
                <div class="row g-3">
                    <?php if ($addressCount > 0): ?>
                        <?php while ($a = mysqli_fetch_assoc($addrResult)): ?>
                        <div class="col-12">
                            <div class="address-card">
                                <div class="d-flex justify-content-between flex-wrap gap-2 align-items-start">
                                    <div>
                                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                            <?php
                                            $lbl = strtolower($a['label'] ?? 'home');
                                            $lblClass = in_array($lbl, ['home']) ? 'label-home' : (in_array($lbl, ['office','work']) ? 'label-work' : 'label-other');
                                            $lblIcon  = $lbl === 'home' ? 'fa-home' : ($lbl === 'office' || $lbl === 'work' ? 'fa-briefcase' : 'fa-map-pin');
                                            ?>
                                            <span class="label-tag <?= $lblClass ?>"><i class="fas <?= $lblIcon ?>"></i><?= qf_escape($a['label']) ?></span>
                                            <?php if ((int)$a['is_default'] === 1): ?>
                                                <span class="default-badge"><i class="fas fa-star me-1"></i>Default</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="fw-bold fs-6"><?= qf_escape($a['recipient_name']) ?></div>
                                        <div class="muted small"><i class="fas fa-phone-alt me-1"></i><?= qf_escape($a['phone']) ?></div>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a class="btn btn-sm btn-outline-primary" href="?edit=<?= qf_int($a['id']) ?>"><i class="fas fa-edit me-1"></i>Edit</a>
                                        <a class="btn btn-sm btn-outline-danger" href="address_action.php?delete=1&id=<?= qf_int($a['id']) ?>" onclick="return confirm('Remove this address?')"><i class="fas fa-trash me-1"></i></a>
                                        <?php if ((int)$a['is_default'] !== 1): ?>
                                            <a class="btn btn-sm btn-dark" href="address_action.php?set_default=1&id=<?= qf_int($a['id']) ?>"><i class="fas fa-star me-1"></i>Set Default</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <hr class="my-2">
                                <div class="small text-muted">
                                    <i class="fas fa-map-marker-alt me-1 text-danger"></i>
                                    <?= qf_escape($a['address_line1']) ?><?= !empty($a['address_line2']) ? ', '.qf_escape($a['address_line2']) : '' ?>,
                                    <?= qf_escape($a['city']) ?>, <?= qf_escape($a['state']) ?> — <?= qf_escape($a['pincode']) ?>, <?= qf_escape($a['country']) ?>
                                    <?php if (!empty($a['landmark'])): ?>
                                        <br><i class="fas fa-landmark me-1"></i>Near: <?= qf_escape($a['landmark']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="text-center py-5">
                                <i class="fas fa-map-marked-alt" style="font-size:3rem;color:#c4b5fd;margin-bottom:16px;display:block;"></i>
                                <h5 class="fw-bold text-muted">No saved addresses yet</h5>
                                <p class="text-muted small">Add your first delivery address using the form on the left.</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
