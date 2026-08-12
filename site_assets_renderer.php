<?php
// ── site_assets_renderer.php ──────────────────────────────────────────────────
// Renders admin-managed hero banners + brand strip for the user-facing homepage.
// Uses the existing homepage_media table (type='banner' | type='brand').
// ─────────────────────────────────────────────────────────────────────────────
require_once __DIR__ . '/admin/includes/db.php';
if (!function_exists('qf_escape')) {
    function qf_escape($v): string { return htmlspecialchars((string)($v ?? ''), ENT_QUOTES, 'UTF-8'); }
}

$heroBanners = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='banner' AND status=1 ORDER BY sort_order ASC, id DESC");
$brandRows   = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='brand'  AND status=1 ORDER BY sort_order ASC, id DESC");

function qf_asset_src(?string $path): string {
    $path = trim((string)$path);
    if ($path === '') return 'https://placehold.co/1200x500?text=Quigly';
    if (preg_match('#^(https?://)#i', $path)) return $path;
    return htmlspecialchars($path, ENT_QUOTES);
}
?>
<style>
.qf-home-hero{padding:22px 0 10px}
.qf-banner-card{
    position:relative;overflow:hidden;border-radius:32px;
    min-height:420px;background:#0f172a;
    box-shadow:0 24px 60px rgba(15,23,42,.18);
}
.qf-banner-card img{width:100%;height:100%;min-height:420px;object-fit:cover;display:block;filter:saturate(1.05)}
.qf-banner-overlay{position:absolute;inset:0;background:linear-gradient(90deg,rgba(2,6,23,.82) 0%,rgba(2,6,23,.35) 50%,rgba(2,6,23,.10) 100%)}
.qf-banner-content{position:absolute;inset:0;z-index:2;display:flex;align-items:center;padding:28px;color:#fff}
.qf-banner-pill{display:inline-flex;align-items:center;gap:.5rem;padding:.6rem 1rem;border-radius:999px;background:rgba(255,255,255,.14);border:1px solid rgba(255,255,255,.14);backdrop-filter:blur(10px);font-weight:700;font-size:.88rem}
.qf-banner-title{font-size:clamp(2rem,4vw,4.2rem);font-weight:900;line-height:1.02;margin:16px 0 12px}
.qf-banner-desc{max-width:58ch;color:rgba(255,255,255,.82);font-size:1.02rem;line-height:1.75}
.qf-banner-actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:22px}
.qf-brand-strip{
    margin-top:18px;padding:16px 18px;border-radius:24px;
    background:rgba(255,255,255,.78);border:1px solid rgba(148,163,184,.25);
    backdrop-filter:blur(14px);box-shadow:0 12px 34px rgba(15,23,42,.06);
}
body.dark-mode .qf-brand-strip{background:rgba(15,23,42,.72);border-color:rgba(255,255,255,.08)}
.qf-brand-track{display:flex;gap:18px;overflow:auto;scrollbar-width:none;padding-bottom:4px}
.qf-brand-track::-webkit-scrollbar{display:none}
.qf-brand-item{
    min-width:170px;display:flex;align-items:center;gap:12px;
    padding:14px 16px;border-radius:18px;
    background:#fff;border:1px solid #e5e7eb;
    box-shadow:0 6px 18px rgba(15,23,42,.04);
    text-decoration:none;color:inherit;transition:.2s;
}
.qf-brand-item:hover{box-shadow:0 10px 28px rgba(124,58,237,.12);border-color:#c4b5fd;color:inherit}
body.dark-mode .qf-brand-item{background:#0f172a;border-color:#243041}
.qf-brand-item img{width:46px;height:46px;object-fit:contain;border-radius:12px;background:#fff;padding:6px;border:1px solid #eef2f7}
.qf-brand-meta strong{display:block;font-size:.95rem;line-height:1.15}
.qf-brand-meta small{color:#64748b}
body.dark-mode .qf-brand-meta small{color:#94a3b8}
</style>

<div class="qf-home-hero container">
    <!-- Hero Banner Carousel -->
    <div id="qfHomeCarousel" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
        <?php
        $first = true; $hasBanner = false;
        if ($heroBanners && mysqli_num_rows($heroBanners) > 0):
            while ($b = mysqli_fetch_assoc($heroBanners)):
                $hasBanner = true;
        ?>
            <div class="carousel-item <?= $first ? 'active' : '' ?>">
                <div class="qf-banner-card">
                    <img src="<?= qf_asset_src($b['image'] ?? '') ?>" alt="<?= qf_escape($b['title'] ?? 'Banner') ?>">
                    <div class="qf-banner-overlay"></div>
                    <div class="qf-banner-content">
                        <div>
                            <?php if (!empty($b['subtitle'])): ?>
                                <div class="qf-banner-pill"><i class="fas fa-bolt"></i> <?= qf_escape($b['subtitle']) ?></div>
                            <?php endif; ?>
                            <h1 class="qf-banner-title"><?= qf_escape($b['title'] ?? '') ?></h1>
                            <div class="qf-banner-actions">
                                <?php $btnText = !empty($b['button_text']) ? $b['button_text'] : 'Shop Now'; ?>
                                <?php $btnLink = !empty($b['link']) ? $b['link'] : 'index.php'; ?>
                                <a class="btn btn-light btn-lg fw-bold" href="<?= qf_escape($btnLink) ?>"><?= qf_escape($btnText) ?></a>
                                <a class="btn btn-outline-light btn-lg fw-bold" href="?section=products">Explore Products</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php $first = false; endwhile; endif;
        if (!$hasBanner): ?>
            <div class="carousel-item active">
                <div class="qf-banner-card">
                    <img src="assets/images/photo-1556656793-08538906a9f8.jpg" alt="Quigly">
                    <div class="qf-banner-overlay"></div>
                    <div class="qf-banner-content">
                        <div>
                            <div class="qf-banner-pill"><i class="fas fa-sparkles"></i> Premium Storefront</div>
                            <h1 class="qf-banner-title">Add hero banners from the Admin Panel.</h1>
                            <p class="qf-banner-desc">Go to Admin → Homepage Assets to add your own banners and brand logos.</p>
                            <div class="qf-banner-actions">
                                <a class="btn btn-light btn-lg fw-bold" href="?section=products">Shop Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        </div>
        <?php if ($hasBanner): ?>
        <button class="carousel-control-prev" type="button" data-bs-target="#qfHomeCarousel" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#qfHomeCarousel" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
        </button>
        <div class="carousel-indicators">
            <?php $cnt = mysqli_num_rows($heroBanners); for($ci=0;$ci<$cnt;$ci++): ?>
            <button type="button" data-bs-target="#qfHomeCarousel" data-bs-slide-to="<?=$ci?>" <?=$ci===0?'class="active"':''?>></button>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Brand Strip -->
    <div class="qf-brand-strip">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
            <div>
                <div class="fw-bold fs-5">Trusted Brands</div>
                <div class="text-muted small">Managed from Admin → Homepage Assets</div>
            </div>
            <a href="?section=products" class="btn btn-sm btn-outline-primary">View All Products</a>
        </div>
        <div class="qf-brand-track">
        <?php
        $brandFound = false;
        if ($brandRows && mysqli_num_rows($brandRows) > 0):
            while ($brand = mysqli_fetch_assoc($brandRows)):
                $brandFound = true;
        ?>
            <a class="qf-brand-item" href="<?= qf_escape($brand['link'] ?? '#') ?>" target="_blank" rel="noopener">
                <img src="<?= qf_asset_src($brand['image'] ?? '') ?>" alt="<?= qf_escape($brand['title'] ?? 'Brand') ?>">
                <div class="qf-brand-meta">
                    <strong><?= qf_escape($brand['title'] ?? '') ?></strong>
                    <small>Partner Brand</small>
                </div>
            </a>
        <?php endwhile; endif;
        if (!$brandFound): ?>
            <div class="qf-brand-item">
                <img src="https://placehold.co/80x80?text=B" alt="Brand">
                <div class="qf-brand-meta"><strong>Brand Slot</strong><small>Add via admin</small></div>
            </div>
        <?php endif; ?>
        </div>
    </div>
</div>
