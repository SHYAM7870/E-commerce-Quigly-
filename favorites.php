<?php
header("Location: index.php?section=favorites");
exit;
?>

$userId    = (int) $_SESSION['user_id'];
$pageTitle = 'Favorites';
$user = admin_query_one('SELECT id, full_name, email, profile_image, is_verified FROM users WHERE id = ?', 'i', [$userId]);

admin_execute("CREATE TABLE IF NOT EXISTS favorites (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, listing_id INT NOT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, UNIQUE KEY uq_user_listing (user_id, listing_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action    = $_POST['action'] ?? '';
    $listingId = (int) ($_POST['listing_id'] ?? 0);

    if ($action === 'remove_favorite' && $listingId > 0) {
        admin_execute('DELETE FROM favorites WHERE user_id = ? AND listing_id = ?', 'ii', [$userId, $listingId]);
        admin_flash('success', 'Removed from favorites.');
        header('Location: favorites.php');
        exit;
    }
}

$categories = admin_query_all('SELECT id, category_name FROM categories ORDER BY category_name ASC');

$favorites = admin_query_all(
    "SELECT f.id AS favorite_id, l.*, c.category_name,
            COALESCE((SELECT li.image_path FROM listing_images li WHERE li.listing_id = l.id ORDER BY li.is_primary DESC, li.id ASC LIMIT 1), '') AS primary_image,
            COALESCE(u.full_name, u.email, 'Unknown user') AS owner_name
     FROM favorites f
     JOIN listings l ON l.id = f.listing_id
     LEFT JOIN categories c ON c.id = l.category_id
     LEFT JOIN users u ON u.id = l.user_id
     WHERE f.user_id = ?
     ORDER BY f.created_at DESC",
    'i',
    [$userId]
);

