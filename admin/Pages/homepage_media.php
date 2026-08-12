<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['email'])) {
    header("Location: /Quigly/login.php?error=Unauthorized+Access");
    exit;
}
// Extra admin check via DB
include_once __DIR__ . "/../includes/db.php";
$_hm_email = mysqli_real_escape_string($conn, $_SESSION['email']);
$_hm_user  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT admin FROM quigly_table WHERE email='{$_hm_email}' LIMIT 1"));
if (!$_hm_user || (int)$_hm_user['admin'] !== 1) {
    header("Location: /Quigly/login.php?error=Unauthorized+Access");
    exit;
}

include_once __DIR__ . "/../includes/config.php";
include_once __DIR__ . "/../includes/db.php";

if (!function_exists('hm_escape')) {
    function hm_escape ($value): string
    {
        return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('hm_int')) {
    function hm_int ($value): int
    {
        return (int) ($value ?? 0);
    }
}

if (!function_exists('hm_allowed_type')) {
    function hm_allowed_type (string $type): string
    {
        $type = strtolower(trim($type));
        return in_array($type, ['banner', 'brand', 'logo'], true) ? $type : 'banner';
    }
}

if (!function_exists('hm_asset_url')) {
    function hm_asset_url (?string $path): string
    {
        $path = trim((string) $path);
        if ($path === '') {
            return 'https://placehold.co/900x600?text=No+Image';
        }
        if (preg_match('#^(https?://)#i', $path)) {
            return $path;
        }
        return '../../' . ltrim($path, '/');
    }
}

if (!function_exists('hm_delete_file')) {
    function hm_delete_file (?string $relativePath): void
    {
        $relativePath = trim((string) $relativePath);
        if ($relativePath === '') {
            return;
        }

        $absolute = __DIR__ . '/../../' . ltrim($relativePath, '/');
        if (is_file($absolute)) {
            @unlink($absolute);
        }
    }
}

if (!function_exists('hm_upload_image')) {
    function hm_upload_image (string $fieldName, string &$error): ?string
    {
        if (!isset($_FILES[$fieldName])) {
            return null;
        }

        $file = $_FILES[$fieldName];

        if ($file['error'] == UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] != UPLOAD_ERR_OK) {
            $error = 'Image upload failed.';
            return null;
        }

        $allowedExt = [
            'jpg',
            'jpeg',
            'png',
            'gif',
            'webp',
            'bmp',
            'svg',
            'ico',
            'tif',
            'tiff',
            'avif',
            'jfif'
        ];

        $tmpName = $file['tmp_name'];
        $size = (int) $file['size'];

        if ($size <= 0) {
            $error = 'Uploaded file is empty.';
            return null;
        }

        if ($size > 5 * 1024 * 1024) {
            $error = 'Image size must be 5MB or less.';
            return null;
        }
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt, true)) {
            $error = 'Unsupported image format.';
            return null;
        }

        $uploadDir = __DIR__ . '/../../uploads/homepage_media/';

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            $error = 'Unable to create upload folder.';
            return null;
        }

        $safeName = preg_replace(
            '/[^a-zA-Z0-9_-]+/',
            '_',
            pathinfo($file['name'], PATHINFO_FILENAME)
        );

        $newName = 'hm_' . $safeName . '_' . uniqid() . '.' . $ext;

        $absolutePath = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $absolutePath)) {
            $error = 'Failed to save image.';
            return null;
        }

        return 'uploads/homepage_media/' . $newName;
    }
}

$tab = hm_allowed_type($_GET['tab'] ?? 'banner');
$editId = isset($_GET['edit']) ? (int) $_GET['edit'] : 0;
$actionId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$msg = trim((string) ($_GET['msg'] ?? ''));
$error = trim((string) ($_GET['error'] ?? ''));

