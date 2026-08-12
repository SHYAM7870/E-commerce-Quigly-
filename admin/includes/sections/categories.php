<section id="categories" class="content-section categories-section" style="display:none;">
    <div class="container py-5">
        <div class="categories-hero mb-4">
            <div>
                <span class="section-pill"><i class="fas fa-th-large"></i> Explore Collections</span>
                <h2 class="section-title gradient-text mb-2">All Categories</h2>
                <p class="categories-subtitle">Tap any category to browse its products</p>
            </div>
        </div>

        <div class="categories-grid">
            <?php
            $cat_query = "SELECT id, name, image, details FROM categories ORDER BY id DESC";
            $cat_result = mysqli_query($conn, $cat_query);

            if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                $catIndex = 0;
                while ($cat = mysqli_fetch_assoc($cat_result)) {
                    $catId   = (int)$cat['id'];
                    $catName = htmlspecialchars($cat['name'], ENT_QUOTES);
                    $catDetails = htmlspecialchars($cat['details'], ENT_QUOTES);
                    $featuredClass = ($catIndex === 0) ? ' category-card-feature' : '';
            ?>
                <div class="category-card-grid<?= $featuredClass ?>"
                     onclick="showCategoryProducts(<?= $catId ?>)">
                    <div class="category-image-wrap">
                        <img src="upload/<?php echo htmlspecialchars($cat['image']); ?>"
                             class="category-image"
                             alt="<?= $catName ?>"
                             onerror="this.onerror=null;this.src='assets/images/no-image.png'">
                        <div class="category-overlay"></div>
                        <div class="category-chip"><i class="fas fa-tag"></i> Category</div>

                        <div class="category-content">
                            <h5 class="category-title"><?= $catName ?></h5>
                            <p class="category-text"><?= $catDetails; ?></p>

                            <button class="category-btn"
                                    onclick="event.stopPropagation();showCategoryProducts(<?= $catId ?>)">
                                Explore Collection
                                <i class="fas fa-arrow-right ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            <?php
                    $catIndex++;
                }
            } else {
                echo "<div class='empty-category-state'>No Categories Found</div>";
            }
            ?>
        </div>
    </div>
    <style>
        .categories-section {
    background:
        radial-gradient(circle at top left, rgba(99, 102, 241, 0.12), transparent 28%),
        radial-gradient(circle at top right, rgba(14, 165, 233, 0.10), transparent 24%),
        linear-gradient(180deg, #f8fafc 0%, #eef2ff 100%);
    min-height: 100vh;
}

.categories-hero {
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 20px;
    padding: 24px 0 10px;
}

.section-pill {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    font-size: 0.82rem;
    font-weight: 700;
    color: #4f46e5;
    background: rgba(79, 70, 229, 0.10);
    border: 1px solid rgba(79, 70, 229, 0.14);
    margin-bottom: 14px;
}

.categories-subtitle {
    margin: 0;
    color: #64748b;
    font-size: 1rem;
}

/* ── Premium Grid Layout ── */
.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(270px, 1fr));
    grid-auto-rows: 280px;
    gap: 26px;
}

/* First category gets a larger "hero" tile for a bento-style premium look */
.category-card-feature {
    grid-column: span 2;
    grid-row: span 2;
}

.category-card-grid {
    cursor: pointer;
    border-radius: 26px;
    overflow: hidden;
    position: relative;
    border: 1px solid rgba(255, 255, 255, 0.6);
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.10);
    transition: transform 0.3s ease, box-shadow 0.3s ease, border-color 0.3s ease;
}

.category-card-grid:hover {
    transform: translateY(-8px);
    box-shadow: 0 26px 60px rgba(15, 23, 42, 0.18);
    border-color: rgba(99, 102, 241, 0.25);
}

.category-image-wrap {
    position: relative;
    width: 100%;
    height: 100%;
    overflow: hidden;
    background: #e2e8f0;
}

.category-image {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
}

.category-card-grid:hover .category-image {
    transform: scale(1.08);
}

.category-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(180deg, rgba(15, 23, 42, 0.05) 35%, rgba(15, 23, 42, 0.55) 78%, rgba(15, 23, 42, 0.82) 100%);
    transition: opacity .3s ease;
}

.category-card-grid:hover .category-overlay {
    background: linear-gradient(180deg, rgba(79, 70, 229, 0.10) 30%, rgba(15, 23, 42, 0.62) 75%, rgba(15, 23, 42, 0.88) 100%);
}

