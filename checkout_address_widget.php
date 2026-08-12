<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/admin/includes/db.php';
require_once __DIR__ . '/admin/includes/feature_utils.php';

// Auto-create saved_addresses table if not exists
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

$checkoutDefaultAddress = '';
$checkoutAddressOptions = [];

if (!empty($_SESSION['email'])) {
    $email = mysqli_real_escape_string($conn, $_SESSION['email']);
    $userQ = mysqli_query($conn, "SELECT id FROM quigly_table WHERE email='{$email}' LIMIT 1");
    if ($userRow = mysqli_fetch_assoc($userQ)) {
        $userId = (int)$userRow['id'];
        $addrQ = mysqli_query($conn, "SELECT * FROM saved_addresses WHERE user_id={$userId} AND is_active=1 ORDER BY is_default DESC, id DESC");
        if ($addrQ && mysqli_num_rows($addrQ) > 0) {
            while ($a = mysqli_fetch_assoc($addrQ)) {
                $full = trim(
                    $a['recipient_name'] . ' | ' .
                    $a['address_line1'] .
                    (!empty($a['address_line2']) ? ', ' . $a['address_line2'] : '') .
                    ', ' . $a['city'] . ', ' . $a['state'] . ' - ' . $a['pincode']
                );
                $checkoutAddressOptions[] = $a;
                if ((int)$a['is_default'] === 1 && $checkoutDefaultAddress === '') {
                    $checkoutDefaultAddress = $full;
                }
            }
            if ($checkoutDefaultAddress === '') {
                $a = $checkoutAddressOptions[0];
                $checkoutDefaultAddress = trim(
                    $a['recipient_name'] . ' | ' .
                    $a['address_line1'] .
                    (!empty($a['address_line2']) ? ', ' . $a['address_line2'] : '') .
                    ', ' . $a['city'] . ', ' . $a['state'] . ' - ' . $a['pincode']
                );
            }
        }
    }
}
?>
<div class="mt-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-semibold mb-0">Saved Addresses</label>
        <a href="saved_addresses.php" class="small text-decoration-none">Manage</a>
    </div>
    <select id="savedAddressSelect" class="form-select checkout-input mb-2">
        <option value="">Choose a saved address</option>
        <?php foreach ($checkoutAddressOptions as $addr): ?>
            <?php
                $pretty = trim(
                    $addr['recipient_name'] . ' | ' .
                    $addr['address_line1'] .
                    (!empty($addr['address_line2']) ? ', ' . $addr['address_line2'] : '') .
                    ', ' . $addr['city'] . ', ' . $addr['state'] . ' - ' . $addr['pincode']
                );
            ?>
            <option value="<?= qf_escape($pretty) ?>" <?= ((int)$addr['is_default'] === 1) ? 'selected' : '' ?>>
                <?= qf_escape($addr['label']) ?><?= ((int)$addr['is_default'] === 1) ? ' (Default)' : '' ?>
            </option>
        <?php endforeach; ?>
    </select>
    <div class="small text-muted mb-3">Your default address will auto-fill the checkout box.</div>
</div>

<script>
(function() {
    const select = document.getElementById('savedAddressSelect');
    const textarea = document.getElementById('checkoutAddress');
    if (!select || !textarea) return;
    const defaultValue = <?= json_encode($checkoutDefaultAddress, JSON_UNESCAPED_SLASHES) ?>;
    if (defaultValue && !textarea.value.trim()) {
        textarea.value = defaultValue;
    }
    select.addEventListener('change', function() {
        if (this.value) {
            textarea.value = this.value;
        }
    });
})();
</script>