if (($_GET['action'] ?? '') === 'delete' && $actionId > 0) {
    $stmt = mysqli_prepare($conn, "SELECT image FROM homepage_media WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $actionId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if ($row) {
        hm_delete_file($row['image'] ?? null);

        $stmt = mysqli_prepare($conn, "DELETE FROM homepage_media WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $actionId);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: homepage_media.php?tab={$tab}&msg=" . urlencode('Media deleted successfully.'));
        exit;
    }

    header("Location: homepage_media.php?tab={$tab}&error=" . urlencode('Record not found.'));
    exit;
}

if (($_GET['action'] ?? '') === 'toggle' && $actionId > 0) {
    $stmt = mysqli_prepare($conn, "UPDATE homepage_media SET status = IF(status = 1, 0, 1) WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $actionId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    header("Location: homepage_media.php?tab={$tab}&msg=" . urlencode('Status updated.'));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_media'])) {
    $id = (int) ($_POST['id'] ?? 0);
    $type = hm_allowed_type($_POST['type'] ?? $tab);
    $title = trim((string) ($_POST['title'] ?? ''));
    $subtitle = trim((string) ($_POST['subtitle'] ?? ''));
    $link = trim((string) ($_POST['link'] ?? ''));
    $buttonText = trim((string) ($_POST['button_text'] ?? ''));
    $status = isset($_POST['status']) ? 1 : 0;
    $sortOrder = (int) ($_POST['sort_order'] ?? 0);

    if ($type === 'logo' && $title === '') {
        $title = 'Main Logo';
    }

    if ($type !== 'logo' && $title === '') {
        header("Location: homepage_media.php?tab={$type}&error=" . urlencode('Title is required.'));
        exit;
    }

    $existingImage = '';
    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "SELECT image FROM homepage_media WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $existingRow = $result ? mysqli_fetch_assoc($result) : null;
        mysqli_stmt_close($stmt);

        if (!$existingRow) {
            header("Location: homepage_media.php?tab={$type}&error=" . urlencode('Record not found.'));
            exit;
        }

        $existingImage = (string) ($existingRow['image'] ?? '');
    }

    $uploadError = '';
    $newImage = hm_upload_image('image', $uploadError);
    if ($uploadError !== '') {
        header("Location: homepage_media.php?tab={$type}&error=" . urlencode($uploadError));
        exit;
    }

    $imagePath = $newImage ?: $existingImage;

    if ($imagePath === '') {
        header("Location: homepage_media.php?tab={$type}&error=" . urlencode('Please upload an image.'));
        exit;
    }

    if ($id > 0) {
        $stmt = mysqli_prepare($conn, "
            UPDATE homepage_media
            SET type = ?, title = ?, subtitle = ?, image = ?, link = ?, button_text = ?, status = ?, sort_order = ?
            WHERE id = ?
        ");
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssiii",
            $type,
            $title,
            $subtitle,
            $imagePath,
            $link,
            $buttonText,
            $status,
            $sortOrder,
            $id
        );
    } else {
        $stmt = mysqli_prepare($conn, "
            INSERT INTO homepage_media (type, title, subtitle, image, link, button_text, status, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ");
        mysqli_stmt_bind_param(
            $stmt,
            "ssssssii",
            $type,
            $title,
            $subtitle,
            $imagePath,
            $link,
            $buttonText,
            $status,
            $sortOrder
        );
    }

    if (!mysqli_stmt_execute($stmt)) {
        $saveError = mysqli_stmt_error($stmt) ?: 'Database save failed.';
        mysqli_stmt_close($stmt);
        header("Location: homepage_media.php?tab={$type}&error=" . urlencode($saveError));
        exit;
    }

    if ($id > 0 && $newImage && $existingImage && $existingImage !== $newImage) {
        hm_delete_file($existingImage);
    }

    mysqli_stmt_close($stmt);

    header("Location: homepage_media.php?tab={$type}&msg=" . urlencode('Media saved successfully.'));
    exit;
}

$editRow = null;
if ($editId > 0) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM homepage_media WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "i", $editId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $editRow = $result ? mysqli_fetch_assoc($result) : null;
    mysqli_stmt_close($stmt);

    if ($editRow) {
        $tab = hm_allowed_type($editRow['type'] ?? $tab);
    }
}

$bannerStats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status = 1) AS active_total FROM homepage_media WHERE type='banner'")) ?: ['total' => 0, 'active_total' => 0];
$brandStats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status = 1) AS active_total FROM homepage_media WHERE type='brand'")) ?: ['total' => 0, 'active_total' => 0];
$logoStats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status = 1) AS active_total FROM homepage_media WHERE type='logo'")) ?: ['total' => 0, 'active_total' => 0];
$totalStats = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total, SUM(status = 1) AS active_total FROM homepage_media")) ?: ['total' => 0, 'active_total' => 0];

