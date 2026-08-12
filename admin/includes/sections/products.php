<?php
// Get category name if filtered
$filteredCategoryName = '';
if ($isFiltered && !empty($cat_filter)) {
    preg_match('/category_id\s*=\s*(\d+)/', $cat_filter, $m);
    if (!empty($m[1])) {
        $catRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT name FROM categories WHERE id=" . (int) $m[1]));
        if ($catRow)
            $filteredCategoryName = $catRow['name'];
    }
}

// Build category → products map from already-fetched $products
$categoryRows = [];
foreach ($products as $p) {
    $catName = $p['category'] ?? 'Other';
    $catId = (int) ($p['category_id'] ?? 0);
    if (!isset($categoryRows[$catName])) {
        $categoryRows[$catName] = ['id' => $catId, 'name' => $catName, 'products' => []];
    }
    $categoryRows[$catName]['products'][] = $p;
}
ksort($categoryRows);
?>
<section id="products" class="content-section">
    <!-- Products Header Bar -->
    <div class="products-header-bar">
        <div class="products-header-left">
            <h2 class="products-main-title">
                <?php if ($filteredCategoryName): ?>
                    <span class="category-filter-pill">
                        <i class="fas fa-tag"></i>
                        <?= htmlspecialchars($filteredCategoryName) ?>
                    </span>
                <?php else: ?>
                    All Products
                <?php endif; ?>
            </h2>
            <span class="products-count-badge" id="productsCount">0 items</span>
        </div>
        <div class="products-header-right">
            <div class="search-wrap">
                <i class="fas fa-search search-icon-inner"></i>
                <input type="text" id="searchProduct" class="products-search-input" placeholder="Search products...">
            </div>
            <select id="sortProducts" class="products-sort-select">
                <option value="name">Sort: Name</option>
                <option value="priceLow">Price: Low → High</option>
                <option value="priceHigh">Price: High → Low</option>
                <option value="discount">Biggest Discount</option>
            </select>
        </div>
    </div>

    <!-- ── SEARCH / FILTER RESULTS VIEW (shown when search active) ── -->
    <div id="productsSearchView" style="display:none;">
        <div id="productsContainer" class="product-grid js-product-grid"></div>
        <div id="loadMoreWrapper" class="load-more-wrapper" style="display:none;">
            <button id="loadMoreProducts" class="load-more-btn">
                <i class="fas fa-chevron-down"></i>
                Load More Products
                <span id="loadMoreCount" class="load-more-count"></span>
            </button>
        </div>
        <div id="noProductsMsg" style="display:none;" class="no-products-state">
            <i class="fas fa-box-open"></i>
            <h4>No products found</h4>
            <p>Try a different search or category</p>
        </div>
    </div>

    <!-- ── CATEGORY SLIDER ROWS (default view) ── -->
    <div id="productsCategoryView">
        <?php if (empty($categoryRows)): ?>
            <div class="no-products-state">
                <i class="fas fa-box-open"></i>
                <h4>No products available</h4>
                <p>Check back soon!</p>
            </div>
        <?php else: ?>

            <?php foreach ($categoryRows as $catName => $catData):
                $rowId = 'catrow_' . preg_replace('/[^a-z0-9]/', '_', strtolower($catName));
                $catProds = $catData['products'];
                $catIdVal = $catData['id'];
                ?>
                <div class="cat-row-section">
                    <div class="cat-row-header">
                        <div class="cat-row-header-left">
                            <span class="cat-row-icon"><i class="fas fa-layer-group"></i></span>
                            <h3 class="cat-row-title"><?= htmlspecialchars($catName) ?></h3>
                            <span class="cat-row-count"><?= count($catProds) ?></span>
                        </div>
                        <button class="cat-row-see-all" onclick="showCategoryProducts(<?= $catIdVal ?>)">
                            See all <i class="fas fa-arrow-right"></i>
                        </button>
                    </div>

                    <div class="cat-slider-wrap" id="<?= $rowId ?>_wrap">
                        <button class="cat-slider-nav cat-slider-prev" onclick="slideRow('<?= $rowId ?>', -1)"
                            aria-label="Previous">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <div class="cat-slider-track" id="<?= $rowId ?>">
                            <!-- JS renders cards here -->
                        </div>
                        <button class="cat-slider-nav cat-slider-next" onclick="slideRow('<?= $rowId ?>', 1)" aria-label="Next">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</section>

