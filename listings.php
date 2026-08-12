<?php
header("Location: index.php?section=products");
exit;
?>

$userId    = (int) $_SESSION['user_id'];
$pageTitle = 'Listings';

$user = admin_query_one(
    'SELECT id, full_name, email, profile_image, is_verified FROM users WHERE id = ?',
    'i',
    [$userId]
);

if (!$user) {
    header('Location: logout.php');
    exit;
}

$categories = admin_query_all('SELECT id, category_name, category_icon FROM categories ORDER BY category_name ASC');

$search     = trim($_GET['search'] ?? '');
$categoryId = (int) ($_GET['category'] ?? 0);
$type       = trim($_GET['type'] ?? '');
$sort       = trim($_GET['sort'] ?? 'newest');

$where  = ["l.status = 'active'"];
$params = [];
$types  = '';

if ($search !== '') {
    $where[]  = '(l.title LIKE ? OR l.description LIKE ? OR l.location LIKE ?)';
    $like     = '%' . $search . '%';
    $params   = array_merge($params, [$like, $like, $like]);
    $types   .= 'sss';
}

if ($categoryId > 0) {
    $where[]  = 'l.category_id = ?';
    $params[] = $categoryId;
    $types   .= 'i';
}

if (in_array($type, ['sell', 'exchange', 'donate'], true)) {
    $where[]  = 'l.listing_type = ?';
    $params[] = $type;
    $types   .= 's';
}

$orderBy = match ($sort) {
    'oldest'     => 'l.created_at ASC',
    'price_low'  => 'CAST(COALESCE(l.price, 0) AS DECIMAL(10,2)) ASC',
    'price_high' => 'CAST(COALESCE(l.price, 0) AS DECIMAL(10,2)) DESC',
    default      => 'l.created_at DESC',
};

$listings = admin_query_all(
    "SELECT l.*, c.category_name, c.category_icon,
            COALESCE((SELECT li.image_path FROM listing_images li WHERE li.listing_id = l.id ORDER BY li.is_primary DESC, li.id ASC LIMIT 1), '') AS primary_image,
            COALESCE(u.full_name, u.email, 'Unknown user') AS owner_name,
            COALESCE(u.profile_image, '') AS owner_image,
            COALESCE((SELECT COUNT(*) FROM favorites f WHERE f.listing_id = l.id), 0) AS favorite_count,
            COALESCE((SELECT COUNT(*) FROM favorites f2 WHERE f2.listing_id = l.id AND f2.user_id = {$userId}), 0) AS is_favorited
     FROM listings l
     LEFT JOIN categories c ON c.id = l.category_id
     LEFT JOIN users u ON u.id = l.user_id
     WHERE " . implode(' AND ', $where) . "
     ORDER BY {$orderBy}",
    $types,
    $params
);

$userAvatar = !empty($user['profile_image'])
    ? 'upload/' . $user['profile_image']
    : 'https://ui-avatars.com/api/?name=' . urlencode($user['full_name'] ?? 'User') . '&background=6366f1&color=fff';

if (!function_exists('ce_listing_image_src')) {
    function ce_listing_image_src(?string $storedPath): string
    {
        $path = trim(str_replace('\\', '/', (string) $storedPath));
        if ($path === '') return '';
        if (preg_match('~^https?://~i', $path) || str_starts_with($path, 'data:')) return $path;
        $path     = preg_replace('~^[A-Z]:/~i', '', $path);
        $path     = ltrim($path, '/');
        $basename = basename($path);
        $candidates = [
            $path,
            'upload/listings/' . $basename,
            'upload/' . $basename,
            'admin/upload/listings/' . $basename,
            'admin/upload/' . $basename,
            'listings/' . $basename,
            $basename,
        ];
        foreach (array_unique($candidates) as $candidate) {
            if (is_file(__DIR__ . '/' . $candidate)) return $candidate;
        }
        return 'upload/listings/' . $basename;
    }
}