$listStmt = mysqli_prepare($conn, "SELECT * FROM homepage_media WHERE type = ? ORDER BY sort_order ASC, id DESC");
mysqli_stmt_bind_param($listStmt, "s", $tab);
mysqli_stmt_execute($listStmt);
$listResult = mysqli_stmt_get_result($listStmt);

include_once __DIR__ . "/../includes/header.php";
include_once __DIR__ . "/../includes/sidebar.php";
?>
<style>
    .hm-page {
        padding: 28px;
        background: linear-gradient(180deg, #f6f8fc 0%, #eef2ff 100%);
        min-height: 100vh;
    }

    .hm-hero {
        position: relative;
        overflow: hidden;
        border-radius: 28px;
        padding: 28px;
        color: #fff;
        background:
            radial-gradient(circle at top left, rgba(255, 255, 255, .18), transparent 24%),
            radial-gradient(circle at bottom right, rgba(255, 255, 255, .12), transparent 22%),
            linear-gradient(135deg, #0f172a 0%, #4f46e5 55%, #7c3aed 100%);
        box-shadow: 0 24px 60px rgba(79, 70, 229, .18);
    }

    .hm-hero::after {
        content: "";
        position: absolute;
        inset: auto -80px -120px auto;
        width: 300px;
        height: 300px;
        border-radius: 50%;
        background: rgba(255, 255, 255, .08);
        filter: blur(2px);
    }

    .hm-hero h3 {
        font-size: 1.8rem;
        font-weight: 900;
        margin-bottom: 8px;
    }

    .hm-hero p {
        margin: 0;
        color: rgba(255, 255, 255, .85);
        max-width: 720px;
    }

    .hm-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 18px;
    }

    .hm-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        border: 1px solid rgba(255, 255, 255, .12);
        color: #fff;
        font-size: .88rem;
        font-weight: 700;
        backdrop-filter: blur(10px);
    }

    .hm-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 16px;
        margin-top: 20px;
    }

    .hm-stat {
        background: rgba(255, 255, 255, .82);
        border: 1px solid rgba(148, 163, 184, .18);
        border-radius: 22px;
        padding: 18px;
        box-shadow: 0 12px 28px rgba(15, 23, 42, .06);
    }

    .hm-stat .num {
        font-size: 1.7rem;
        font-weight: 900;
        color: #4f46e5;
        line-height: 1;
    }

    .hm-stat .label {
        margin-top: 6px;
        color: #64748b;
        font-size: .92rem;
        font-weight: 600;
    }

    .hm-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 26px;
        box-shadow: 0 10px 28px rgba(15, 23, 42, .06);
        overflow: hidden;
    }

    .hm-card-hd {
        padding: 18px 20px;
        border-bottom: 1px solid #eef2f7;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
    }

    .hm-card-hd h5 {
        margin: 0;
        font-weight: 900;
        color: #0f172a;
    }

    .hm-card-bd {
        padding: 20px;
    }

    .hm-tabs {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .hm-tab {
        padding: 10px 14px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        text-decoration: none;
        font-weight: 800;
        color: #334155;
        background: #f8fafc;
        transition: .2s ease;
    }

    .hm-tab:hover {
        transform: translateY(-1px);
        color: #4f46e5;
        border-color: #c7d2fe;
    }

    .hm-tab.active {
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 12px 24px rgba(124, 58, 237, .22);
    }

    .form-label {
        font-weight: 700;
        color: #334155;
    }

    .form-control,
    .form-select {
        border-radius: 14px;
        padding: .78rem .95rem;
        border: 1px solid #dbe4ee;
        box-shadow: none;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 .2rem rgba(124, 58, 237, .12);
    }

    .hm-help {
        font-size: .86rem;
        color: #64748b;
        margin-top: 6px;
    }

    .hm-preview {
        width: 100%;
        height: 220px;
        object-fit: cover;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .hm-table {
        margin: 0;
    }

    .hm-table th {
        font-size: .82rem;
        text-transform: uppercase;
        letter-spacing: .08em;
        color: #64748b;
        border-bottom: 1px solid #eef2f7 !important;
        white-space: nowrap;
    }

    .hm-table td {
        vertical-align: middle;
    }

    .hm-thumb {
        width: 88px;
        height: 58px;
        object-fit: cover;
        border-radius: 14px;
        border: 1px solid #e5e7eb;
        background: #f8fafc;
    }

    .hm-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .hm-action {
        padding: 8px 12px;
        border-radius: 12px;
        text-decoration: none;
        font-weight: 800;
        font-size: .85rem;
        display: inline-flex;
        align-items: center;
        gap: 7px;
    }

    .hm-action.edit {
        background: #eef2ff;
        color: #4338ca;
    }

    .hm-action.toggle {
        background: #ecfeff;
        color: #0f766e;
    }

    .hm-action.delete {
        background: #fef2f2;
        color: #b91c1c;
    }

    .hm-action:hover {
        filter: brightness(.98);
    }

    .hm-empty {
        padding: 40px 18px;
        text-align: center;
        color: #64748b;
        font-weight: 700;
    }

    .hm-soft-note {
        display: inline-flex;
        padding: 6px 10px;
        border-radius: 999px;
        background: #f1f5f9;
        color: #475569;
        font-size: .78rem;
        font-weight: 700;
    }

    @media (max-width: 1199px) {
        .hm-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 767px) {
        .hm-page {
            padding: 18px;
        }

        .hm-hero {
            padding: 22px;
            border-radius: 22px;
        }

        .hm-grid {
            grid-template-columns: 1fr;
        }

        .hm-card-hd {
            padding: 16px;
        }

        .hm-card-bd {
            padding: 16px;
        }
    }
</style>

<div class="hm-page">
    <div class="hm-hero mb-4">
        <div class="position-relative" style="z-index:1;">
            <h3><i class="fas fa-house me-2"></i>Homepage Media Manager</h3>
            <p>Upload, edit, activate, or remove homepage banners, brand logos, and the site logo from the database.</p>

            <div class="hm-badges">
                <span class="hm-pill"><i class="fas fa-image"></i> Banners</span>
                <span class="hm-pill"><i class="fas fa-copyright"></i> Brands</span>
                <span class="hm-pill"><i class="fas fa-store"></i> Logo</span>
            </div>
        </div>
    </div>

    <div class="hm-grid">
        <div class="hm-stat">
            <div class="num"><?= hm_int($bannerStats['total'] ?? 0) ?></div>
            <div class="label">Banner records</div>
        </div>
        <div class="hm-stat">
            <div class="num"><?= hm_int($brandStats['total'] ?? 0) ?></div>
            <div class="label">Brand records</div>
        </div>
        <div class="hm-stat">
            <div class="num"><?= hm_int($logoStats['total'] ?? 0) ?></div>
            <div class="label">Logo records</div>
        </div>
        <div class="hm-stat">
            <div class="num"><?= hm_int($totalStats['active_total'] ?? 0) ?></div>
            <div class="label">Active media items</div>
        </div>
    </div>

    <?php if ($error !== ''): ?>
        <div class="alert alert-danger mt-4 mb-0"><?= hm_escape($error) ?></div>
    <?php endif; ?>
    <?php if ($msg !== ''): ?>
        <div class="alert alert-success mt-4 mb-0"><?= hm_escape($msg) ?></div>
    <?php endif; ?>

    <div class="row g-4 mt-2">
        <div class="col-xl-5">
            <div class="hm-card h-100">
                <div class="hm-card-hd">
                    <div>
                        <h5><?= ucfirst($tab) ?> Form</h5>
                        <div class="hm-help">Upload a new item or edit an existing one.</div>
                    </div>
                    <span class="hm-soft-note">Current tab: <?= ucfirst($tab) ?></span>
                </div>

                <div class="hm-card-bd">
                    <div class="hm-tabs mb-4">
                        <a class="hm-tab <?= $tab === 'banner' ? 'active' : '' ?>" href="?tab=banner">Banners</a>
                        <a class="hm-tab <?= $tab === 'brand' ? 'active' : '' ?>" href="?tab=brand">Brands</a>
                        <a class="hm-tab <?= $tab === 'logo' ? 'active' : '' ?>" href="?tab=logo">Logo</a>
                    </div>

                    <form method="post" enctype="multipart/form-data">
                        <input type="hidden" name="save_media" value="1">
                        <input type="hidden" name="id" value="<?= hm_int($editRow['id'] ?? 0) ?>">
                        <input type="hidden" name="type" value="<?= hm_escape($tab) ?>">

                        <div class="mb-3">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control"
                                placeholder="<?= $tab === 'brand' ? 'Apple / Samsung / Sony' : ($tab === 'logo' ? 'Main Logo' : 'Hero banner title') ?>"
                                value="<?= hm_escape($editRow['title'] ?? ($tab === 'logo' ? 'Main Logo' : '')) ?>"
                                <?= $tab === 'logo' ? '' : 'required' ?>>
                            <div class="hm-help">For brands, use the brand name. For banners, this is the main headline.
                            </div>
                        </div>

                        <?php if ($tab !== 'logo'): ?>
                            <div class="mb-3">
                                <label class="form-label"><?= $tab === 'brand' ? 'Short Note' : 'Subtitle' ?></label>
                                <input type="text" name="subtitle" class="form-control"
                                    placeholder="<?= $tab === 'brand' ? 'Optional short brand text' : 'Optional banner subtitle' ?>"
                                    value="<?= hm_escape($editRow['subtitle'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><?= $tab === 'brand' ? 'Website / Link' : 'Button Link' ?></label>
                                <input type="text" name="link" class="form-control"
                                    placeholder="<?= $tab === 'brand' ? 'https://brand.com' : 'products.php or #products' ?>"
                                    value="<?= hm_escape($editRow['link'] ?? '') ?>">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Button Text</label>
                                <input type="text" name="button_text" class="form-control"
                                    placeholder="<?= $tab === 'brand' ? 'Visit Website' : 'Shop Now' ?>"
                                    value="<?= hm_escape($editRow['button_text'] ?? '') ?>">
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label
                                class="form-label"><?= $tab === 'logo' ? 'Logo Image' : ($tab === 'brand' ? 'Brand Logo' : 'Banner Image') ?></label>
                            <input type="file" name="image" class="form-control" <?= empty($editRow['image']) ? 'required' : '' ?>>
                            <div class="hm-help">Leave empty while editing to keep the current image.</div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Sort Order</label>
                                <input type="number" name="sort_order" class="form-control"
                                    value="<?= hm_int($editRow['sort_order'] ?? 0) ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label d-block">Status</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" id="statusSwitch" name="status"
                                        <?= ((int) ($editRow['status'] ?? 1) === 1 ? 'checked' : '') ?>>
                                    <label class="form-check-label" for="statusSwitch">Active</label>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($editRow['image'])): ?>
                            <div class="mt-4">
                                <label class="form-label">Current Image</label>
                                <img src="<?= hm_escape(hm_asset_url($editRow['image'])) ?>" alt="Preview"
                                    class="hm-preview">
                            </div>
                        <?php endif; ?>

                        <div class="d-flex gap-2 flex-wrap mt-4">
                            <button type="submit" class="btn btn-primary px-4 rounded-3">
                                <i class="fas fa-floppy-disk me-1"></i>
                                Save Media
                            </button>
                            <a href="homepage_media.php?tab=<?= hm_escape($tab) ?>"
                                class="btn btn-outline-secondary px-4 rounded-3">
                                Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="hm-card">
                <div class="hm-card-hd">
                    <div>
                        <h5><?= ucfirst($tab) ?> List</h5>
                        <div class="hm-help">Edit, toggle status, or delete any saved media.</div>
                    </div>
                    <div class="hm-tabs">
                        <a class="hm-tab <?= $tab === 'banner' ? 'active' : '' ?>" href="?tab=banner">Banners</a>
                        <a class="hm-tab <?= $tab === 'brand' ? 'active' : '' ?>" href="?tab=brand">Brands</a>
                        <a class="hm-tab <?= $tab === 'logo' ? 'active' : '' ?>" href="?tab=logo">Logo</a>
                    </div>
                </div>

                <div class="hm-card-bd p-0">
                    <div class="table-responsive">
                        <table class="table hm-table align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:110px;">Preview</th>
                                    <th>Title</th>
                                    <th>Details</th>
                                    <th style="width:90px;">Order</th>
                                    <th style="width:100px;">Status</th>
                                    <th style="width:220px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($listResult && mysqli_num_rows($listResult) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($listResult)): ?>
                                        <tr>
                                            <td>
                                                <img src="<?= hm_escape(hm_asset_url($row['image'] ?? '')) ?>" alt="Media"
                                                    class="hm-thumb">
                                            </td>
                                            <td>
                                                <div class="fw-bold text-dark"><?= hm_escape($row['title'] ?? '') ?></div>
                                                <div class="small text-muted"><?= ucfirst(hm_escape($row['type'] ?? '')) ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($tab === 'banner'): ?>
                                                    <div class="text-dark"><?= hm_escape($row['subtitle'] ?? '') ?></div>
                                                    <div class="small text-muted"><?= hm_escape($row['button_text'] ?? '') ?></div>
                                                <?php elseif ($tab === 'brand'): ?>
                                                    <div class="text-dark"><?= hm_escape($row['link'] ?? '') ?></div>
                                                    <div class="small text-muted"><?= hm_escape($row['button_text'] ?? '') ?></div>
                                                <?php else: ?>
                                                    <div class="text-muted">Site logo image</div>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold"><?= hm_int($row['sort_order'] ?? 0) ?></td>
                                            <td>
                                                <?php if ((int) ($row['status'] ?? 0) === 1): ?>
                                                    <span class="badge text-bg-success">Active</span>
                                                <?php else: ?>
                                                    <span class="badge text-bg-secondary">Hidden</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="hm-actions">
                                                    <a href="homepage_media.php?tab=<?= hm_escape($tab) ?>&edit=<?= hm_int($row['id']) ?>"
                                                        class="hm-action edit">
                                                        <i class="fas fa-pen"></i> Edit
                                                    </a>
                                                    <a href="homepage_media.php?tab=<?= hm_escape($tab) ?>&action=toggle&id=<?= hm_int($row['id']) ?>"
                                                        class="hm-action toggle">
                                                        <i class="fas fa-eye"></i> Toggle
                                                    </a>
                                                    <a href="homepage_media.php?tab=<?= hm_escape($tab) ?>&action=delete&id=<?= hm_int($row['id']) ?>"
                                                        class="hm-action delete"
                                                        onclick="return confirm('Delete this item permanently?')">
                                                        <i class="fas fa-trash"></i> Delete
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6">
                                            <div class="hm-empty">No <?= hm_escape($tab) ?> records found.</div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if ($listStmt) {
    mysqli_stmt_close($listStmt);
}
include_once __DIR__ . "/../includes/footer.php";
?>