<style>
    /* ── Products Header Bar ── */
    .products-header-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        padding: 20px 20px 0;
        margin-bottom: 4px;
    }

    .products-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .products-main-title {
        font-size: 22px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    body.dark-mode .products-main-title {
        color: #f1f5f9;
    }

    .category-filter-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
        padding: 5px 14px;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 700;
    }

    .category-filter-pill i {
        font-size: 12px;
    }

    .products-count-badge {
        background: #f1f5f9;
        color: #64748b;
        font-size: 12px;
        font-weight: 700;
        padding: 4px 12px;
        border-radius: 999px;
        border: 1px solid #e2e8f0;
        white-space: nowrap;
    }

    body.dark-mode .products-count-badge {
        background: #1e293b;
        color: #94a3b8;
        border-color: #334155;
    }

    .products-header-right {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .search-wrap {
        position: relative;
    }

    .search-icon-inner {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
        pointer-events: none;
    }

    .products-search-input {
        padding: 9px 14px 9px 34px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        font-size: 13px;
        color: #1e293b;
        width: 220px;
        outline: none;
        transition: border-color .2s, box-shadow .2s;
    }

    .products-search-input:focus {
        border-color: #7c3aed;
        box-shadow: 0 0 0 3px rgba(124, 58, 237, .12);
    }

    body.dark-mode .products-search-input {
        background: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }

    .products-sort-select {
        padding: 9px 12px;
        border-radius: 10px;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        font-size: 13px;
        color: #1e293b;
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
    }

    .products-sort-select:focus {
        border-color: #7c3aed;
    }

    body.dark-mode .products-sort-select {
        background: #1e293b;
        border-color: #334155;
        color: #f1f5f9;
    }

    /* ── Load More (search view) ── */
    .load-more-wrapper {
        display: flex;
        justify-content: center;
        padding: 24px 20px 36px;
    }

    .load-more-btn {
        display: inline-flex;
        align-items: center;
        gap: 9px;
        padding: 13px 32px;
        background: #fff;
        border: 2px solid #7c3aed;
        color: #7c3aed;
        border-radius: 999px;
        font-size: 14px;
        font-weight: 800;
        cursor: pointer;
        transition: all .22s;
        box-shadow: 0 4px 18px rgba(124, 58, 237, .12);
    }

    .load-more-btn:hover {
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
        border-color: transparent;
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(124, 58, 237, .28);
    }

    .load-more-count {
        background: rgba(124, 58, 237, .12);
        color: #7c3aed;
        padding: 2px 8px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .load-more-btn:hover .load-more-count {
        background: rgba(255, 255, 255, .2);
        color: #fff;
    }

    body.dark-mode .load-more-btn {
        background: #1e293b;
        border-color: #7c3aed;
        color: #c4b5fd;
    }

    /* ── No products ── */
    .no-products-state {
        text-align: center;
        padding: 60px 20px;
        color: #94a3b8;
    }

    .no-products-state i {
        font-size: 52px;
        margin-bottom: 16px;
        display: block;
        opacity: .4;
    }

    .no-products-state h4 {
        font-weight: 700;
        color: #64748b;
        margin-bottom: 6px;
    }

    .no-products-state p {
        font-size: 14px;
    }

    /* ── Search view grid ── */
    .js-product-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 20px;
        padding: 20px;
    }

    /* ════════════════════════════════
   CATEGORY ROW SLIDERS
   ════════════════════════════════ */
    .cat-row-section {
        padding: 24px 20px 8px;
        border-bottom: 1px solid #f1f5f9;
    }

    body.dark-mode .cat-row-section {
        border-bottom-color: #1e293b;
    }

    .cat-row-section:last-child {
        border-bottom: none;
    }

    .cat-row-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
    }

    .cat-row-header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .cat-row-icon {
        width: 34px;
        height: 34px;
        background: linear-gradient(135deg, #7c3aed22, #4f46e522);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #7c3aed;
        font-size: 14px;
    }

    .cat-row-title {
        font-size: 17px;
        font-weight: 800;
        color: #1e293b;
        margin: 0;
    }

    body.dark-mode .cat-row-title {
        color: #f1f5f9;
    }

    .cat-row-count {
        background: #7c3aed;
        color: #fff;
        font-size: 10px;
        font-weight: 800;
        padding: 2px 7px;
        border-radius: 999px;
        min-width: 22px;
        text-align: center;
    }

    .cat-row-see-all {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 7px 16px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        border: 1.5px solid #e2e8f0;
        background: #fff;
        color: #475569;
        cursor: pointer;
        transition: all .2s;
        white-space: nowrap;
    }

    .cat-row-see-all:hover {
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
        border-color: transparent;
    }

    body.dark-mode .cat-row-see-all {
        background: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }

    body.dark-mode .cat-row-see-all:hover {
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
    }

    /* ── Slider wrapper ── */
    .cat-slider-wrap {
        position: relative;
        display: flex;
        align-items: stretch;
    }

    .cat-slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 10;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: #fff;
        border: 1.5px solid #e2e8f0;
        color: #475569;
        font-size: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 4px 16px rgba(0, 0, 0, .1);
        transition: all .2s;
        flex-shrink: 0;
    }

    .cat-slider-nav:hover {
        background: linear-gradient(135deg, #7c3aed, #4f46e5);
        color: #fff;
        border-color: transparent;
        box-shadow: 0 6px 20px rgba(124, 58, 237, .3);
    }

    .cat-slider-prev {
        left: -14px;
    }

    .cat-slider-next {
        right: -14px;
    }

    body.dark-mode .cat-slider-nav {
        background: #1e293b;
        border-color: #334155;
        color: #94a3b8;
    }

    .cat-slider-prev {
        left: -14px;
    }

    .cat-slider-next {
        right: -14px;
    }

    .cat-slider-track {
        display: flex;
        gap: 14px;
        overflow-x: auto;
        scroll-behavior: smooth;
        padding: 8px 22px 16px;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: none;
    }

    .cat-slider-track::-webkit-scrollbar {
        display: none;
    }

    /* ── Slider product card ── */
    .cat-slider-track .product-card {
        flex: 0 0 200px;
        width: 200px;
        min-width: 200px;
        max-width: 200px;
    }

    /* ══════════════════════════════════
       MOBILE — ≤ 768px
    ══════════════════════════════════ */
    @media (max-width: 768px) {

        /* ─ Header bar: two clean rows ─ */
        .products-header-bar {
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
            padding: 14px 14px 0;
        }

        .products-header-left {
            justify-content: space-between;
            width: 100%;
        }

        .products-header-right {
            flex-wrap: nowrap;
            width: 100%;
            gap: 8px;
        }

        .search-wrap {
            flex: 1 1 0;
            min-width: 0;
        }

        .products-search-input {
            width: 100%;
            box-sizing: border-box;
            font-size: 14px;
        }

        .products-sort-select {
            flex-shrink: 0;
            font-size: 13px;
            padding: 9px 10px;
        }

        /* ─ Category rows ─ */
        .cat-row-section {
            padding: 16px 0 6px;
        }

        .cat-row-header {
            margin-bottom: 10px;
            padding: 0 14px;
        }

        .cat-row-title {
            font-size: 15px;
        }

        /* ─ Slider nav: float over track ─ */
        .cat-slider-wrap {
            padding: 0;
        }

        .cat-slider-prev {
            left: 4px;
            z-index: 20;
        }

        .cat-slider-next {
            right: 4px;
            z-index: 20;
        }

        .cat-slider-nav {
            width: 30px;
            height: 30px;
            font-size: 11px;
            background: rgba(255, 255, 255, 0.93);
            box-shadow: 0 2px 10px rgba(0,0,0,.18);
        }

        body.dark-mode .cat-slider-nav {
            background: rgba(15, 23, 42, 0.93);
        }

        /* ─ Slider track ─ */
        .cat-slider-track {
            padding: 6px 14px 14px;
            gap: 12px;
        }

        /* ─ PRODUCT CARD in slider: 160px wide like home section ─ */
        .cat-slider-track .product-card {
            flex: 0 0 160px;
            width: 160px;
            min-width: 160px;
            max-width: 160px;
            border-radius: 14px;
        }

        /* Image: shorter so card stays compact */
        .cat-slider-track .product-card .product-img {
            height: 140px;
        }

        /* Body: tighter padding */
        .cat-slider-track .product-card .product-body {
            padding: 10px 10px 8px;
            gap: 5px;
        }

        /* Title: two-line clamp instead of single-line ellipsis */
        .cat-slider-track .product-card .product-title {
            font-size: 0.8rem;
            white-space: normal;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.3;
            min-height: 2.1em;
        }

        /* Ratings: compact */
        .cat-slider-track .product-card .rating-stars {
            font-size: 0.68rem;
        }

        .cat-slider-track .product-card .stars {
            font-size: 0.68rem;
        }

        .cat-slider-track .product-card .rating-value {
            font-size: 0.68rem;
        }

        /* Price */
        .cat-slider-track .product-card .current-price {
            font-size: 1rem;
            font-weight: 800;
        }

        .cat-slider-track .product-card .old-price {
            font-size: 0.72rem;
        }

        /* Tags: hide to save vertical space */
        .cat-slider-track .product-card .tags-group {
            display: none;
        }

        /* Buttons: ALWAYS side-by-side on mobile, never stacked */
        .cat-slider-track .product-card .product-actions {
            flex-direction: row !important;
            gap: 6px;
            padding-top: 8px;
            margin-top: auto;
        }

        .cat-slider-track .product-card .product-action-btn {
            flex: 1;
            height: 34px;
            font-size: 0.72rem;
            border-radius: 10px;
            gap: 4px;
            width: auto !important;
        }

        .cat-slider-track .product-card .add-cart-btn i {
            font-size: 0.7rem;
        }

        /* Badge & fav: scale down */
        .cat-slider-track .product-card .badge-discount {
            font-size: 0.6rem;
            padding: 0.2rem 0.55rem;
            top: 0.5rem;
            left: 0.5rem;
        }

        .cat-slider-track .product-card .favorite-btn {
            width: 24px;
            height: 24px;
            top: 0.5rem;
            right: 0.5rem;
        }

        .cat-slider-track .product-card .favorite-btn i {
            font-size: 0.78rem;
        }

        /* ─ Search/filter grid ─ */
        .js-product-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 12px;
            padding: 14px;
        }

        /* Grid cards also need button fix */
        #productsContainer .product-card .product-actions {
            flex-direction: row !important;
            gap: 8px;
        }

        #productsContainer .product-card .product-action-btn {
            flex: 1;
            width: auto !important;
            height: 38px;
            font-size: 0.78rem;
        }

        #productsContainer .product-card .tags-group {
            display: none;
        }
    }

    /* ══════════════════════════════════
       SMALL MOBILE — ≤ 420px
    ══════════════════════════════════ */
    @media (max-width: 420px) {
        .products-header-bar {
            padding: 12px 12px 0;
        }

        .products-main-title {
            font-size: 18px;
        }

        .cat-slider-track .product-card {
            flex: 0 0 148px;
            width: 148px;
            min-width: 148px;
            max-width: 148px;
        }

        .cat-slider-track .product-card .product-img {
            height: 130px;
        }

        .cat-slider-track .product-card .product-action-btn {
            height: 32px;
            font-size: 0.68rem;
            border-radius: 9px;
        }

        .cat-row-see-all {
            padding: 5px 11px;
            font-size: 11px;
        }

        .js-product-grid {
            gap: 10px;
            padding: 12px;
        }
    }