function ce_badge(string $status): array
{
    $map = [
        'sell'     => ['Sell',     'background:#e0e7ff;color:#3730a3;'],
        'exchange' => ['Exchange', 'background:#dcfce7;color:#166534;'],
        'donate'   => ['Donate',   'background:#ffedd5;color:#9a3412;'],
    ];
    return $map[strtolower($status)] ?? [ucfirst($status), 'background:#f3f4f6;color:#6b7280;'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= admin_e($pageTitle) ?> | Campus Exchange</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Inter, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            overflow-x: hidden;
        }

        .app { display: flex; min-height: 100vh; }

        /* ── Sidebar ── */
        .sidebar {
            width: 280px;
            position: fixed;
            inset: 0 auto 0 0;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
            transition: transform .28s ease;
        }

        .brand {
            padding: 24px 20px;
            font-weight: 800;
            font-size: 1.25rem;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .brand i { color: #6366f1; }

        .menu {
            padding: 16px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
            overflow: auto;
        }
        .menu a {
            padding: 12px 16px;
            border-radius: 14px;
            text-decoration: none;
            color: #64748b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .menu a:hover, .menu a.active { background: #e0e7ff; color: #4338ca; }
        .menu a.logout { margin-top: auto; color: #ef4444; }
        .menu a.logout:hover { background: #fef2f2; color: #dc2626; }

        /* ── Main ── */
        .main { margin-left: 280px; flex: 1; width: calc(100% - 280px); }

        .top {
            min-height: 84px;
            background: linear-gradient(135deg, #1e1b4b, #312e81);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            padding: 14px 28px;
            position: sticky;
            top: 0;
            z-index: 5;
            box-shadow: 0 10px 30px rgba(15,23,42,.14);
        }

        .mobile-toggle {
            display: none;
            width: 44px;
            height: 44px;
            border-radius: 14px;
            border: 1px solid rgba(255,255,255,.14);
            background: rgba(255,255,255,.08);
            color: #fff;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            cursor: pointer;
        }

        .searchbar {
            display: flex;
            gap: 10px;
            align-items: center;
            background: rgba(255,255,255,.10);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 999px;
            padding: 6px 8px 6px 14px;
            width: min(100%, 560px);
            flex: 1;
        }
        .searchbar input {
            flex: 1;
            background: transparent;
            border: 0;
            outline: 0;
            color: #fff;
            min-width: 0;
        }
        .searchbar input::placeholder { color: #c7d2fe; }
        .searchbar button {
            border: 0;
            border-radius: 999px;
            background: #4f46e5;
            color: #fff;
            padding: 8px 18px;
            font-weight: 700;
            white-space: nowrap;
            cursor: pointer;
        }

        .profile {
            display: flex;
            align-items: center;
            gap: 12px;
            text-decoration: none;
            color: #fff;
            flex: 0 0 auto;
        }
        .avatar { width: 42px; height: 42px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.25); }

        .wrap { padding: 28px; max-width: 1500px; margin: 0 auto; }

        .hero {
            display: flex;
            justify-content: space-between;
            gap: 16px;
            align-items: flex-start;
            margin-bottom: 22px;
        }
        .hero h1 { margin: 0; font-size: 1.9rem; font-weight: 800; letter-spacing: -0.03em; }
        .hero p { margin: 6px 0 0; color: #64748b; }

        .cardx {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 22px;
            box-shadow: 0 10px 24px rgba(15,23,42,.05);
        }

        /* ── Filters ── */
        .filters {
            padding: 18px;
            display: grid;
            grid-template-columns: 1.2fr .7fr .7fr .5fr auto;
            gap: 12px;
        }
        .filters input, .filters select {
            width: 100%;
            border: 1px solid #d1d5db;
            border-radius: 14px;
            padding: 12px 14px;
            background: #fff;
            outline: none;
            font-family: inherit;
            font-size: .95rem;
        }
        .filters input:focus, .filters select:focus {
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99,102,241,.12);
        }
        .filters button {
            border: 0;
            border-radius: 14px;
            background: #4f46e5;
            color: #fff;
            font-weight: 700;
            padding: 12px 18px;
            cursor: pointer;
            font-family: inherit;
        }
        .filters button:hover { background: #4338ca; }

        /* ── Live-search result count ── */
        #resultCount {
            font-size: .9rem;
            color: #64748b;
            margin-bottom: 14px;
            min-height: 22px;
        }

        /* ── Grid ── */
        .grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 18px;
        }

        .item { overflow: hidden; transition: opacity .2s; }
        .item.hidden { display: none; }

        .thumb { height: 210px; background: #eef2ff; position: relative; }
        .thumb img { width: 100%; height: 100%; object-fit: cover; display: block; }
        .thumb .ph {
            width: 100%; height: 100%;
            display: flex; align-items: center; justify-content: center;
            color: #6366f1; font-size: 3rem;
            background: linear-gradient(135deg, #e0e7ff, #f8fafc);
        }

        /* Heart button overlay */
        .fav-btn {
            position: absolute;
            top: 12px;
            right: 12px;
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: rgba(255,255,255,.92);
            border: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 1.1rem;
            color: #94a3b8;
            transition: all .2s;
            box-shadow: 0 2px 8px rgba(0,0,0,.1);
            z-index: 2;
        }
        .fav-btn:hover { transform: scale(1.12); }
        .fav-btn.active { color: #ef4444; border-color: #fecaca; background: #fff1f2; }
        .fav-btn.loading { pointer-events: none; opacity: .6; }

        .body { padding: 18px; }
        .meta { display: flex; justify-content: space-between; gap: 10px; align-items: flex-start; margin-bottom: 10px; }
        .meta h3 { font-size: 1.05rem; font-weight: 800; margin: 0; line-height: 1.25; }
        .price { font-weight: 800; color: #4f46e5; white-space: nowrap; }
        .small { color: #64748b; font-size: .92rem; }

        .chips { display: flex; gap: 8px; flex-wrap: wrap; margin: 12px 0; }
        .chip { font-size: .78rem; padding: 6px 10px; border-radius: 999px; font-weight: 700; }

        .foot { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; gap: 12px; }

        .btnline {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 10px 14px; border-radius: 12px; text-decoration: none;
            background: #eef2ff; color: #3730a3; font-weight: 700; white-space: nowrap;
        }
        .btnline:hover { background: #e0e7ff; color: #3730a3; }

        .empty { padding: 40px; text-align: center; color: #64748b; }

        /* ── Sidebar active state on mobile ── */
        .sidebar.active { transform: translateX(0) !important; }

        @media (max-width: 1100px) {
            .grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filters { grid-template-columns: 1fr 1fr; }
            .sidebar { transform: translateX(-100%); }
            .main { margin-left: 0; width: 100%; }
            .mobile-toggle { display: inline-flex; }
        }

        @media (max-width: 700px) {
            .grid { grid-template-columns: 1fr; }
            .hero { flex-direction: column; }
            .top { padding: 12px 16px; }
            .wrap { padding: 16px; }
            .searchbar { display: none; }
            .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

<div class="app">
    <aside class="sidebar" id="sidebar">
        <div class="brand">
            <i class="fa fa-shopping-bag"></i> CampusExchange
        </div>
        <nav class="menu">
            <a href="index.php"><i class="fa fa-home"></i> Home</a>
            <a href="categories.php"><i class="fa fa-th-large"></i> Categories</a>
            <a href="listings.php" class="active"><i class="fa fa-list"></i> All Listings</a>
            <a href="post_item.php"><i class="fa fa-plus-circle"></i> Post Item</a>
            <a href="favorites.php"><i class="fa fa-heart"></i> Favorites</a>
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

            <!-- Top search bar (large screens) -->
            <form class="searchbar" id="topSearchForm" method="get">
                <i class="fa fa-search"></i>
                <input type="text" name="search" id="topSearchInput" placeholder="Search textbooks, electronics, notes..." value="<?= admin_e($search) ?>">
                <input type="hidden" name="category" value="<?= (int) $categoryId ?>">
                <input type="hidden" name="type" value="<?= admin_e($type) ?>">
                <input type="hidden" name="sort" value="<?= admin_e($sort) ?>">
                <button type="submit">Search</button>
            </form>

            <a class="profile" href="profile.php">
                <img class="avatar" src="<?= admin_e($userAvatar) ?>" alt="Profile">
                <div class="d-none d-md-block text-end">
                    <div style="font-weight:700"><?= admin_e($user['full_name'] ?? 'User') ?></div>
                    <div style="font-size:.8rem;color:#a5b4fc">
                        <?= !empty($user['is_verified']) ? 'Verified Student' : 'Student' ?>
                    </div>
                </div>
            </a>
        </header>

        <div class="wrap">
            <div class="hero">
                <div>
                    <h1>Browse Listings</h1>
                    <p>Find safe, active campus marketplace posts.</p>
                </div>
                <a href="post_item.php" class="btnline">
                    <i class="fa fa-plus"></i> Post Item
                </a>
            </div>

            <!-- Filter bar -->
            <div class="cardx mb-4">
                <form class="filters" method="get" id="filterForm">
                    <input type="search" name="search" id="filterSearch" placeholder="Search listings..." value="<?= admin_e($search) ?>">
                    <select name="category" id="filterCategory">
                        <option value="0">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= (int) $cat['id'] ?>" <?= $categoryId === (int) $cat['id'] ? 'selected' : '' ?>>
                                <?= admin_e($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <select name="type" id="filterType">
                        <option value="">All Types</option>
                        <option value="sell"     <?= $type === 'sell'     ? 'selected' : '' ?>>Sell</option>
                        <option value="exchange" <?= $type === 'exchange' ? 'selected' : '' ?>>Exchange</option>
                        <option value="donate"   <?= $type === 'donate'   ? 'selected' : '' ?>>Donate</option>
                    </select>
                    <select name="sort" id="filterSort">
                        <option value="newest"     <?= $sort === 'newest'     ? 'selected' : '' ?>>Newest</option>
                        <option value="oldest"     <?= $sort === 'oldest'     ? 'selected' : '' ?>>Oldest</option>
                        <option value="price_low"  <?= $sort === 'price_low'  ? 'selected' : '' ?>>Price: Low</option>
                        <option value="price_high" <?= $sort === 'price_high' ? 'selected' : '' ?>>Price: High</option>
                    </select>
                    <button type="submit">Apply</button>
                </form>
            </div>

            <!-- Live result count -->
            <div id="resultCount"></div>

            <?php if (!empty($listings)): ?>
                <section class="grid" id="listingsGrid">
                    <?php foreach ($listings as $listing): ?>
                        <?php
                            [$label, $chipStyle] = ce_badge((string) ($listing['listing_type'] ?? ''));
                            $primaryImage = ce_listing_image_src((string) ($listing['primary_image'] ?? ''));
                            $priceValue   = (float) ($listing['price'] ?? 0);
                            $priceLabel   = strtolower((string) ($listing['listing_type'] ?? '')) === 'donate'
                                ? 'Free'
                                : '₹' . number_format($priceValue, 2);
                            $isFav        = (int) ($listing['is_favorited'] ?? 0) > 0;
                            $favCount     = (int) ($listing['favorite_count'] ?? 0);
                        ?>
                        <article class="cardx item"
                            data-title="<?= strtolower(admin_e($listing['title'])) ?>"
                            data-desc="<?= strtolower(admin_e($listing['description'] ?? '')) ?>"
                            data-location="<?= strtolower(admin_e($listing['location'] ?? '')) ?>"
                            data-category="<?= (int) $listing['category_id'] ?>"
                            data-type="<?= admin_e($listing['listing_type'] ?? '') ?>">

                            <div class="thumb">
                                <?php if ($primaryImage !== ''): ?>
                                    <img src="<?= admin_e($primaryImage) ?>" alt="<?= admin_e($listing['title']) ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="ph"><i class="fa fa-image"></i></div>
                                <?php endif; ?>

                                <!-- Heart button -->
                                <button class="fav-btn <?= $isFav ? 'active' : '' ?>"
                                    data-id="<?= (int) $listing['id'] ?>"
                                    title="<?= $isFav ? 'Remove from favorites' : 'Add to favorites' ?>">
                                    <i class="fa fa-heart<?= $isFav ? '' : '-o' ?>"></i>
                                </button>
                            </div>

                            <div class="body">
                                <div class="meta">
                                    <div>
                                        <h3><?= admin_e($listing['title']) ?></h3>
                                        <div class="small">
                                            <i class="fa fa-map-marker"></i>
                                            <?= admin_e($listing['location'] ?: 'Campus') ?>
                                        </div>
                                    </div>
                                    <div class="price"><?= admin_e($priceLabel) ?></div>
                                </div>

                                <div class="small">by <?= admin_e($listing['owner_name']) ?></div>

                                <div class="chips">
                                    <span class="chip" style="<?= $chipStyle ?>"><?= admin_e($label) ?></span>
                                    <span class="chip fav-count-chip" style="background:#fff1f2;color:#be123c;"
                                        data-id="<?= (int) $listing['id'] ?>">
                                        <i class="fa fa-heart"></i> <?= $favCount ?>
                                    </span>
                                    <span class="chip" style="background:#eff6ff;color:#1d4ed8;">
                                        <?= admin_e($listing['category_name'] ?? 'Uncategorized') ?>
                                    </span>
                                </div>

                                <p class="small" style="min-height:44px">
                                    <?= admin_e(mb_substr((string) ($listing['description'] ?? ''), 0, 110)) ?>
                                    <?= mb_strlen((string) ($listing['description'] ?? '')) > 110 ? '…' : '' ?>
                                </p>

                                <div class="foot">
                                    <div class="small">
                                        <i class="fa fa-clock-o"></i>
                                        <?= admin_e(date('M d, Y', strtotime($listing['created_at'] ?? 'now'))) ?>
                                    </div>
                                    <a href="listing_details.php?id=<?= (int) $listing['id'] ?>" class="btnline">
                                        View
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <div class="cardx empty" id="emptyState">
                    <i class="fa fa-inbox fa-3x mb-3"></i>
                    <h4>No listings found</h4>
                    <p class="mb-0">Try a different search or post the first item.</p>
                </div>
            <?php endif; ?>

            <!-- Live-search empty state (hidden by default) -->
            <div class="cardx empty" id="liveEmpty" style="display:none">
                <i class="fa fa-search fa-3x mb-3"></i>
                <h4>No results match</h4>
                <p class="mb-0">Try different keywords or clear the filter.</p>
            </div>
        </div>
    </main>
</div>

<script>
/* ─── Sidebar toggle ─── */
const mobileToggle = document.getElementById('mobileToggle');
const sidebar      = document.getElementById('sidebar');

mobileToggle?.addEventListener('click', () => sidebar.classList.toggle('active'));
document.addEventListener('click', (e) => {
    if (window.innerWidth <= 1100 && sidebar && mobileToggle) {
        if (!sidebar.contains(e.target) && !mobileToggle.contains(e.target)) {
            sidebar.classList.remove('active');
        }
    }
});

/* ─── Heart / Favorite toggle ─── */
document.querySelectorAll('.fav-btn').forEach(btn => {
    btn.addEventListener('click', async () => {
        const id = btn.dataset.id;
        btn.classList.add('loading');

        try {
            const fd = new FormData();
            fd.append('listing_id', id);
            const res  = await fetch('toggle_favorite.php', { method: 'POST', body: fd });
            const data = await res.json();

            if (data.success) {
                const icon = btn.querySelector('i');
                if (data.favorited) {
                    btn.classList.add('active');
                    btn.title = 'Remove from favorites';
                    icon.className = 'fa fa-heart';
                } else {
                    btn.classList.remove('active');
                    btn.title = 'Add to favorites';
                    icon.className = 'fa fa-heart-o';
                }

                // Update count chip on the same card
                const chip = document.querySelector(`.fav-count-chip[data-id="${id}"]`);
                if (chip) chip.innerHTML = `<i class="fa fa-heart"></i> ${data.count}`;
            }
        } catch (err) {
            console.error('Favorite toggle failed', err);
        } finally {
            btn.classList.remove('loading');
        }
    });
});

/* ─── Live search + filter (client-side, instant) ─── */
const grid         = document.getElementById('listingsGrid');
const liveEmpty    = document.getElementById('liveEmpty');
const resultCount  = document.getElementById('resultCount');
const filterSearch = document.getElementById('filterSearch');
const filterCat    = document.getElementById('filterCategory');
const filterType   = document.getElementById('filterType');
const filterSort   = document.getElementById('filterSort');

// Keep all cards as an array so we can sort
let allCards = grid ? Array.from(grid.querySelectorAll('.item')) : [];

function applyLiveFilter() {
    if (!grid) return;

    const q    = (filterSearch?.value ?? '').trim().toLowerCase();
    const cat  = filterCat?.value  ?? '0';
    const type = filterType?.value ?? '';

    let visible = 0;

    allCards.forEach(card => {
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

    // Apply client-side sort
    const sort = filterSort?.value ?? 'newest';
    const sorted = [...allCards].filter(c => !c.classList.contains('hidden'));

    if (sort === 'price_low' || sort === 'price_high') {
        sorted.sort((a, b) => {
            const pa = parseFloat(a.querySelector('.price')?.textContent.replace(/[^\d.]/g, '') || '0');
            const pb = parseFloat(b.querySelector('.price')?.textContent.replace(/[^\d.]/g, '') || '0');
            return sort === 'price_low' ? pa - pb : pb - pa;
        });
        sorted.forEach(c => grid.appendChild(c));
    }

    // Show / hide empty state
    if (liveEmpty) liveEmpty.style.display = visible === 0 ? '' : 'none';

    // Result count
    const total = allCards.length;
    if (resultCount) {
        resultCount.textContent = q || cat !== '0' || type
            ? `Showing ${visible} of ${total} listing${total !== 1 ? 's' : ''}`
            : '';
    }
}

// Wire up live listeners
[filterSearch, filterCat, filterType, filterSort].forEach(el => {
    if (!el) return;
    el.addEventListener('input',  applyLiveFilter);
    el.addEventListener('change', applyLiveFilter);
});

// Sync top search bar with filter panel
document.getElementById('topSearchInput')?.addEventListener('input', function () {
    if (filterSearch) {
        filterSearch.value = this.value;
        applyLiveFilter();
    }
});

// Prevent form submit (we're filtering live)
document.getElementById('filterForm')?.addEventListener('submit', e => {
    // Allow submit for sort / category — they need a full reload to apply server-side ORDER BY
    // Only block if just the search changed (handled live)
    // Actually let both work: live for search/type/category, server reload for sort
    const sort = filterSort?.value ?? 'newest';
    if (sort !== '<?= admin_e($sort) ?>') return; // let sort trigger page reload
    e.preventDefault();
    applyLiveFilter();
});

// Run once on load to apply any pre-filled URL params client-side
applyLiveFilter();
</script>

</body>
</html>
