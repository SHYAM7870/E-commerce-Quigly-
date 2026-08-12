<?php
$bannerRows = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='banner' AND status=1 ORDER BY sort_order ASC, id DESC");
$brandRows = mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='brand' AND status=1 ORDER BY sort_order ASC, id DESC");
$logoRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM homepage_media WHERE type='logo' AND status=1 ORDER BY sort_order ASC, id DESC LIMIT 1"));
$banner = mysqli_fetch_assoc($bannerRows);
?>
<section id="home" class="content-section">
    <style>
    /* ============================================
   HOME SECTION — PREMIUM ECOMMERCE REDESIGN
   Clean full CSS replace
   ============================================ */

    /* ---- BASE ---- */
    #home {
        position: relative;
        overflow: hidden;
    }

    #home * {
        box-sizing: border-box;
    }

    .section-wrap {
        padding: 72px 0;
    }

    .section-head {
        margin-bottom: 28px;
    }

    .section-title {
        margin-bottom: 8px;
        font-weight: 900;
        letter-spacing: -.03em;
    }

    .section-subtitle {
        color: var(--text-secondary, #64748b);
        max-width: 820px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .section-pill {
        display: inline-flex;
        align-items: center;
        padding: 7px 12px;
        border-radius: 999px;
        font-size: .8rem;
        font-weight: 800;
        color: #4f46e5;
        background: rgba(79, 70, 229, .10);
        border: 1px solid rgba(79, 70, 229, .14);
        margin-bottom: 10px;
    }

    .gradient-text {
        background: linear-gradient(90deg, #4f46e5 0%, #7c3aed 50%, #a855f7 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* ---- HERO BANNER ---- */
    .home-hero {
        position: relative;
        padding: 0;
        min-height: 520px;
        overflow: hidden;
        background:
            radial-gradient(ellipse 70% 60% at 80% 40%, rgba(120, 40, 200, .55) 0%, transparent 65%),
            radial-gradient(ellipse 50% 50% at 10% 20%, rgba(80, 20, 160, .40) 0%, transparent 60%),
            linear-gradient(160deg, #0d0620 0%, #130930 40%, #0a0518 100%);
    }

    .hero-neon-ring {
        position: absolute;
        top: -40px;
        right: -40px;
        width: 420px;
        height: 420px;
        border-radius: 50%;
        border: 3px solid transparent;
        background: conic-gradient(from 220deg, #b040ff, #7020d0, transparent 60%) border-box;
        -webkit-mask: linear-gradient(#fff 0 0) padding-box, linear-gradient(#fff 0 0);
        -webkit-mask-composite: destination-out;
        mask-composite: exclude;
        filter: blur(1px);
        pointer-events: none;
        z-index: 1;
    }

    .hero-neon-ring::after {
        content: '';
        position: absolute;
        inset: 18px;
        border-radius: 50%;
        border: 2px solid rgba(180, 80, 255, .18);
    }

    .hero-banner-inner {
        position: relative;
        z-index: 2;
        padding: 52px 0 0;
    }

    .hero-discount-badge {
        position: absolute;
        top: 28px;
        right: 28px;
        width: 96px;
        height: 96px;
        border-radius: 50%;
        background: transparent;
        border: 2.5px dashed rgba(255, 255, 255, .55);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-align: center;
        font-weight: 900;
        z-index: 10;
        line-height: 1.1;
    }

    .hero-discount-badge .up-to {
        font-size: .68rem;
        font-weight: 700;
        opacity: .85;
    }

    .hero-discount-badge .pct {
        font-size: 1.85rem;
        font-weight: 900;
        line-height: 1;
    }

    .hero-discount-badge .off {
        font-size: .72rem;
        font-weight: 700;
        opacity: .85;
    }

    .hero-badge-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border-radius: 999px;
        font-size: .82rem;
        font-weight: 800;
        color: #fff;
        background: rgba(255, 255, 255, .12);
        border: 1.5px solid rgba(255, 255, 255, .22);
        backdrop-filter: blur(10px);
        letter-spacing: .04em;
        margin-bottom: 22px;
    }

    .hero-badge-pill i {
        color: #d080ff;
        font-size: .88rem;
    }

    .hero-title-main {
        font-size: clamp(2rem, 4.5vw, 3.8rem);
        line-height: 1;
        letter-spacing: -.03em;
        font-weight: 900;
        color: #fff;
        margin: 0 0 4px;
        text-transform: uppercase;
    }

    .hero-title-accent {
        font-size: clamp(2rem, 4.5vw, 3.8rem);
        line-height: 1;
        letter-spacing: -.03em;
        font-weight: 900;
        background: linear-gradient(90deg, #9b3dff 0%, #c060ff 50%, #a030ff 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        text-transform: uppercase;
        margin: 0 0 18px;
        display: block;
    }

    .hero-copy-text {
        font-size: 1rem;
        line-height: 1.65;
        color: rgba(255, 255, 255, .72);
        max-width: 480px;
        margin-bottom: 22px;
    }

    .hero-btn-row {
        display: flex;
        gap: 14px;
        flex-wrap: wrap;
    }

    .btn-hero-primary,
    .btn-hero-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 13px 28px;
        border-radius: 10px;
        font-weight: 800;
        font-size: .92rem;
        text-decoration: none;
        letter-spacing: .04em;
        text-transform: uppercase;
        transition: transform .22s ease, box-shadow .22s ease, background .22s ease, border-color .22s ease;
    }
.features-card, 
.shipping-card { 
    display: flex;
    flex-direction: column;
    align-items: center;     
    justify-content: center;
    text-align: center;      
    padding: 24px;           
}
.features-card .icon-box,
.features-card i {  
    margin: 0 auto 15px auto; 
}
    .btn-hero-primary {
        color: #fff;
        background: linear-gradient(135deg, #8b20ff, #6010d0);
        border: none;
        cursor: pointer;
        box-shadow: 0 10px 28px rgba(130, 30, 255, .38);
    }

    .btn-hero-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 16px 36px rgba(130, 30, 255, .50);
        color: #fff;
    }

    .btn-hero-outline {
        color: #fff;
        background: transparent;
        border: 2px solid rgba(255, 255, 255, .30);
        cursor: pointer;
    }

    .btn-hero-outline:hover {
        transform: translateY(-2px);
        border-color: rgba(255, 255, 255, .55);
        background: rgba(255, 255, 255, .06);
        color: #fff;
    }

    .hero-products-col {
        position: relative;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        min-height: 420px;
        padding-bottom: 0;
    }

    .hero-product-img {
        width: 100%;
        max-width: 580px;
        height: 440px;
        object-fit: contain;
        object-position: bottom center;
        position: relative;
        z-index: 3;
        filter: drop-shadow(0 20px 60px rgba(120, 30, 220, .40));
        margin-bottom: -4px;
    }

    /* Stats bar */
    .hero-stats-bar {
        background: rgba(255, 255, 255, .055);
        border-top: 1px solid rgba(255, 255, 255, .09);
        backdrop-filter: blur(12px);
        padding: 20px 0;
        position: relative;
        z-index: 4;
    }

    .stats-bar-inner {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0;
    }

    .stat-bar-item {
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 4px 24px;
        border-right: 1px solid rgba(255, 255, 255, .10);
        color: #fff;
    }

    .stat-bar-item:last-child {
        border-right: none;
    }

    .stat-bar-item .stat-icon {
        font-size: 1.5rem;
        color: #a060ff;
        flex-shrink: 0;
    }

    .stat-bar-item .stat-val {
        font-size: 1.25rem;
        font-weight: 900;
        line-height: 1.1;
    }

    .stat-bar-item .stat-lbl {
        font-size: .78rem;
        color: rgba(255, 255, 255, .52);
        line-height: 1.3;
    }

    /* ---- CATEGORY SECTION ---- */
    .categories-wrapper {
        width: 100%;
        margin-top: 10px;
    }

    .categories-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 22px;
        width: 100%;
    }

    .category-card {
        position: relative;
        overflow: hidden;
        border-radius: 24px;
        background: rgba(255, 255, 255, .90);
        border: 1px solid rgba(148, 163, 184, .18);
        box-shadow: 0 14px 34px rgba(15, 23, 42, .08);
        cursor: pointer;
        transition: transform .28s ease, box-shadow .28s ease, border-color .28s ease;
        min-height: 100%;
    }

    .category-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 22px 48px rgba(15, 23, 42, .14);
        border-color: rgba(99, 102, 241, .24);
    }

    .category-image-wrap {
        position: relative;
        height: 220px;
        overflow: hidden;
        background: linear-gradient(135deg, #e2e8f0, #f8fafc);
    }

    .category-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        display: block;
        transition: transform .38s ease;
    }

    .category-card:hover .category-image {
        transform: scale(1.08);
    }

    .category-overlay {
        position: absolute;
        inset: 0;
        background: linear-gradient(180deg, rgba(15, 23, 42, .02), rgba(15, 23, 42, .36));
    }

    .category-chip {
        position: absolute;
        top: 14px;
        left: 14px;
        z-index: 2;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        color: #fff;
        background: rgba(15, 23, 42, .40);
        border: 1px solid rgba(255, 255, 255, .16);
        backdrop-filter: blur(10px);
    }

    .category-badge-count {
        position: absolute;
        top: 14px;
        right: 14px;
        z-index: 2;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: .72rem;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        border: 1px solid rgba(255, 255, 255, .16);
        backdrop-filter: blur(10px);
    }

    .category-body {
        padding: 18px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .category-title {
        font-size: 1.08rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -.02em;
    }

    .category-text {
        color: #64748b;
        font-size: .9rem;
        line-height: 1.5;
        margin: 0;
        min-height: 42px;
    }

    .category-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        padding: 11px 14px;
        font-weight: 800;
        color: #fff;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        box-shadow: 0 10px 22px rgba(79, 70, 229, .18);
        transition: transform .2s ease, box-shadow .2s ease, opacity .2s ease;
    }

    .category-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 14px 26px rgba(79, 70, 229, .28);
        opacity: .98;
    }

    /* ---- PRODUCT SLIDER ---- */
    .horizontal-slider {
        position: relative;
    }

    .slider-track {
        display: flex;
        gap: 20px;
        overflow-x: auto;
        scroll-behavior: smooth;
        scroll-snap-type: x mandatory;
        padding: 8px 48px 16px;
        scrollbar-width: none;
        -ms-overflow-style: none;
    }

    .slider-track::-webkit-scrollbar {
        display: none;
    }

    .slider-track>* {
        flex: 0 0 280px;
        scroll-snap-align: start;
    }

    .brand-slider>* {
        flex: 0 0 180px;
    }

    .slider-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 50%;
        background: rgba(15, 23, 42, .88);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 5;
        box-shadow: 0 10px 24px rgba(15, 23, 42, .22);
        transition: background .2s ease, transform .2s ease;
    }

    .slider-nav:hover {
        background: rgba(79, 70, 229, .95);
    }

    .slider-nav.prev {
        left: 0;
    }

    .slider-nav.next {
        right: 0;
    }

    /* ---- DEMO PRODUCT CARD ---- */
    .demo-product-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(5, 5, 5, .09);
        transition: transform .3s ease, box-shadow .3s ease;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        min-height: 380px;
        position: relative;
    }

    .demo-product-card:hover {
        transform: translateY(-7px);
        box-shadow: 0 20px 48px rgba(0, 0, 0, .18);
    }

    .demo-product-img-wrap {
        position: relative;
        width: 100%;
        height: 220px;
        overflow: hidden;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    .demo-product-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center top;
        display: block;
        transition: transform .35s ease;
    }

    .demo-product-card:hover .demo-product-img-wrap img {
        transform: scale(1.06);
    }

    .demo-badge-off,
    .demo-badge-feat,
    .demo-badge-trend {
        position: absolute;
        top: 10px;
        left: 10px;
        z-index: 5;
        color: #fff;
        font-size: .68rem;
        font-weight: 800;
        padding: .28rem .7rem;
        border-radius: 2rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, .2);
    }

    .demo-badge-off {
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }

    .demo-badge-feat {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
    }

    .demo-badge-trend {
        background: linear-gradient(135deg, #f59e0b, #d97706);
    }

    .demo-fav-btn {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 5;
        width: 32px;
        height: 32px;
        border: none;
        background: rgba(255, 255, 255, .9);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .12);
        color: #94a3b8;
        font-size: .9rem;
        transition: all .2s ease;
    }

    .demo-fav-btn:hover {
        background: #fff;
        color: #f43f5e;
        transform: scale(1.1);
    }

    .demo-product-body {
        padding: 14px;
        display: flex;
        flex-direction: column;
        gap: 8px;
        flex: 1;
    }

    .demo-product-cat {
        font-size: .68rem;
        font-weight: 700;
        color: #7c3aed;
        text-transform: uppercase;
        letter-spacing: .05em;
    }

    .demo-product-name {
        font-size: .98rem;
        font-weight: 700;
        color: #111827;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        margin: 0;
    }

    .demo-rating-row {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .demo-stars {
        color: #fbbf24;
        font-size: .72rem;
    }

    .demo-rating-val {
        font-size: .72rem;
        color: #6b7280;
    }

    .demo-price-row {
        display: flex;
        align-items: baseline;
        gap: 8px;
        flex-wrap: wrap;
        margin-top: 2px;
    }

    .demo-price-current {
        font-size: 1.18rem;
        font-weight: 800;
        color: #7c3aed;
    }

    .demo-price-old {
        font-size: .82rem;
        color: #9ca3af;
        text-decoration: line-through;
    }

    .demo-product-actions {
        display: flex;
        gap: 8px;
        margin-top: auto;
        padding-top: 10px;
    }

    .demo-btn-cart,
    .demo-btn-buy {
        flex: 1;
        height: 40px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: .85rem;
        cursor: pointer;
        transition: all .25s ease;
    }

    .demo-btn-cart {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .demo-btn-cart:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(124, 58, 237, .35);
    }

    .demo-btn-buy {
        background: linear-gradient(135deg, #111827, #1f2937);
        color: #fff;
    }

    .demo-btn-buy:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(17, 24, 39, .3);
    }

    /* ---- EMPTY SLIDER STATE ---- */
    .empty-slider-state {
        text-align: center;
        padding: 48px 24px;
        border-radius: 18px;
        background: rgba(255, 255, 255, .7);
        border: 1px dashed rgba(148, 163, 184, .3);
        color: #64748b;
        font-weight: 600;
        width: 100%;
        min-width: 300px;
    }

    /* ---- BRAND GRID ---- */
    .brand-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 18px;
        align-items: center;
    }

    .brand-box {
        height: 96px;
        border-radius: 20px;
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        box-shadow: var(--shadow-sm, 0 8px 20px rgba(0, 0, 0, .06));
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .brand-box:hover {
        transform: translateY(-3px);
        box-shadow: var(--shadow-md, 0 14px 28px rgba(0, 0, 0, .10));
    }

    .brand-box img {
        max-width: 100%;
        max-height: 44px;
        object-fit: contain;
        opacity: .9;
    }

    .brand-logo {
        filter: grayscale(100%) contrast(1.2);
    }

    /* ---- TRUST CARDS ---- */
    .trust-card {
        /* display: flex;
        justify-content: column;
        align-items: center;
        justify-content: center; */
        padding: 28px 20px;
        border-radius: 22px;
        background: var(--bg-card, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        box-shadow: var(--shadow-md, 0 12px 28px rgba(0, 0, 0, .08));
        height: 100%;
        transition: transform .25s ease, box-shadow .25s ease;
    }

    .trust-card:hover {
        transform: translateY(-4px);
        box-shadow: var(--shadow-lg, 0 18px 38px rgba(0, 0, 0, .12));
    }

    .trust-icon {
        width: 62px;
        height: 62px;
        margin: 0 0 16px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 1.25rem;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        box-shadow: 0 14px 30px rgba(124, 58, 237, .26);
    }

    .trust-card h5 {
        color: var(--text-primary, #0f172a);
        margin-bottom: 8px;
        font-weight: 800;
    }

    .trust-card p {
        color: var(--text-secondary, #64748b);
        margin-bottom: 0;
    }

    .divider-soft {
        height: 1px;
        background: linear-gradient(90deg, transparent, rgba(148, 163, 184, .35), transparent);
    }

    /* ---- DARK MODE OVERRIDES ---- */
    body.dark-mode .demo-product-card {
        background: #1e1b2e;
        border-color: #2d2845;
        box-shadow: 0 10px 30px rgba(0, 0, 0, .35);
    }

    body.dark-mode .demo-product-name {
        color: #f1f5f9;
    }

    body.dark-mode .demo-product-cat {
        color: #a78bfa;
    }

    body.dark-mode .category-card {
        background: rgba(30, 27, 46, .92);
        border-color: rgba(80, 60, 140, .28);
    }

    body.dark-mode .category-title {
        color: #f8fafc;
    }

    body.dark-mode .category-text {
        color: #94a3b8;
    }

    body.dark-mode .empty-slider-state {
        background: rgba(30, 27, 46, .7);
        color: #94a3b8;
    }

    body.dark-mode .demo-fav-btn {
        background: rgba(30, 27, 46, .9);
        color: #6b7280;
    }

    body.dark-mode .brand-box,
    body.dark-mode .trust-card {
        background: rgba(30, 27, 46, .92);
        border-color: rgba(80, 60, 140, .22);
    }

    body.dark-mode .trust-card h5 {
        color: #f8fafc;
    }

    body.dark-mode .trust-card p {
        color: #94a3b8;
    }

    /* ---- RESPONSIVE ---- */
    @media (max-width: 991px) {
        .hero-products-col {
            min-height: 280px;
        }

        .hero-product-img {
            height: 300px;
        }

        .hero-discount-badge {
            width: 80px;
            height: 80px;
            top: 16px;
            right: 16px;
        }

        .hero-discount-badge .pct {
            font-size: 1.45rem;
        }

        .stats-bar-inner {
            grid-template-columns: repeat(2, 1fr);
        }

        .stat-bar-item {
            padding: 10px 16px;
        }

        .stat-bar-item:nth-child(2) {
            border-right: none;
        }

        .categories-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 16px;
        }

        .category-image-wrap {
            height: 190px;
        }
    }

    @media (max-width: 767px) {
        .section-wrap {
            padding: 52px 0;
        }

        .hero-banner-inner {
            padding: 36px 0 0;
        }

        .hero-title-main,
        .hero-title-accent {
            font-size: 2.1rem;
        }

        .hero-copy-text {
            max-width: 100%;
        }

        .hero-neon-ring {
            width: 260px;
            height: 260px;
        }

        .slider-track {
            padding: 6px 0 10px;
        }

        .slider-nav {
            display: none;
        }

        .slider-track>* {
            flex-basis: 260px;
        }

        .brand-slider>* {
            flex-basis: 140px;
        }

        .category-body {
            padding: 14px;
        }

        .category-title {
            font-size: 1rem;
        }

        .category-text {
            font-size: .82rem;
            min-height: 36px;
        }

        .category-btn {
            padding: 10px 12px;
            font-size: .9rem;
        }

        .demo-product-card {
            min-height: 360px;
        }
    }

    @media (max-width: 480px) {
        .categories-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 10px;
        }

        .category-card {
            border-radius: 18px;
        }

        .category-image-wrap {
            height: 145px;
        }

        .category-chip,
        .category-badge-count {
            font-size: .65rem;
            padding: 5px 8px;
        }

        .category-body {
            padding: 12px;
            gap: 8px;
        }

        .category-title {
            font-size: .92rem;
        }

        .category-text {
            font-size: .76rem;
            min-height: 32px;
        }

        .category-btn {
            padding: 9px 10px;
            font-size: .82rem;
        }

        .hero-title-main,
        .hero-title-accent {
            font-size: 1.85rem;
        }

        .hero-copy-text {
            font-size: .93rem;
        }

        .btn-hero-primary,
        .btn-hero-outline {
            width: 100%;
            justify-content: center;
        }
    }
    </style>

    <script>
    function slideSection(id, direction) {
        const el = document.getElementById(id);
        if (!el) return;
        const amount = Math.max(300, el.clientWidth * 0.8);
        el.scrollBy({
            left: amount * direction,
            behavior: 'smooth'
        });
    }

    // Static demo products by category — shown as fallback and supplemental content
    const demoProducts = {
        electronics: [{
                name: 'iPhone 15 Pro Max',
                cat: 'Electronics',
                price: '₹1,29,900',
                old: '₹1,49,900',
                rating: '4.9',
                reviews: '2.3k',
                off: '13%',
                badge: 'feat',
                img: 'assets/images/photo-1511707171634-5f897ff02aa9.jpg'
            },
            {
                name: 'Sony WH-1000XM5',
                cat: 'Electronics',
                price: '₹24,990',
                old: '₹34,990',
                rating: '4.8',
                reviews: '1.8k',
                off: '29%',
                badge: 'trend',
                img: 'assets/images/photo-1546435770-a3e426bf472b.jpg'
            },
            {
                name: 'MacBook Air M3',
                cat: 'Electronics',
                price: '₹1,14,900',
                old: '₹1,29,900',
                rating: '4.9',
                reviews: '985',
                off: '12%',
                badge: 'feat',
                img: 'assets/images/photo-1496181133206-80ce9b88a853.jpg'
            },
            {
                name: 'Samsung 65" QLED',
                cat: 'Electronics',
                price: '₹79,990',
                old: '₹1,09,990',
                rating: '4.7',
                reviews: '543',
                off: '27%',
                badge: 'trend',
                img: 'assets/images/photo-1593640408182-31c70c8268f5.jpg'
            },
            {
                name: 'Wireless Earbuds Pro',
                cat: 'Electronics',
                price: '₹5,499',
                old: '₹8,999',
                rating: '4.6',
                reviews: '3.1k',
                off: '39%',
                badge: 'off',
                img: 'assets/images/wireless-earbuds-001-6792869accae0.jpg'
            },
            {
                name: 'iPad Pro 12.9"',
                cat: 'Electronics',
                price: '₹1,09,900',
                old: '₹1,19,900',
                rating: '4.8',
                reviews: '712',
                off: '8%',
                badge: 'feat',
                img: 'assets/images/photo-1588872657578-7efd1f1555ed.jpg'
            },
        ],
        fashion: [{
                name: 'Premium Silk Saree',
                cat: 'Fashion',
                price: '₹3,499',
                old: '₹5,999',
                rating: '4.8',
                reviews: '892',
                off: '42%',
                badge: 'trend',
                img: 'assets/images/sari/a.jpg'
            },
            {
                name: 'Floral Kurta Set',
                cat: 'Fashion',
                price: '₹1,299',
                old: '₹2,199',
                rating: '4.7',
                reviews: '1.2k',
                off: '41%',
                badge: 'feat',
                img: 'assets/images/Girls/1.jpg'
            },
            {
                name: 'Casual Tee Collection',
                cat: 'Fashion',
                price: '₹599',
                old: '₹999',
                rating: '4.5',
                reviews: '2.4k',
                off: '40%',
                badge: 'off',
                img: 'assets/images/top/1.jpg'
            },
            {
                name: 'Denim Jeans Slim Fit',
                cat: 'Fashion',
                price: '₹1,799',
                old: '₹2,999',
                rating: '4.6',
                reviews: '1.5k',
                off: '40%',
                badge: 'trend',
                img: 'assets/images/bottom/1.jpg'
            },
            {
                name: 'Tote Bag Leather',
                cat: 'Fashion',
                price: '₹2,499',
                old: '₹4,499',
                rating: '4.7',
                reviews: '678',
                off: '44%',
                badge: 'feat',
                img: 'assets/images/bag/1.jpg'
            },
            {
                name: 'Sports Running Shoes',
                cat: 'Fashion',
                price: '₹3,299',
                old: '₹5,499',
                rating: '4.8',
                reviews: '943',
                off: '40%',
                badge: 'trend',
                img: 'assets/images/shoes/1.jpg'
            },
        ],
        phones: [{
                name: 'Samsung Galaxy S24',
                cat: 'Phones',
                price: '₹79,999',
                old: '₹89,999',
                rating: '4.8',
                reviews: '1.6k',
                off: '11%',
                badge: 'feat',
                img: 'assets/images/photo-1556656793-08538906a9f8.jpg'
            },
            {
                name: 'OnePlus 12',
                cat: 'Phones',
                price: '₹64,999',
                old: '₹74,999',
                rating: '4.7',
                reviews: '1.1k',
                off: '13%',
                badge: 'trend',
                img: 'assets/images/photo-1591488320449-011701bb6704.jpg'
            },
            {
                name: 'Pixel 8 Pro',
                cat: 'Phones',
                price: '₹1,06,999',
                old: '₹1,19,999',
                rating: '4.8',
                reviews: '834',
                off: '11%',
                badge: 'feat',
                img: 'assets/images/phone/1.jpg'
            },
            {
                name: 'Vivo V30 Pro',
                cat: 'Phones',
                price: '₹39,999',
                old: '₹44,999',
                rating: '4.5',
                reviews: '665',
                off: '11%',
                badge: 'off',
                img: 'assets/images/photo-1608043152269-423dbba4e7e1.jpg'
            },
        ],
    };

    function getDemoProductHTML(p, badgeType) {
        const badgeClass = {
            feat: 'demo-badge-feat',
            trend: 'demo-badge-trend',
            off: 'demo-badge-off'
        } [p.badge || badgeType] || 'demo-badge-off';
        const badgeLabel = {
            feat: '⭐ Featured',
            trend: '🔥 Trending',
            off: p.off + ' OFF'
        } [p.badge || badgeType] || (p.off + ' OFF');
        return `
    <div class="demo-product-card">
        <span class="${badgeClass}">${badgeLabel}</span>
        <button class="demo-fav-btn" onclick="event.stopPropagation()"><i class="fa-regular fa-heart"></i></button>
        <div class="demo-product-img-wrap">
            <img src="${p.img}" alt="${p.name}"
                 onerror="this.onerror=null;this.src='assets/images/shopping.jpg'">
        </div>
        <div class="demo-product-body">
            <span class="demo-product-cat">${p.cat}</span>
            <p class="demo-product-name">${p.name}</p>
            <div class="demo-rating-row">
                <span class="demo-stars">★★★★★</span>
                <span class="demo-rating-val">${p.rating} (${p.reviews})</span>
            </div>
            <div class="demo-price-row">
                <span class="demo-price-current">${p.price}</span>
                <span class="demo-price-old">${p.old}</span>
            </div>
            <div class="demo-product-actions">
                <button class="demo-btn-cart"><i class="fas fa-cart-plus"></i> Add</button>
                <button class="demo-btn-buy">Buy Now</button>
            </div>
        </div>
    </div>`;
    }

    // Inject fallback demo products into sliders if DB products are missing
    function injectDemoProductsIfEmpty(containerId, demoKey, labelBadge) {
        const container = document.getElementById(containerId);
        if (!container) return;
        setTimeout(() => {
            const kids = container.querySelectorAll('.product-card, .demo-product-card');
            const hasEmpty = container.innerHTML.includes('No products') || kids.length === 0;
            if (hasEmpty || kids.length === 0) {
                const pool = demoProducts[demoKey] || demoProducts.electronics;
                container.innerHTML = pool.map(p => getDemoProductHTML(p, labelBadge)).join('');
            }
        }, 700);
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Inject demos after the main product loader runs
        setTimeout(() => {
            injectDemoProductsIfEmpty('featuredProducts', 'electronics', 'feat');
            injectDemoProductsIfEmpty('trendingProducts', 'fashion', 'trend');
        }, 900);
    });
    </script>

    <!-- ===================== HERO BANNER ===================== -->
    <div class="home-hero">
        <div class="hero-neon-ring"></div>
        <div class="hero-banner-inner">
            <div class="container position-relative">
                <div class="hero-discount-badge">
                    <span class="up-to">UP TO</span>
                    <span class="pct">50%</span>
                    <span class="off">OFF</span>
                </div>
                <div class="row g-0 align-items-end">
                    <!-- LEFT -->
                    <div class="col-lg-6 pb-5">
                        <div class="hero-badge-pill">
                            <i class="fas fa-bolt"></i> PREMIUM STORE
                        </div>
                        <h1 class="hero-title-main">
                            <?= htmlspecialchars($banner['title'] ?? 'SHOP THE BEST ELECTRONICS &') ?>
                        </h1>
                        <span class="hero-title-accent">FASHION</span>
                        <p class="hero-copy-text">
                            <?= htmlspecialchars($banner['subtitle'] ?? 'Discover the latest gadgets, trending fashion, and premium accessories at unbeatable prices.') ?>
                        </p>
                        <div class="hero-btn-row">
                            <a href="<?= htmlspecialchars($banner['link'] ?? '#') ?>" class="btn-hero-primary">
                                <?= htmlspecialchars($banner['button_text'] ?? 'Shop Now') ?>
                                <i class="fas fa-arrow-right"></i>
                            </a>
                            <button class="btn-hero-outline" onclick="showSection('products')">
                                Explore Products <i class="fas fa-shopping-bag"></i>
                            </button>
                        </div>
                    </div>
                    <!-- RIGHT: hero image proper fit -->
                    <div class="col-lg-6 hero-products-col">
                        <img src="<?= htmlspecialchars($banner['image'] ?? 'assets/images/photo-1511707171634-5f897ff02aa9.jpg') ?>"
                            class="hero-product-img" alt="<?= htmlspecialchars($banner['title'] ?? 'Products') ?>"
                            onerror="this.onerror=null;this.src='assets/images/photo-1502920917128-1aa500764cbd.jpg'">
                    </div>
                </div>
            </div>
        </div>
        <!-- Stats bar -->
        <div class="hero-stats-bar">
            <div class="container">
                <div class="stats-bar-inner">
                    <div class="stat-bar-item">
                        <div class="stat-icon"><i class="fas fa-users"></i></div>
                        <div>
                            <div class="stat-val">10K+</div>
                            <div class="stat-lbl">Happy Customers</div>
                        </div>
                    </div>
                    <div class="stat-bar-item">
                        <div class="stat-icon"><i class="fas fa-star"></i></div>
                        <div>
                            <div class="stat-val">4.8/5</div>
                            <div class="stat-lbl">Customer Rating</div>
                        </div>
                    </div>
                    <div class="stat-bar-item">
                        <div class="stat-icon"><i class="fas fa-award"></i></div>
                        <div>
                            <div class="stat-val">100%</div>
                            <div class="stat-lbl">Quality Products</div>
                        </div>
                    </div>
                    <div class="stat-bar-item">
                        <div class="stat-icon"><i class="fas fa-headset"></i></div>
                        <div>
                            <div class="stat-val">24/7</div>
                            <div class="stat-lbl">Customer Support</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ==================== END BANNER ==================== -->

    <!-- ==================== CATEGORY SECTION (HOME PREVIEW) ==================== -->
    <div class="container section-wrap">
        <div class="section-head text-center">
            <span class="section-pill">Explore Collections</span>
            <h2 class="section-title center gradient-text mb-2">Shop By Category</h2>
            <p class="section-subtitle">
                Browse our carefully curated collections — each category stocked with top-rated products.
            </p>
        </div>

        <div class="categories-wrapper">
            <div class="categories-grid home-cat-preview">
                <?php
            $cat_query  = "SELECT id, name, image, details FROM categories ORDER BY id DESC LIMIT 4";
            $cat_result = mysqli_query($conn, $cat_query);

            $fallback_cats = [
                ['name' => 'Electronics', 'details' => 'Phones, laptops, audio & more'],
                ['name' => 'Fashion',     'details' => 'Tops, sarees, jeans & accessories'],
                ['name' => 'Footwear',    'details' => 'Sneakers, heels, casual & formal'],
                ['name' => 'Bags',        'details' => 'Handbags, backpacks, totes & more'],
            ];

            $fallback_imgs = [
                'assets/images/photo-1511707171634-5f897ff02aa9.jpg',
                'assets/images/Girls/2.jpg',
                'assets/images/shoes/2.jpg',
                'assets/images/bag/3.jpg',
            ];

            if ($cat_result && mysqli_num_rows($cat_result) > 0) {
                $idx = 0;
                while ($cat = mysqli_fetch_assoc($cat_result)) {
                    $catId   = (int)($cat['id'] ?? 0);
                    $catName = trim((string)($cat['name'] ?? 'Category'));
                    $catDesc = trim((string)($cat['details'] ?? 'Explore products in this category'));

                    $fallbackImg = $fallback_imgs[$idx % count($fallback_imgs)];
                    $idx++;

                    $dbImage = trim((string)($cat['image'] ?? ''));
                    $catImg  = $dbImage !== '' ? 'upload/' . ltrim($dbImage, '/') : $fallbackImg;
                    ?>
                <div class="category-card" role="button" tabindex="0" data-category-id="<?= $catId ?>"
                    data-category-name="<?= htmlspecialchars($catName, ENT_QUOTES) ?>"
                    onclick="showCategoryProducts(<?= $catId ?>, <?= json_encode($catName, JSON_UNESCAPED_UNICODE) ?>)">
                    <div class="category-image-wrap">
                        <img src="<?= htmlspecialchars($catImg) ?>" class="category-image"
                            alt="<?= htmlspecialchars($catName) ?>"
                            onerror="this.onerror=null;this.src='<?= htmlspecialchars($fallbackImg) ?>'">
                        <div class="category-overlay"></div>
                        <div class="category-chip">Category</div>
                        <div class="category-badge-count">Shop Now</div>
                    </div>

                    <div class="category-body">
                        <h5 class="category-title"><?= htmlspecialchars($catName) ?></h5>
                        <p class="category-text"><?= htmlspecialchars($catDesc) ?></p>
                        <button type="button" class="category-btn"
                            onclick="event.stopPropagation();showCategoryProducts(<?= $catId ?>, <?= json_encode($catName, JSON_UNESCAPED_UNICODE) ?>)">
                            View Products <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                <?php
                }
            } else {
                foreach ($fallback_cats as $i => $cat) {
                    ?>
                <div class="category-card" role="button" tabindex="0" data-category-id="<?= $i + 1 ?>"
                    data-category-name="<?= htmlspecialchars($cat['name'], ENT_QUOTES) ?>"
                    onclick="showCategoryProducts(<?= $i + 1 ?>, <?= json_encode($cat['name'], JSON_UNESCAPED_UNICODE) ?>)">
                    <div class="category-image-wrap">
                        <img src="<?= htmlspecialchars($fallback_imgs[$i]) ?>" class="category-image"
                            alt="<?= htmlspecialchars($cat['name']) ?>">
                        <div class="category-overlay"></div>
                        <div class="category-chip">Category</div>
                        <div class="category-badge-count">Shop Now</div>
                    </div>

                    <div class="category-body">
                        <h5 class="category-title"><?= htmlspecialchars($cat['name']) ?></h5>
                        <p class="category-text"><?= htmlspecialchars($cat['details']) ?></p>
                        <button type="button" class="category-btn"
                            onclick="event.stopPropagation();showCategoryProducts(<?= $i + 1 ?>, <?= json_encode($cat['name'], JSON_UNESCAPED_UNICODE) ?>)">
                            View Products <i class="fas fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>
                <?php
                }
            }
            ?>
            </div>
        </div>

        <!-- View All Categories Button -->
        <div class="text-center mt-5">
            <button type="button" class="view-all-categories-btn" onclick="showSection('categories')">
                <i class="fas fa-th-large me-2"></i>
                View All Categories
                <i class="fas fa-arrow-right ms-2"></i>
            </button>
        </div>
    </div>

    <style>
    /* ---- VIEW ALL CATEGORIES BUTTON ---- */
    .view-all-categories-btn {
        display: inline-flex;
        align-items: center;
        gap: 0;
        padding: 14px 36px;
        border-radius: 14px;
        border: 2px solid transparent;
        background: linear-gradient(var(--bg-color, #0f1115), var(--bg-color, #0f1115)) padding-box,
                    linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7) border-box;
        color: #7c3aed;
        font-weight: 800;
        font-size: 1rem;
        letter-spacing: .02em;
        cursor: pointer;
        transition: all .25s ease;
        position: relative;
        overflow: hidden;
    }

    .view-all-categories-btn::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, #4f46e5, #7c3aed);
        opacity: 0;
        transition: opacity .25s ease;
        z-index: 0;
    }

    .view-all-categories-btn i,
    .view-all-categories-btn span {
        position: relative;
        z-index: 1;
    }

    .view-all-categories-btn:hover::before {
        opacity: 1;
    }

    .view-all-categories-btn:hover {
        color: #fff;
        transform: translateY(-3px);
        box-shadow: 0 14px 32px rgba(124, 58, 237, .35);
    }

    body.light-mode .view-all-categories-btn {
        background: linear-gradient(#ffffff, #ffffff) padding-box,
                    linear-gradient(135deg, #4f46e5, #7c3aed, #a855f7) border-box;
    }

    /* Limit preview to 4 cols max, 2 on mobile */
    .home-cat-preview {
        grid-template-columns: repeat(4, minmax(0, 1fr)) !important;
    }

    @media (max-width: 991px) {
        .home-cat-preview {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
        }
    }

    @media (max-width: 480px) {
        .home-cat-preview {
            grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
            gap: 10px !important;
        }
    }
    </style>
    <!-- ==================== END CATEGORY SECTION ==================== -->

    <!-- ==================== FEATURED PRODUCTS ==================== -->
    <div class="container section-wrap" style="padding-top: 0;">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <span class="section-pill">Hand Picked</span>
                <h2 class="section-title gradient-text mb-1">Featured Products</h2>
                <p class="section-subtitle mb-0">Top-rated picks across electronics, fashion and more.</p>
            </div>
            <a href="#" class="text-decoration-none fw-600" onclick="showSection('products')">
                View All <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="horizontal-slider">
            <button type="button" class="slider-nav prev" onclick="slideSection('featuredProducts',-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-track" id="featuredProducts">
                <!-- Populated by JS (DB products). Demo injected as fallback. -->
                <?php
            // Pre-render demo cards server-side too (they'll be replaced by JS if DB has products)
            $demoFeatured = [
                ['name'=>'iPhone 15 Pro Max','cat'=>'Electronics','price'=>'₹1,29,900','old'=>'₹1,49,900','badge'=>'⭐ Featured','img'=>'assets/images/photo-1511707171634-5f897ff02aa9.jpg','rating'=>'4.9','reviews'=>'2.3k'],
                ['name'=>'Sony WH-1000XM5','cat'=>'Electronics','price'=>'₹24,990','old'=>'₹34,990','badge'=>'29% OFF','img'=>'assets/images/photo-1546435770-a3e426bf472b.jpg','rating'=>'4.8','reviews'=>'1.8k'],
                ['name'=>'MacBook Air M3','cat'=>'Electronics','price'=>'₹1,14,900','old'=>'₹1,29,900','badge'=>'⭐ Featured','img'=>'assets/images/photo-1496181133206-80ce9b88a853.jpg','rating'=>'4.9','reviews'=>'985'],
                ['name'=>'iPad Pro 12.9"','cat'=>'Electronics','price'=>'₹1,09,900','old'=>'₹1,19,900','badge'=>'⭐ Featured','img'=>'assets/images/photo-1588872657578-7efd1f1555ed.jpg','rating'=>'4.8','reviews'=>'712'],
                ['name'=>'Wireless Earbuds Pro','cat'=>'Electronics','price'=>'₹5,499','old'=>'₹8,999','badge'=>'39% OFF','img'=>'assets/images/wireless-earbuds-001-6792869accae0.jpg','rating'=>'4.6','reviews'=>'3.1k'],
                ['name'=>'Samsung Galaxy S24','cat'=>'Phones','price'=>'₹79,999','old'=>'₹89,999','badge'=>'⭐ Featured','img'=>'assets/images/photo-1556656793-08538906a9f8.jpg','rating'=>'4.8','reviews'=>'1.6k'],
            ];
            foreach($demoFeatured as $p): ?>
                <div class="demo-product-card" id="server-feat">
                    <span class="demo-badge-feat"><?= $p['badge'] ?></span>
                    <button class="demo-fav-btn" onclick="event.stopPropagation()"><i class="fa-regular fa-heart"></i></button>
                    <div class="demo-product-img-wrap">
                        <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>"
                            onerror="this.onerror=null;this.src='assets/images/shopping.jpg'">
                    </div>
                    <div class="demo-product-body">
                        <span class="demo-product-cat"><?= $p['cat'] ?></span>
                        <p class="demo-product-name"><?= $p['name'] ?></p>
                        <div class="demo-rating-row">
                            <span class="demo-stars">★★★★★</span>
                            <span class="demo-rating-val"><?= $p['rating'] ?> (<?= $p['reviews'] ?>)</span>
                        </div>
                        <div class="demo-price-row">
                            <span class="demo-price-current"><?= $p['price'] ?></span>
                            <span class="demo-price-old"><?= $p['old'] ?></span>
                        </div>
                        <div class="demo-product-actions">
                            <button class="demo-btn-cart" onclick="event.stopPropagation()"><i
                                    class="fas fa-cart-plus"></i> Add</button>
                            <button class="demo-btn-buy" onclick="event.stopPropagation()">Buy Now</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="slider-nav next" onclick="slideSection('featuredProducts',1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <!-- ==================== END FEATURED ==================== -->


    <!-- ==================== TRENDING NOW ==================== -->
    <div class="container section-wrap" style="padding-top: 0;">
        <div class="d-flex justify-content-between align-items-end mb-4 flex-wrap gap-3">
            <div>
                <span class="section-pill"
                    style="color:#d97706;background:rgba(245,158,11,.10);border-color:rgba(245,158,11,.2);">🔥 Hot Right
                    Now</span>
                <h2 class="section-title gradient-text mb-1">Trending Now</h2>
                <p class="section-subtitle mb-0">What everyone's buying this week — don't miss out.</p>
            </div>
            <a href="#" class="text-decoration-none fw-600" onclick="showSection('products')">
                View All <i class="fas fa-arrow-right ms-1"></i>
            </a>
        </div>
        <div class="horizontal-slider">
            <button type="button" class="slider-nav prev" onclick="slideSection('trendingProducts',-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-track" id="trendingProducts">
                <?php
            $demoTrending = [
                ['name'=>'Premium Silk Saree','cat'=>'Fashion','price'=>'₹3,499','old'=>'₹5,999','badge'=>'🔥 Trending','img'=>'assets/images/sari/a.jpg','rating'=>'4.8','reviews'=>'892'],
                ['name'=>'Floral Kurta Set','cat'=>'Fashion','price'=>'₹1,299','old'=>'₹2,199','badge'=>'41% OFF','img'=>'assets/images/Girls/1.jpg','rating'=>'4.7','reviews'=>'1.2k'],
                ['name'=>'Casual Tee Collection','cat'=>'Fashion','price'=>'₹599','old'=>'₹999','badge'=>'🔥 Trending','img'=>'assets/images/top/1.jpg','rating'=>'4.5','reviews'=>'2.4k'],
                ['name'=>'Denim Slim Fit','cat'=>'Fashion','price'=>'₹1,799','old'=>'₹2,999','badge'=>'40% OFF','img'=>'assets/images/bottom/1.jpg','rating'=>'4.6','reviews'=>'1.5k'],
                ['name'=>'Leather Tote Bag','cat'=>'Fashion','price'=>'₹2,499','old'=>'₹4,499','badge'=>'🔥 Trending','img'=>'assets/images/bag/1.jpg','rating'=>'4.7','reviews'=>'678'],
                ['name'=>'Sports Running Shoes','cat'=>'Footwear','price'=>'₹3,299','old'=>'₹5,499','badge'=>'40% OFF','img'=>'assets/images/shoes/1.jpg','rating'=>'4.8','reviews'=>'943'],
            ];
            foreach($demoTrending as $p): ?>
                <div class="demo-product-card">
                    <span class="demo-badge-trend"><?= $p['badge'] ?></span>
                    <button class="demo-fav-btn" onclick="event.stopPropagation()"><i class="fa-regular fa-heart"></i></button>
                    <div class="demo-product-img-wrap">
                        <img src="<?= $p['img'] ?>" alt="<?= $p['name'] ?>"
                            onerror="this.onerror=null;this.src='assets/images/shopping.jpg'">
                    </div>
                    <div class="demo-product-body">
                        <span class="demo-product-cat"><?= $p['cat'] ?></span>
                        <p class="demo-product-name"><?= $p['name'] ?></p>
                        <div class="demo-rating-row">
                            <span class="demo-stars">★★★★★</span>
                            <span class="demo-rating-val"><?= $p['rating'] ?> (<?= $p['reviews'] ?>)</span>
                        </div>
                        <div class="demo-price-row">
                            <span class="demo-price-current"><?= $p['price'] ?></span>
                            <span class="demo-price-old"><?= $p['old'] ?></span>
                        </div>
                        <div class="demo-product-actions">
                            <button class="demo-btn-cart" onclick="event.stopPropagation()"><i
                                    class="fas fa-cart-plus"></i> Add</button>
                            <button class="demo-btn-buy" onclick="event.stopPropagation()">Buy Now</button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <button type="button" class="slider-nav next" onclick="slideSection('trendingProducts',1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <!-- ==================== END TRENDING ==================== -->


    <!-- ==================== TRUSTED BRANDS ==================== -->
    <div class="container section-wrap" style="padding-top: 0;">
        <div class="section-head text-center">
            <h2 class="section-title center gradient-text">Trusted Brands</h2>
            <p class="section-subtitle">We carry only premium, verified brands you can trust.</p>
        </div>
        <div class="horizontal-slider">
            <button type="button" class="slider-nav prev" onclick="slideSection('brandSlider',-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <div class="slider-track brand-slider" id="brandSlider">
                <?php if ($brandRows && mysqli_num_rows($brandRows) > 0): ?>
                <?php while ($brand = mysqli_fetch_assoc($brandRows)): ?>
                <div class="brand-box">
                    <img src="<?= htmlspecialchars($brand['image']) ?>" class="brand-logo"
                        alt="<?= htmlspecialchars($brand['title'] ?? 'Brand') ?>" onerror="this.style.display='none'">
                </div>
                <?php endwhile; ?>
                <?php else: ?>
                <?php
                $staticBrands = [
                    ['img'=>'assets/logo/Samsung_old_logo_before_year_2015.svg', 'name'=>'Samsung'],
                    ['img'=>'assets/logo/Dell_Logo.png', 'name'=>'Dell'],
                    ['img'=>'assets/logo/hp-brand-logo-laptop-symbol-white-design-vector-46356684.png', 'name'=>'HP'],
                    ['img'=>'assets/logo/logitech-logo-2.png', 'name'=>'Logitech'],
                    ['img'=>'assets/logo/oppo-logo.png', 'name'=>'Oppo'],
                    ['img'=>'assets/logo/vivo.png', 'name'=>'Vivo'],
                ];
                foreach($staticBrands as $b): ?>
                <div class="brand-box">
                    <img src="<?= $b['img'] ?>" class="brand-logo" alt="<?= $b['name'] ?>"
                        onerror="this.onerror=null;this.parentElement.innerHTML='<span style=\'font-weight:700;font-size:.9rem;color:#64748b\'><?= $b['name'] ?></span>'">
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <button type="button" class="slider-nav next" onclick="slideSection('brandSlider',1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    </div>
    <!-- ==================== END BRANDS ==================== -->


    <!-- ==================== WHY SHOP WITH US ==================== -->
    <div class="container section-wrap" style="padding-top: 0; padding-bottom: 80px;">
        <div class="row g-4">
            <div class="col-md-4 card-container">
                <div class="trust-card features-card">
                    <div class="trust-icon icon-wrapper">
                        <i class=" fas fa-shipping-fast"></i>
                    </div>
                    <h5>Free Shipping</h5>
                    <p>Get fast, reliable delivery on every order — no minimum required on premium purchases.</p>
                </div>
            </div>
            <div class="col-md-4 card-container">
                <div class="trust-card features-card">
                    <div class="trust-icon icon-wrapper"><i class="fas fa-undo-alt"></i></div>
                    <h5>Easy Returns</h5>
                    <p>Changed your mind? Our hassle-free 30-day return policy keeps you shopping with confidence.</p>
                </div>
            </div>
            <div class="col-md-4 card-container">
                <div class="trust-card features-card">
                    <div class="trust-icon icon-wrapper"><i class="fas fa-shield-alt"></i></div>
                    <h5>Secure Checkout</h5>
                    <p>Bank-grade SSL encryption on every transaction — your data and payments are always safe.</p>
                </div>
            </div>
        </div>
    </div>
    <!-- ==================== END TRUST ==================== -->

</section>