</style>

<script>
    // ── Slider scroll helper
    function slideRow(rowId, dir) {
        const track = document.getElementById(rowId);
        if (!track) return;
        const cardWidth = 214; // card + gap
        track.scrollBy({ left: dir * cardWidth * 3, behavior: 'smooth' });
    }

    // ── Category data from PHP
    const categoryProductMap = <?php
    $catMapJS = [];
    foreach ($categoryRows as $catName => $catData) {
        $rowId = 'catrow_' . preg_replace('/[^a-z0-9]/', '_', strtolower($catName));
        $catMapJS[$rowId] = $catData['products'];
    }
    echo json_encode($catMapJS, JSON_HEX_TAG | JSON_HEX_AMP);
    ?>;

    // ── Render all category slider rows
    function renderCategoryRows() {
        // Use the JS `products` array (already has correct image paths from index.php)
        // categoryProductMap just gives us rowId → category_id mapping
        const catIdByRow = {};
        Object.entries(categoryProductMap).forEach(([rowId, phpProds]) => {
            if (phpProds.length > 0) {
                catIdByRow[rowId] = String(phpProds[0].category_id || '');
            }
        });

        Object.entries(catIdByRow).forEach(([rowId, catId]) => {
            const track = document.getElementById(rowId);
            if (!track) return;
            // Use JS products array for correct image paths
            const prods = (typeof products !== 'undefined')
                ? products.filter(p => String(p.category_id) === catId)
                : [];
            track.innerHTML = '';
            prods.forEach(product => {
                const cardHtml = getProductCardHTML(product);
                const wrapper = document.createElement('div');
                wrapper.innerHTML = cardHtml;
                const card = wrapper.firstElementChild;
                if (card) {
                    // Add onerror fallback to all images in this card
                    card.querySelectorAll('img').forEach(img => {
                        if (!img.onerror) img.onerror = function () { this.src = 'assets/images/no-image.png'; };
                    });
                    track.appendChild(card);
                }
            });
            // Update count badge
            const countBadge = track.closest('.cat-row-section')?.querySelector('.cat-row-count');
            if (countBadge) countBadge.textContent = prods.length;
            // Show/hide nav arrows
            const wrap = document.getElementById(rowId + '_wrap');
            if (wrap) {
                const prev = wrap.querySelector('.cat-slider-prev');
                const next = wrap.querySelector('.cat-slider-next');
                const hasScroll = prods.length > 3;
                if (prev) prev.style.display = hasScroll ? 'flex' : 'none';
                if (next) next.style.display = hasScroll ? 'flex' : 'none';
            }
        });
    }

    // ── Switch between category-row view and search/filter view
    function showProductsCategoryView() {
        const catView = document.getElementById('productsCategoryView');
        const searchView = document.getElementById('productsSearchView');
        if (catView) catView.style.display = 'block';
        if (searchView) searchView.style.display = 'none';
        // Update count badge
        const total = (typeof products !== 'undefined') ? products.length : 0;
        const el = document.getElementById('productsCount');
        if (el) el.textContent = total + ' items';
    }

    function showProductsSearchView() {
        const catView = document.getElementById('productsCategoryView');
        const searchView = document.getElementById('productsSearchView');
        if (catView) catView.style.display = 'none';
        if (searchView) searchView.style.display = 'block';
    }

    // Override filterProducts to toggle views
    const _origFilterProducts = typeof filterProducts === 'function' ? filterProducts : null;

    document.addEventListener('DOMContentLoaded', function () {
        // Re-bind search to toggle views
        const searchInput = document.getElementById('searchProduct');
        const sortSelect = document.getElementById('sortProducts');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const val = this.value.trim();
                if (val === '') {
                    showProductsCategoryView();
                } else {
                    showProductsSearchView();
                    if (typeof filterProducts === 'function') filterProducts();
                }
            });
        }

        if (sortSelect) {
            sortSelect.addEventListener('change', function () {
                const searchVal = searchInput ? searchInput.value.trim() : '';
                if (searchVal) {
                    showProductsSearchView();
                }
                if (typeof sortProducts === 'function') sortProducts();
            });
        }

        // renderCategoryRows is called by window._catRowsReady flag watcher below
    });

    // Wait until favorites are loaded AND products JS array exists, then render
    // This avoids race conditions with index.php's DOMContentLoaded init order
    (function waitAndRenderCatRows() {
        const check = setInterval(() => {
            if (
                typeof getProductCardHTML === 'function' &&
                typeof products !== 'undefined' &&
                Array.isArray(products) &&
                products.length > 0 &&
                typeof favorites !== 'undefined'
            ) {
                clearInterval(check);
                renderCategoryRows();
                const total = products.length;
                const el = document.getElementById('productsCount');
                if (el) el.textContent = total + ' items';
            }
        }, 50);
        // Safety timeout — render anyway after 2s
        setTimeout(() => {
            clearInterval(check);
            if (typeof renderCategoryRows === 'function') renderCategoryRows();
        }, 2000);
    })();

    // Patch showCategoryProducts to also switch to search view
    const _origShowCatProducts_patcher = setInterval(() => {
        if (typeof showCategoryProducts === 'function' && !showCategoryProducts._catViewPatched) {
            const orig = showCategoryProducts;
            showCategoryProducts = function (catId, catData) {
                showProductsSearchView();
                orig.call(this, catId, catData);
            };
            showCategoryProducts._catViewPatched = true;
            clearInterval(_origShowCatProducts_patcher);
        }
    }, 50);

    // Patch showAllProducts to go back to category view
    const _origShowAllProducts_patcher = setInterval(() => {
        if (typeof showAllProducts === 'function' && !showAllProducts._catViewPatched) {
            const orig = showAllProducts;
            showAllProducts = function () {
                showProductsCategoryView();
                orig.call(this);
            };
            showAllProducts._catViewPatched = true;
            clearInterval(_origShowAllProducts_patcher);
        }
    }, 50);
</script>