.category-chip {
    position: absolute;
    top: 16px;
    left: 16px;
    z-index: 2;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 7px 13px;
    border-radius: 999px;
    font-size: 0.74rem;
    font-weight: 800;
    letter-spacing: 0.03em;
    text-transform: uppercase;
    color: #fff;
    background: rgba(15, 23, 42, 0.35);
    border: 1px solid rgba(255, 255, 255, 0.22);
    backdrop-filter: blur(10px);
}

.category-content {
    position: absolute;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 2;
    padding: 20px;
    display: flex;
    flex-direction: column;
    gap: 6px;
    transform: translateY(8px);
    transition: transform .3s ease;
}

.category-card-grid:hover .category-content {
    transform: translateY(0);
}

.category-title {
    font-size: 1.25rem;
    font-weight: 900;
    color: #fff;
    margin: 0;
    letter-spacing: -0.02em;
    text-shadow: 0 2px 12px rgba(0,0,0,.35);
}

.category-card-feature .category-title {
    font-size: 1.65rem;
}

.category-text {
    color: rgba(255, 255, 255, 0.86);
    font-size: 0.9rem;
    line-height: 1.5;
    margin: 0;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    max-height: 0;
    opacity: 0;
    transition: max-height .35s ease, opacity .25s ease, margin .35s ease;
}

.category-card-grid:hover .category-text,
.category-card-feature .category-text {
    max-height: 60px;
    opacity: 1;
    margin-top: 2px;
    margin-bottom: 4px;
}

.category-btn {
    display: inline-flex;
    align-items: center;
    align-self: flex-start;
    gap: 8px;
    border: none;
    border-radius: 999px;
    font-weight: 800;
    font-size: 0.84rem;
    padding: 10px 18px;
    margin-top: 10px;
    color: #fff;
    background: linear-gradient(135deg, #4f46e5, #7c3aed);
    box-shadow: 0 12px 26px rgba(79, 70, 229, 0.35);
    transition: transform 0.2s ease, box-shadow 0.2s ease, background .2s ease;
    opacity: 0;
    transform: translateY(6px);
}

.category-card-grid:hover .category-btn,
.category-card-feature .category-btn {
    opacity: 1;
    transform: translateY(0);
}

.category-btn:hover {
    transform: translateY(-2px) scale(1.03);
    box-shadow: 0 16px 34px rgba(79, 70, 229, 0.45);
}

.empty-category-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 56px 20px;
    border-radius: 22px;
    background: rgba(255, 255, 255, 0.78);
    border: 1px solid rgba(148, 163, 184, 0.16);
    color: #64748b;
    font-weight: 600;
    box-shadow: 0 12px 32px rgba(15, 23, 42, 0.06);
}

/* ── Dark mode ── */
body.dark-mode .categories-section {
    background:
        radial-gradient(circle at top left, rgba(99, 102, 241, 0.16), transparent 28%),
        radial-gradient(circle at top right, rgba(14, 165, 233, 0.12), transparent 24%),
        linear-gradient(180deg, #0b1120 0%, #111827 100%);
}
body.dark-mode .categories-subtitle { color: #cbd5e1; }
body.dark-mode .category-card-grid { border-color: rgba(148,163,184,.14); }
body.dark-mode .empty-category-state { background: rgba(17,24,39,.82); border-color: rgba(148,163,184,.18); color:#9ca3af; }

/* ── Responsive ── */
@media (max-width: 991.98px) {
    .categories-grid {
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        grid-auto-rows: 240px;
        gap: 20px;
    }
    .category-card-feature { grid-column: span 2; grid-row: span 1; }
    .category-card-feature .category-title { font-size: 1.25rem; }
}

@media (max-width: 768px) {
    .categories-hero {
        align-items: flex-start;
        flex-direction: column;
    }
}

@media (max-width: 576px) {
    .categories-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
        grid-auto-rows: 190px;
        gap: 14px;
    }
    .category-card-grid,
    .empty-category-state {
        border-radius: 18px;
    }
    .category-card-feature { grid-column: span 2; grid-row: span 1; }
    .category-content { padding: 14px; }
    .category-title { font-size: 1rem; }
    .category-card-feature .category-title { font-size: 1.2rem; }
    .category-text,
    .category-card-grid:hover .category-text { max-height: 0; opacity: 0; }
    .category-btn,
    .category-card-grid:hover .category-btn { opacity: 1; transform: translateY(0); padding: 8px 14px; font-size: 0.76rem; }
    .category-chip { font-size: 0.66rem; padding: 5px 10px; top: 10px; left: 10px; }
}
    </style>
</section>