$userAvatar = !empty($user['profile_image'])
    ? 'upload/' . $user['profile_image']
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name'] ?? 'User') . '&background=6366f1&color=fff';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Favorites | Campus Exchange</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; font-family: Inter, sans-serif; background: #f8fafc; color: #0f172a; overflow-x: hidden; }

        .app { display: flex; min-height: 100vh; }

        .sidebar { width: 280px; position: fixed; inset: 0 auto 0 0; background: #fff; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; z-index: 1000; transition: transform .28s ease; }
        .brand { padding: 24px 20px; font-weight: 800; font-size: 1.25rem; border-bottom: 1px solid #f1f5f9; display: flex; align-items: center; gap: 10px; }
        .brand i { color: #6366f1; }
        .menu { padding: 16px; display: flex; flex-direction: column; gap: 6px; flex: 1; overflow: auto; }
        .menu a { padding: 12px 16px; border-radius: 14px; text-decoration: none; color: #64748b; font-weight: 600; display: flex; align-items: center; gap: 12px; }
        .menu a:hover, .menu a.active { background: #e0e7ff; color: #4338ca; }
        .menu a.logout { margin-top: auto; color: #ef4444; }
        .menu a.logout:hover { background: #fef2f2; color: #dc2626; }

        .main { margin-left: 280px; flex: 1; width: calc(100% - 280px); }

        .top { min-height: 84px; background: linear-gradient(135deg, #1e1b4b, #312e81); color: #fff; display: flex; align-items: center; justify-content: space-between; gap: 16px; padding: 14px 28px; position: sticky; top: 0; z-index: 5; box-shadow: 0 10px 30px rgba(15,23,42,.14); }
        .mobile-toggle { display: none; width: 44px; height: 44px; border-radius: 14px; border: 1px solid rgba(255,255,255,.14); background: rgba(255,255,255,.08); color: #fff; align-items: center; justify-content: center; flex: 0 0 auto; cursor: pointer; }
        .profile { display: flex; align-items: center; gap: 12px; text-decoration: none; color: #fff; flex: 0 0 auto; }
        .avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.25); }

        .wrap { padding: 28px; max-width: 1400px; margin: 0 auto; }

        .hero { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; margin-bottom: 22px; }
        .hero h1 { margin: 0; font-size: 1.9rem; font-weight: 800; letter-spacing: -0.03em; }
        .hero p { margin: 6px 0 0; color: #64748b; }

        .cardx { background: #fff; border: 1px solid #e5e7eb; border-radius: 22px; box-shadow: 0 10px 24px rgba(15,23,42,.05); }

        /* ── Filter bar ── */
        .filters { padding: 18px; display: grid; grid-template-columns: 1.5fr .8fr .8fr auto; gap: 12px; }
        .filters input, .filters select { width: 100%; border: 1px solid #d1d5db; border-radius: 14px; padding: 12px 14px; background: #fff; outline: none; font-family: inherit; font-size: .95rem; }
        .filters input:focus, .filters select:focus { border-color: #6366f1; box-shadow: 0 0 0 3px rgba(99,102,241,.12); }
        .filters button { border: 0; border-radius: 14px; background: #ef4444; color: #fff; font-weight: 700; padding: 12px 18px; cursor: pointer; font-family: inherit; white-space: nowrap; }
        .filters button:hover { background: #dc2626; }

        #resultCount { font-size: .9rem; color: #64748b; margin-bottom: 14px; min-height: 22px; }

        .grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .item { overflow: hidden; transition: opacity .2s; }
        .item.hidden { display: none; }

        .thumb { height: 200px; background: #eef2ff; position: relative; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; }
        .ph { height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: #6366f1; background: linear-gradient(135deg, #e0e7ff, #f8fafc); }

        /* Heart remove button overlay */
        .fav-remove-btn {
            position: absolute;
            top: 12px; right: 12px;
            width: 38px; height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,.92);
            border: 1px solid #fecaca;
            display: flex; align-items: center; justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            color: #ef4444;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            z-index: 2;
        }
        .fav-remove-btn:hover { transform: scale(1.12); background: #fff1f2; }
        .fav-remove-btn.loading { pointer-events: none; opacity: .6; }

        .body { padding: 18px; }
        .meta { display: flex; justify-content: space-between; gap: 10px; }
        .body h3 { margin: 0 0 8px; font-size: 1.05rem; font-weight: 800; }
        .price { font-size: 1.2rem; font-weight: 800; color: #4f46e5; }
        .small { color: #64748b; font-size: .92rem; }
        .chips { display: flex; gap: 8px; flex-wrap: wrap; margin: 10px 0; }
        .chip { font-size: .78rem; padding: 6px 10px; border-radius: 999px; font-weight: 700; }

        .btnx { border: 0; border-radius: 12px; padding: 10px 14px; font-weight: 800; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; gap: 8px; }
        .primary { background: #eef2ff; color: #3730a3; }
        .primary:hover { background: #e0e7ff; color: #3730a3; }
        .danger  { background: #fff1f2; color: #be123c; cursor: pointer; }
        .danger:hover { background: #fde8e8; }

        .empty { background: #fff; border: 1px dashed #cbd5e1; border-radius: 22px; padding: 44px; text-align: center; color: #64748b; }

        .sidebar.active { transform: translateX(0) !important; }

        @media (max-width: 1100px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filters { grid-template-columns: 1fr 1fr; }
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; width: 100%; }
            .mobile-toggle { display: inline-flex; }
        }
        @media (max-width: 700px) {
            .wrap { padding: 16px; }
            .grid { grid-template-columns: 1fr; }
            .hero { flex-direction: column; }
            .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="brand"><i class="fa fa-shopping-bag"></i> CampusExchange</div>
        <nav class="menu">
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <a href="categories.php"><i class="fa fa-th-large"></i> Categories</a>
            <a href="listings.php"><i class="fa fa-list"></i> All Listings</a>
            <a href="post_item.php"><i class="fa fa-plus-circle"></i> Post Item</a>
            <a href="favorites.php" class="active"><i class="fa fa-heart"></i> Favorites</a>
            <a href="messages.php"><i class="fa fa-comments"></i> Messages</a>
            <a href="meetups.php"><i class="fa fa-map-marker"></i> Meetups</a>
            <a href="profile.php"><i class="fa fa-user"></i> Profile</a>
            <a href="logout.php" class="logout"><i class="fa fa-sign-out"></i> Logout</a>
        </nav>
    </aside>

    <main class="main">
        <header class="top">
            <button type="button" class="mobile-toggle" id="mobileToggle" aria-label="Toggle sidebar">
                <i class="fa fa-bars"></i>
            </button>
            <div style="font-weight:800;font-size:1.1rem;">Your Favorites</div>
            <a class="profile" href="profile.php">
                <img class="avatar" src="<?= admin_e($userAvatar) ?>" alt="Profile">
                <div class="d-none d-md-block">
                    <div style="font-weight:700"><?= admin_e($user['full_name'] ?? 'User') ?></div>
                    <div style="font-size:.8rem;color:#a5b4fc"><?= !empty($user['is_verified']) ? 'Verified Student' : 'Student' ?></div>
                </div>
            </a>
        </header>

        <div class="wrap">
            <?php $flash = admin_flash_get(); if ($flash): ?>
                <div class="alert alert-<?= admin_e($flash['type'] === 'error' ? 'danger' : 'success') ?> mb-3">
                    <?= admin_e($flash['message']) ?>
                </div>
            <?php endif; ?>

            <div class="hero">
                <div>
                    <h1><i class="fa fa-heart" style="color:#ef4444"></i> Favorites</h1>
                    <p><?= count($favorites) ?> saved listing<?= count($favorites) !== 1 ? 's' : '' ?></p>
                </div>
            </div>

            <?php if (!empty($favorites)): ?>
                <!-- Filter bar -->
                <div class="cardx mb-4">
                    <div class="filters">
                        <input type="search" id="favSearch" placeholder="Search your favorites...">
                        <select id="favCategory">
                            <option value="0">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= (int) $cat['id'] ?>"><?= admin_e($cat['category_name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                        <select id="favType">
                            <option value="">All Types</option>
                            <option value="sell">Sell</option>
                            <option value="exchange">Exchange</option>
                            <option value="donate">Donate</option>
                        </select>
                        <button type="button" onclick="clearFavFilters()">
                            <i class="fa fa-times"></i> Clear
                        </button>
                    </div>
                </div>

                <div id="resultCount"></div>

                <section class="grid" id="favGrid">
                    <?php foreach ($favorites as $item): ?>
                        <?php
                            $typeMap = [
                                'sell'     => ['Sell',     'background:#e0e7ff;color:#3730a3;'],
                                'exchange' => ['Exchange', 'background:#dcfce7;color:#166534;'],
                                'donate'   => ['Donate',   'background:#ffedd5;color:#9a3412;'],
                            ];
                            [$typeLabel, $typeStyle] = $typeMap[strtolower((string)($item['listing_type'] ?? ''))] ?? [ucfirst((string)($item['listing_type'] ?? '')), 'background:#f3f4f6;color:#6b7280;'];
                            $priceLabel = strtolower((string)($item['listing_type'] ?? '')) === 'donate'
                                ? 'Free'
                                : '₹' . number_format((float)($item['price'] ?? 0), 2);
                        ?>
                        <article class="cardx item"
                            data-title="<?= strtolower(admin_e($item['title'])) ?>"
                            data-desc="<?= strtolower(admin_e($item['description'] ?? '')) ?>"
                            data-location="<?= strtolower(admin_e($item['location'] ?? '')) ?>"
                            data-category="<?= (int) $item['category_id'] ?>"
                            data-type="<?= admin_e($item['listing_type'] ?? '') ?>">

                            <div class="thumb">
                                <?php if (!empty($item['primary_image'])): ?>
                                    <img src="upload/<?= admin_e($item['primary_image']) ?>" alt="<?= admin_e($item['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="ph"><i class="fa fa-image"></i></div>
                                <?php endif; ?>

                                <!-- Red heart remove button -->
                                <button class="fav-remove-btn" data-id="<?= (int)$item['id'] ?>" title="Remove from favorites">
                                    <i class="fa fa-heart"></i>
                                </button>
                            </div>

                            <div class="body">
                                <div class="meta">
                                    <div>
                                        <h3><?= admin_e($item['title']) ?></h3>
                                        <div class="small">by <?= admin_e($item['owner_name']) ?></div>
                                    </div>
                                    <div class="price"><?= admin_e($priceLabel) ?></div>
                                </div>
                                <div class="small mt-1"><i class="fa fa-map-marker"></i> <?= admin_e($item['location'] ?: 'Campus') ?></div>

                                <div class="chips">
                                    <span class="chip" style="<?= $typeStyle ?>"><?= admin_e($typeLabel) ?></span>
                                    <span class="chip" style="background:#eff6ff;color:#1d4ed8;"><?= admin_e($item['category_name'] ?? 'Uncategorized') ?></span>
                                </div>

                                <p class="small" style="min-height:40px">
                                    <?= admin_e(mb_substr((string)($item['description'] ?? ''), 0, 100)) ?>
                                    <?= mb_strlen((string)($item['description'] ?? '')) > 100 ? '…' : '' ?>
                                </p>

                                <div class="d-flex gap-2 mt-3">
                                    <a href="listing_details.php?id=<?= (int)$item['id'] ?>" class="btnx primary flex-grow-1">
                                        <i class="fa fa-eye"></i> View
                                    </a>
                                    <!-- Fallback form remove (no JS) -->
                                    <form method="post" class="m-0 fav-remove-form" data-id="<?= (int)$item['id'] ?>">
                                        <input type="hidden" name="action" value="remove_favorite">
                                        <input type="hidden" name="listing_id" value="<?= (int)$item['id'] ?>">
                                        <button type="submit" class="btnx danger">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>

                <div class="empty" id="liveEmpty" style="display:none">
                    <i class="fa fa-search fa-3x mb-3"></i>
                    <h4>No favorites match</h4>
                    <p class="mb-0">Try different keywords or clear the filter.</p>
                </div>

            <?php else: ?>
                <div class="empty">
                    <i class="fa fa-heart-o fa-3x mb-3"></i>
                    <h4>No favorites yet</h4>
                    <p class="mb-0">Tap the heart on any listing to save it here.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>
</div>

<script>
/* ── Sidebar toggle ── */
const mobileToggle = document.getElementById('mobileToggle');
const sidebar      = document.getElementById('sidebar');
mobileToggle?.addEventListener('click', () => sidebar.classList.toggle('active'));
document.addEventListener('click', e => {
    if (window.innerWidth <= 1100 && sidebar && mobileToggle) {
        if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target))
            sidebar.classList.remove('active');
    }
});

/* ── AJAX remove (heart overlay button) ── */
document.querySelectorAll('.fav-remove-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id   = btn.dataset.id;
        const card = btn.closest('.item');
        btn.classList.add('loading');

        try {
            const fd = new FormData();
            fd.append('listing_id', id);
            fd.append('action', 'remove_favorite');
            const res  = await fetch('toggle_favorite.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                // Animate card out
                card.style.transition = 'opacity .3s, transform .3s';
                card.style.opacity    = '0';
                card.style.transform  = 'scale(.95)';
                setTimeout(() => {
                    card.remove();
                    applyFavFilter();
                    // Update hero count
                    const remaining = document.querySelectorAll('#favGrid .item').length;
                    const heroP = document.querySelector('.hero p');
                    if (heroP) heroP.textContent = `${remaining} saved listing${remaining !== 1 ? 's' : ''}`;
                }, 300);
            }
        } catch (err) {
            console.error('Remove failed', err);
            btn.classList.remove('loading');
        }
    });
});

/* ── Live filter ── */
const favGrid    = document.getElementById('favGrid');
const liveEmpty  = document.getElementById('liveEmpty');
const resultCount = document.getElementById('resultCount');

function applyFavFilter() {
    if (!favGrid) return;
    const q    = (document.getElementById('favSearch')?.value  ?? '').trim().toLowerCase();
    const cat  = document.getElementById('favCategory')?.value ?? '0';
    const type = document.getElementById('favType')?.value     ?? '';

    const cards = Array.from(favGrid.querySelectorAll('.item'));
    let visible = 0;

    cards.forEach(card => {
        const title    = card.dataset.title    ?? '';
        const desc     = card.dataset.desc     ?? '';
        const location = card.dataset.location ?? '';
        const cardCat  = card.dataset.category ?? '0';
        const cardType = card.dataset.type     ?? '';

        const matchQ   = q === '' || title.includes(q) || desc.includes(q) || location.includes(q);
        const matchCat = cat  === '0' || cat  === cardCat;
        const matchTyp = type === ''  || type === cardType;

        if (matchQ && matchCat && matchTyp) {
            card.classList.remove('hidden');
            visible++;
        } else {
            card.classList.add('hidden');
        }
    });

    if (liveEmpty)  liveEmpty.style.display  = visible === 0 ? '' : 'none';
    if (resultCount) resultCount.textContent = (q || cat !== '0' || type)
        ? `Showing ${visible} of ${cards.length} favorite${cards.length !== 1 ? 's' : ''}`
        : '';
}

function clearFavFilters() {
    document.getElementById('favSearch').value    = '';
    document.getElementById('favCategory').value  = '0';
    document.getElementById('favType').value      = '';
    applyFavFilter();
}

['favSearch', 'favCategory', 'favType'].forEach(id => {
    document.getElementById(id)?.addEventListener('input',  applyFavFilter);
    document.getElementById(id)?.addEventListener('change', applyFavFilter);
});
</script>
</body>
</html>
