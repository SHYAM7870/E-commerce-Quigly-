<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/feature_utils.php';
include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/sidebar.php';

$tab = strtolower(trim($_GET['tab'] ?? 'banners'));
if (!in_array($tab, ['banners', 'brands'], true)) $tab = 'banners';

$editId  = (int)($_GET['edit'] ?? 0);
$error   = trim($_GET['error'] ?? '');
$msg     = trim($_GET['msg']   ?? '');

$dbType  = $tab === 'banners' ? 'banner' : 'brand';
$editRow = null;
if ($editId > 0) {
    $editRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM homepage_media WHERE id={$editId} AND type='{$dbType}' LIMIT 1"));
}

$banners = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='banner' ORDER BY sort_order ASC, id DESC");
$brands  = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='brand'  ORDER BY sort_order ASC, id DESC");

function asset_url(?string $path): string {
    if (!$path) return 'https://placehold.co/400x250?text=No+Image';
    if (preg_match('#^(https?://)#i', $path)) return $path;
    return '../../' . ltrim($path, '/');
}
?>
<style>
.assets-page{padding:24px 28px 40px;background:#f8fafc;min-height:100vh}
.assets-hero{background:linear-gradient(135deg,#0f172a 0%,#1e1b4b 52%,#7c3aed 100%);color:#fff;border-radius:28px;padding:28px;margin-bottom:20px;box-shadow:0 22px 60px rgba(15,23,42,.18)}
.assets-hero h3{font-weight:900;margin:0}
.assets-hero p{margin:8px 0 0;color:rgba(255,255,255,.8)}
.assets-card{background:#fff;border:1px solid #e5e7eb;border-radius:24px;overflow:hidden;box-shadow:0 8px 24px rgba(15,23,42,.05)}
.assets-card .card-head{padding:18px 22px;border-bottom:1px solid #eef2f7;display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap}
.assets-card .card-head h5{margin:0;font-weight:900}
.assets-card .card-bodyx{padding:22px}
.preview-img{width:100%;height:170px;object-fit:cover;border-radius:18px;border:1px solid #e5e7eb;background:#f8fafc}
.asset-table td,.asset-table th{vertical-align:middle}
.action-group{display:flex;gap:8px;flex-wrap:wrap}
.form-shell{background:#fff;border:1px solid #e5e7eb;border-radius:24px;padding:22px;box-shadow:0 8px 24px rgba(15,23,42,.05)}
.form-shell .form-label{font-weight:700;color:#334155}
.small-note{font-size:12px;color:#64748b}
.tab-btn{padding:8px 20px;border-radius:999px;border:1.5px solid #e2e8f0;background:#fff;font-weight:700;color:#64748b;cursor:pointer;text-decoration:none;font-size:.9rem;transition:.2s}
.tab-btn.active,.tab-btn:hover{background:#7c3aed;color:#fff;border-color:#7c3aed;text-decoration:none}
</style>

<div class="assets-page">
    <div class="assets-hero">
        <h3><i class="fas fa-images me-2"></i>Homepage Assets Manager</h3>
        <p>Control the hero banners and brand logos shown on your storefront homepage.</p>
    </div>

    <?php if ($error): ?>
        <div class="alert alert-danger rounded-4"><?= qf_escape($error) ?></div>
    <?php endif; ?>
    <?php if ($msg): ?>
        <div class="alert alert-success rounded-4"><i class="fas fa-check-circle me-2"></i>Action completed: <strong><?= qf_escape($msg) ?></strong></div>
    <?php endif; ?>

    <!-- Tab switcher -->
    <div class="d-flex gap-2 mb-4">
        <a href="?tab=banners<?= $editId && $tab==='banners' ? '&edit='.$editId : '' ?>" class="tab-btn <?= $tab==='banners'?'active':'' ?>"><i class="fas fa-image me-1"></i>Banners</a>
        <a href="?tab=brands<?= $editId && $tab==='brands' ? '&edit='.$editId : '' ?>"  class="tab-btn <?= $tab==='brands' ?'active':'' ?>"><i class="fas fa-star me-1"></i>Brands</a>
    </div>

    <div class="row g-4">
        <!-- ── FORM ── -->
        <div class="col-xl-5">
            <div class="form-shell mb-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0 fw-bold"><?= $editRow ? 'Edit '.ucfirst($dbType) : 'Add New '.ucfirst($dbType) ?></h5>
                    <div class="d-flex gap-2">
                        <span class="badge text-bg-dark"><?= ucfirst($tab) ?></span>
                        <?php if ($editRow): ?><a href="?tab=<?= $tab ?>" class="btn btn-sm btn-outline-secondary">Cancel</a><?php endif; ?>
                    </div>
                </div>

                <?php if ($tab === 'banners'): ?>
                <form method="POST" action="../actions/site_assets_action.php" enctype="multipart/form-data">
                    <input type="hidden" name="type"  value="banner">
                    <input type="hidden" name="id"    value="<?= qf_int($editRow['id'] ?? 0) ?>">
                    <div class="mb-3">
                        <label class="form-label">Title *</label>
                        <input type="text" name="title" class="form-control" value="<?= qf_escape($editRow['title'] ?? '') ?>" required placeholder="e.g. Summer Sale — Up to 60% Off">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Subtitle / Description</label>
                        <textarea name="subtitle" class="form-control" rows="3" placeholder="Short promo text..."><?= qf_escape($editRow['subtitle'] ?? '') ?></textarea>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Button Text</label>
                            <input type="text" name="cta_text" class="form-control" value="<?= qf_escape($editRow['button_text'] ?? 'Shop Now') ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= qf_int($editRow['sort_order'] ?? 0) ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Button URL</label>
                        <input type="text" name="cta_url" class="form-control" value="<?= qf_escape($editRow['link'] ?? 'index.php') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Banner Image <?= $editRow ? '(leave blank to keep existing)' : '*' ?></label>
                        <?php if (!empty($editRow['image'])): ?>
                            <div class="mb-2"><img src="<?= asset_url($editRow['image']) ?>" style="max-height:80px;border-radius:10px;"></div>
                        <?php endif; ?>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div class="small-note mt-1">Recommended: 1200×450px JPG/PNG/WebP</div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="bannerActive" <?= (($editRow['status'] ?? 1) ? 'checked' : '') ?>>
                        <label class="form-check-label" for="bannerActive">Active (visible on homepage)</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4">Save Banner</button>
                        <a href="?tab=banners" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>

                <?php else: ?>
                <form method="POST" action="../actions/site_assets_action.php" enctype="multipart/form-data">
                    <input type="hidden" name="type" value="brand">
                    <input type="hidden" name="id"   value="<?= qf_int($editRow['id'] ?? 0) ?>">
                    <div class="mb-3">
                        <label class="form-label">Brand Name *</label>
                        <input type="text" name="name" class="form-control" value="<?= qf_escape($editRow['title'] ?? '') ?>" required placeholder="e.g. Sony, Apple, Samsung">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Website URL</label>
                        <input type="text" name="website" class="form-control" value="<?= qf_escape($editRow['link'] ?? '') ?>" placeholder="https://...">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sort Order</label>
                            <input type="number" name="sort_order" class="form-control" value="<?= qf_int($editRow['sort_order'] ?? 0) ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Logo (PNG/SVG preferred)</label>
                            <input type="file" name="logo" class="form-control" accept="image/*">
                            <?php if (!empty($editRow['image'])): ?>
                                <img src="<?= asset_url($editRow['image']) ?>" class="mt-2" style="max-height:40px;">
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" id="brandActive" <?= (($editRow['status'] ?? 1) ? 'checked' : '') ?>>
                        <label class="form-check-label" for="brandActive">Active (show in carousel)</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary fw-bold px-4">Save Brand</button>
                        <a href="?tab=brands" class="btn btn-outline-secondary">Reset</a>
                    </div>
                </form>
                <?php endif; ?>
            </div>
        </div>

        <!-- ── LIST ── -->
        <div class="col-xl-7">

            <!-- Banners table -->
            <div class="assets-card mb-4">
                <div class="card-head">
                    <h5><i class="fas fa-image me-2 text-primary"></i>Hero Banners (<?= mysqli_num_rows($banners) ?>)</h5>
                    <a href="?tab=banners" class="btn btn-sm <?= $tab==='banners' ? 'btn-dark' : 'btn-outline-dark' ?>">+ Add Banner</a>
                </div>
                <div class="card-bodyx">
                    <div class="table-responsive">
                        <table class="table asset-table align-middle mb-0">
                            <thead class="table-light">
                                <tr><th>Preview</th><th>Title</th><th>Order</th><th>Status</th><th>Actions</th></tr>
                            </thead>
                            <tbody>
                            <?php if (mysqli_num_rows($banners) === 0): ?>
                                <tr><td colspan="5" class="text-center text-muted py-4">No banners yet. Add your first one →</td></tr>
                            <?php else: ?>
                            <?php while ($row = mysqli_fetch_assoc($banners)): ?>
                                <tr>
                                    <td style="width:120px">
                                        <img src="<?= asset_url($row['image'] ?? '') ?>" style="height:60px;width:110px;object-fit:cover;border-radius:10px;border:1px solid #e5e7eb;">
                                    </td>
                                    <td>
                                        <div class="fw-bold"><?= qf_escape($row['title']) ?></div>
                                        <div class="small text-muted"><?= qf_escape(substr($row['subtitle'] ?? '', 0, 55)) ?><?= strlen($row['subtitle']??'')>55?'...':'' ?></div>
                                    </td>
                                    <td><?= qf_int($row['sort_order']) ?></td>
                                    <td><span class="badge <?= $row['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $row['status'] ? 'Active' : 'Hidden' ?></span></td>
                                    <td>
                                        <div class="action-group">
                                            <a class="btn btn-sm btn-outline-primary" href="?tab=banners&edit=<?= qf_int($row['id']) ?>">Edit</a>
                                            <form method="POST" action="../actions/site_assets_action.php" onsubmit="return confirm('Delete this banner?')" style="display:inline">
                                                <input type="hidden" name="type"   value="banner">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id"     value="<?= qf_int($row['id']) ?>">
                                                <button class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Brands grid -->
            <div class="assets-card">
                <div class="card-head">
                    <h5><i class="fas fa-star me-2 text-warning"></i>Brand Carousel (<?= mysqli_num_rows($brands) ?>)</h5>
                    <a href="?tab=brands" class="btn btn-sm <?= $tab==='brands' ? 'btn-dark' : 'btn-outline-dark' ?>">+ Add Brand</a>
                </div>
                <div class="card-bodyx">
                    <div class="row g-3">
                    <?php if (mysqli_num_rows($brands) === 0): ?>
                        <div class="col-12"><div class="alert alert-light border mb-0">No brands yet. Add your first brand logo.</div></div>
                    <?php else: ?>
                    <?php while ($row = mysqli_fetch_assoc($brands)): ?>
                        <div class="col-md-6 col-lg-4">
                            <div class="border rounded-4 p-3 h-100">
                                <img src="<?= asset_url($row['image'] ?? '') ?>" class="preview-img mb-2" style="height:90px;object-fit:contain;padding:8px;">
                                <div class="fw-bold"><?= qf_escape($row['title']) ?></div>
                                <div class="small text-muted mb-2"><?= qf_escape($row['link'] ?? '') ?></div>
                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                    <span class="badge <?= $row['status'] ? 'bg-success' : 'bg-secondary' ?>"><?= $row['status'] ? 'Active' : 'Hidden' ?></span>
                                    <div class="action-group">
                                        <a class="btn btn-sm btn-outline-primary" href="?tab=brands&edit=<?= qf_int($row['id']) ?>">Edit</a>
                                        <form method="POST" action="../actions/site_assets_action.php" onsubmit="return confirm('Delete this brand?')" style="display:inline">
                                            <input type="hidden" name="type"   value="brand">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id"     value="<?= qf_int($row['id']) ?>">
                                            <button class="btn btn-sm btn-outline-danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                    <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
