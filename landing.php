<?php
// ── Public page – no auth required ──
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// DB connection
$_lp_conn = null;
foreach ([3306,3307] as $_p) {
    try {
        $_lp_conn = @mysqli_connect('127.0.0.1','root','','college_db',$_p);
        if ($_lp_conn instanceof mysqli) break;
    } catch (Throwable $e) { $_lp_conn = null; }
}
if ($_lp_conn) {
    mysqli_query($_lp_conn, "CREATE TABLE IF NOT EXISTS landing_page_settings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        setting_key VARCHAR(120) NOT NULL UNIQUE,
        setting_value TEXT,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

function lp_get($conn, $key, $default = '') {
    if (!$conn) return $default;
    $key = mysqli_real_escape_string($conn, $key);
    $r = mysqli_query($conn, "SELECT setting_value FROM landing_page_settings WHERE setting_key='$key' LIMIT 1");
    if ($r && $row = mysqli_fetch_assoc($r)) {
        return $row['setting_value'] !== null ? $row['setting_value'] : $default;
    }
    return $default;
}

// ── Load settings with global e-commerce defaults ──
$hero_headline   = lp_get($_lp_conn,'hero_headline',   'Shop the World.<br><span class="grad-text">Delivered to Your Door.</span>');
$hero_subtext    = lp_get($_lp_conn,'hero_subtext',    'Quigly brings you millions of products from top brands worldwide — with lightning-fast delivery, unbeatable prices, and a shopping experience you\'ll love.');
$hero_cta_label  = lp_get($_lp_conn,'hero_cta_label',  'Start Shopping');
$hero_cta_url    = lp_get($_lp_conn,'hero_cta_url',    '/Quigly/register.php');
$hero_cta2_label = lp_get($_lp_conn,'hero_cta2_label', 'Sign In');
$hero_cta2_url   = lp_get($_lp_conn,'hero_cta2_url',   '/Quigly/login.php');

$feat1_icon  = lp_get($_lp_conn,'feat1_icon',  'fas fa-shield-halved');
$feat1_title = lp_get($_lp_conn,'feat1_title', 'Secure Shopping');
$feat1_desc  = lp_get($_lp_conn,'feat1_desc',  'Every transaction is encrypted and protected. Shop with complete confidence and zero worries.');
$feat2_icon  = lp_get($_lp_conn,'feat2_icon',  'fas fa-truck-fast');
$feat2_title = lp_get($_lp_conn,'feat2_title', 'Express Delivery');
$feat2_desc  = lp_get($_lp_conn,'feat2_desc',  'Get your orders delivered fast — same-day, next-day or scheduled. We bring it right to your doorstep.');
$feat3_icon  = lp_get($_lp_conn,'feat3_icon',  'fas fa-tags');
$feat3_title = lp_get($_lp_conn,'feat3_title', 'Best Prices');
$feat3_desc  = lp_get($_lp_conn,'feat3_desc',  'We compare thousands of sellers to guarantee you always get the best deal on every product.');
$feat4_icon  = lp_get($_lp_conn,'feat4_icon',  'fas fa-headset');
$feat4_title = lp_get($_lp_conn,'feat4_title', '24/7 Support');
$feat4_desc  = lp_get($_lp_conn,'feat4_desc',  'Our dedicated support team is always available to help you with orders, returns, or any queries.');

$stat1_num   = lp_get($_lp_conn,'stat1_num',   '5M+');
$stat1_label = lp_get($_lp_conn,'stat1_label', 'Happy Customers');
$stat2_num   = lp_get($_lp_conn,'stat2_num',   '2M+');
$stat2_label = lp_get($_lp_conn,'stat2_label', 'Products Listed');
$stat3_num   = lp_get($_lp_conn,'stat3_num',   '99%');
$stat3_label = lp_get($_lp_conn,'stat3_label', 'Satisfaction Rate');
$stat4_num   = lp_get($_lp_conn,'stat4_num',   '180+');
$stat4_label = lp_get($_lp_conn,'stat4_label', 'Countries Served');

$t1_name  = lp_get($_lp_conn,'t1_name',  'Sarah Johnson');
$t1_role  = lp_get($_lp_conn,'t1_role',  'Verified Buyer, New York');
$t1_text  = lp_get($_lp_conn,'t1_text',  'Quigly is hands-down the best shopping experience I\'ve ever had. The delivery was super fast and the product quality exceeded my expectations!');
$t1_init  = lp_get($_lp_conn,'t1_init',  'S');
$t2_name  = lp_get($_lp_conn,'t2_name',  'Ravi Kumar');
$t2_role  = lp_get($_lp_conn,'t2_role',  'Verified Buyer, Mumbai');
$t2_text  = lp_get($_lp_conn,'t2_text',  'Amazing deals and genuinely great quality. I\'ve been shopping on Quigly for 6 months and haven\'t been disappointed once.');
$t2_init  = lp_get($_lp_conn,'t2_init',  'R');
$t3_name  = lp_get($_lp_conn,'t3_name',  'Emily Chen');
$t3_role  = lp_get($_lp_conn,'t3_role',  'Verified Buyer, London');
$t3_text  = lp_get($_lp_conn,'t3_text',  'The returns process is so smooth and the customer support is incredibly responsive. Quigly feels like the future of online shopping.');
$t3_init  = lp_get($_lp_conn,'t3_init',  'E');

$cta_title   = lp_get($_lp_conn,'cta_title',   'Start Shopping Smarter Today');
$cta_sub     = lp_get($_lp_conn,'cta_sub',     'Join over 5 million satisfied customers and experience the world\'s most rewarding shopping platform.');
$cta_btn     = lp_get($_lp_conn,'cta_btn',     'Create Free Account');
$cta_btn_url = lp_get($_lp_conn,'cta_btn_url', '/Quigly/register.php');
$footer_tag  = lp_get($_lp_conn,'footer_tag',  'Your Global Shopping Destination — Quality, Speed & Value.');

$already_logged_in = isset($_SESSION['user_id']) || isset($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quigly — Your Global Shopping Destination</title>
    <meta name="description"
        content="Quigly is the premium global e-commerce platform offering millions of products, express delivery, unbeatable prices and 24/7 support worldwide.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <script>
    // Apply theme before render to avoid flash
    (function() {
        var t = localStorage.getItem('quiglyLandingTheme') || 'dark';
        document.documentElement.setAttribute('data-theme', t);
    })();
    </script>

    <style>
    /* ═══════════════════════════════════════════════
       QUIGLY LANDING — GLOBAL E-COMMERCE  v2.0
       Light / Dark Mode  |  Fully Responsive
    ═══════════════════════════════════════════════ */
    *,
    *::before,
    *::after {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    /* ── CSS Variables: Dark Mode ── */
    :root,
    [data-theme="dark"] {
        --bg: #05060f;
        --bg2: #080c18;
        --bg3: #0d1225;
        --surface: rgba(255, 255, 255, .04);
        --surface2: rgba(255, 255, 255, .07);
        --border: rgba(255, 255, 255, .08);
        --border2: rgba(255, 255, 255, .14);
        --text: #e2e8f0;
        --text-muted: #94a3b8;
        --text-faint: #475569;
        --nav-bg: rgba(5, 6, 15, .75);
        --nav-scrolled: rgba(5, 6, 15, .96);
        --card-bg: rgba(255, 255, 255, .03);
        --card-hover: rgba(124, 58, 237, .07);
        --stats-bg: rgba(255, 255, 255, .03);
        --footer-bg: #07080e;
        --footer-border: rgba(255, 255, 255, .06);
        --footer-text: #94a3b8;
        --footer-title: #ffffff;
        --footer-logo-text: #ffffff;
        --footer-badge-bg: rgba(255, 255, 255, .03);
        --footer-badge-border: rgba(255, 255, 255, .08);
        --shadow: 0 8px 40px rgba(0, 0, 0, .5);
        --shadow-lg: 0 24px 80px rgba(0, 0, 0, .65);
        --orb-opacity: .32;
        --mesh-color: rgba(255, 255, 255, .018);
        --mode-icon: "☀️";
    }

    /* ── CSS Variables: Light Mode ── */
    [data-theme="light"] {
        --bg: #f8faff;
        --bg2: #f0f4ff;
        --bg3: #e8edf8;
        --surface: rgba(255, 255, 255, .9);
        --surface2: rgba(255, 255, 255, .98);
        --border: rgba(0, 0, 0, .08);
        --border2: rgba(0, 0, 0, .13);
        --text: #0f172a;
        --text-muted: #475569;
        --text-faint: #94a3b8;
        --nav-bg: rgba(248, 250, 255, .85);
        --nav-scrolled: rgba(248, 250, 255, .97);
        --card-bg: #ffffff;
        --card-hover: rgba(124, 58, 237, .05);
        --stats-bg: #ffffff;
        --footer-bg: #f8fafc;
        --footer-border: rgba(15, 23, 42, 0.08);
        --footer-text: #475569;
        --footer-title: #0f172a;
        --footer-logo-text: #0f172a;
        --footer-badge-bg: rgba(15, 23, 42, .03);
        --footer-badge-border: rgba(15, 23, 42, .08);
        --shadow: 0 8px 40px rgba(15, 23, 42, .1);
        --shadow-lg: 0 24px 80px rgba(15, 23, 42, .14);
        --orb-opacity: .18;
        --mesh-color: rgba(0, 0, 0, .025);
    }

    /* ── Accent (same both modes) ── */
    :root {
        --accent: #7c3aed;
        --accent2: #4f46e5;
        --accent3: #06b6d4;
        --pink: #ec4899;
        --grad: linear-gradient(135deg, #7c3aed, #4f46e5, #06b6d4);
        --grad2: linear-gradient(135deg, #7c3aed, #ec4899);
        --grad-text: linear-gradient(135deg, #a78bfa, #818cf8, #67e8f9);
        --radius-lg: 24px;
        --radius-md: 16px;
        --radius-sm: 12px;
        --transition: .32s cubic-bezier(.4, 0, .2, 1);
        --font: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: var(--font);
        background: var(--bg);
        color: var(--text);
        overflow-x: hidden;
        line-height: 1.6;
        transition: background var(--transition), color var(--transition);
    }

    ::-webkit-scrollbar {
        width: 6px;
    }

    ::-webkit-scrollbar-track {
        background: var(--bg);
    }

    ::-webkit-scrollbar-thumb {
        background: var(--accent);
        border-radius: 99px;
    }

    /* ── Utility ── */
    .grad-text {
        background: var(--grad-text);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .grad-text-pink {
        background: var(--grad2);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 28px;
    }

    .text-center {
        text-align: center;
    }

    /* ══ AMBIENT BACKGROUND ══ */
    .ambient-bg {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        overflow: hidden;
    }

    .orb {
        position: absolute;
        border-radius: 50%;
        filter: blur(90px);
        opacity: var(--orb-opacity);
        animation: floatOrb 10s ease-in-out infinite;
        transition: opacity var(--transition);
    }

    .orb-1 {
        width: 650px;
        height: 650px;
        background: radial-gradient(circle, #7c3aed, transparent 70%);
        top: -220px;
        left: -160px;
    }

    .orb-2 {
        width: 500px;
        height: 500px;
        background: radial-gradient(circle, #4f46e5, transparent 70%);
        top: 80px;
        right: -120px;
        animation-delay: -4s;
    }

    .orb-3 {
        width: 420px;
        height: 420px;
        background: radial-gradient(circle, #06b6d4, transparent 70%);
        bottom: 150px;
        left: 32%;
        animation-delay: -7s;
    }

    .orb-4 {
        width: 360px;
        height: 360px;
        background: radial-gradient(circle, #ec4899, transparent 70%);
        bottom: -80px;
        right: 18%;
        animation-delay: -2s;
    }

    @keyframes floatOrb {

        0%,
        100% {
            transform: translateY(0) scale(1);
        }

        50% {
            transform: translateY(-40px) scale(1.07);
        }
    }

    .mesh-grid {
        position: fixed;
        inset: 0;
        pointer-events: none;
        z-index: 0;
        background-image:
            linear-gradient(var(--mesh-color) 1px, transparent 1px),
            linear-gradient(90deg, var(--mesh-color) 1px, transparent 1px);
        background-size: 64px 64px;
        transition: background-image var(--transition);
    }

    /* ══ NAVBAR ══ */
    .lp-nav {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 999;
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 40px;
        background: var(--nav-bg);
        backdrop-filter: blur(22px);
        -webkit-backdrop-filter: blur(22px);
        border-bottom: 1px solid var(--border);
        transition: background var(--transition), box-shadow var(--transition), border-color var(--transition);
    }

    .lp-nav.scrolled {
        background: var(--nav-scrolled);
        box-shadow: 0 4px 32px rgba(0, 0, 0, .15);
    }

    [data-theme="light"] .lp-nav.scrolled {
        box-shadow: 0 4px 32px rgba(15, 23, 42, .12);
    }

    .nav-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }

    .nav-logo-icon {
        width: 40px;
        height: 40px;
        border-radius: 13px;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 900;
        color: #fff;
        box-shadow: 0 4px 22px rgba(124, 58, 237, .45);
        flex-shrink: 0;
    }

    .nav-logo-wordmark {
        font-size: 22px;
        font-weight: 900;
        letter-spacing: -.7px;
        color: var(--text);
        transition: color var(--transition);
    }

    .nav-logo-wordmark span {
        color: #a78bfa;
    }

    .nav-right {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Theme toggle */
    .theme-toggle {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        border: 1px solid var(--border2);
        background: var(--surface2);
        color: var(--text-muted);
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: var(--transition);
        flex-shrink: 0;
    }

    .theme-toggle:hover {
        border-color: var(--accent);
        color: var(--accent);
        background: var(--card-hover);
    }

    .btn-ghost {
        padding: 9px 20px;
        border-radius: 11px;
        border: 1px solid var(--border2);
        background: var(--surface);
        color: var(--text);
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        transition: var(--transition);
    }

    .btn-ghost:hover {
        border-color: rgba(124, 58, 237, .45);
        background: var(--card-hover);
        color: var(--accent);
    }

    .btn-primary-nav {
        padding: 9px 22px;
        border-radius: 11px;
        background: var(--grad);
        color: #fff;
        font-size: 14px;
        font-weight: 700;
        text-decoration: none;
        border: none;
        box-shadow: 0 4px 20px rgba(124, 58, 237, .38);
        transition: var(--transition);
    }

    .btn-primary-nav:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 32px rgba(124, 58, 237, .55);
        color: #fff;
    }

    /* Logged-in bar */
    .logged-bar {
        position: fixed;
        top: 72px;
        left: 0;
        right: 0;
        background: var(--grad);
        color: #fff;
        text-align: center;
        padding: 10px;
        font-size: 13px;
        font-weight: 600;
        z-index: 998;
    }

    .logged-bar a {
        color: #fde68a;
        text-decoration: underline;
    }

    /* ══ HERO ══ */
    .hero {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 1;
        padding: 130px 28px 100px;
        text-align: center;
    }

    .hero-inner {
        max-width: 800px;
        margin: 0 auto;
    }

    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 18px;
        border-radius: 999px;
        border: 1px solid rgba(124, 58, 237, .3);
        background: rgba(124, 58, 237, .1);
        color: #c4b5fd;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: .4px;
        margin-bottom: 30px;
        animation: fadeInDown .6s ease both;
        backdrop-filter: blur(8px);
    }

    [data-theme="light"] .hero-badge {
        color: #6d28d9;
        background: rgba(124, 58, 237, .08);
        border-color: rgba(124, 58, 237, .25);
    }

    .hero-badge .dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #7c3aed;
        box-shadow: 0 0 10px #7c3aed;
        animation: pulseDot 1.6s ease-in-out infinite;
    }

    @keyframes pulseDot {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .25;
        }
    }

    .hero-h1 {
        font-size: clamp(2.8rem, 6.5vw, 5.2rem);
        font-weight: 900;
        line-height: 1.08;
        letter-spacing: -.05em;
        margin-bottom: 26px;
        color: var(--text);
        transition: color var(--transition);
        animation: fadeInUp .7s .1s ease both;
    }

    .hero-sub {
        font-size: clamp(1rem, 2vw, 1.22rem);
        color: var(--text-muted);
        max-width: 640px;
        margin: 0 auto 44px;
        line-height: 1.75;
        animation: fadeInUp .7s .2s ease both;
        transition: color var(--transition);
    }

    .hero-actions {
        display: flex;
        gap: 14px;
        justify-content: center;
        flex-wrap: wrap;
        margin-bottom: 56px;
        animation: fadeInUp .7s .3s ease both;
    }

    .hero-cta-primary {
        padding: 15px 38px;
        font-size: 16px;
        font-weight: 700;
        border-radius: var(--radius-sm);
        background: var(--grad);
        color: #fff;
        text-decoration: none;
        box-shadow: 0 6px 32px rgba(124, 58, 237, .45);
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .hero-cta-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 14px 44px rgba(124, 58, 237, .6);
        color: #fff;
    }

    .hero-cta-secondary {
        padding: 15px 38px;
        font-size: 16px;
        font-weight: 600;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border2);
        background: var(--surface2);
        backdrop-filter: blur(10px);
        color: var(--text);
        text-decoration: none;
        transition: var(--transition);
        display: inline-flex;
        align-items: center;
        gap: 9px;
    }

    .hero-cta-secondary:hover {
        border-color: rgba(124, 58, 237, .45);
        background: var(--card-hover);
        color: #a78bfa;
        transform: translateY(-2px);
    }

    [data-theme="light"] .hero-cta-secondary {
        color: #0f172a;
    }

    /* Trust badges */
    .trust-badges {
        display: flex;
        gap: 12px;
        justify-content: center;
        flex-wrap: wrap;
        animation: fadeInUp .7s .4s ease both;
    }

    .trust-badge {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 18px;
        border-radius: var(--radius-sm);
        border: 1px solid var(--border);
        background: var(--surface);
        backdrop-filter: blur(12px);
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        transition: var(--transition);
    }

    .trust-badge:hover {
        border-color: var(--border2);
        color: var(--text);
    }

    .trust-badge i {
        color: #a78bfa;
        font-size: 15px;
    }

    [data-theme="light"] .trust-badge {
        background: #fff;
        box-shadow: 0 2px 12px rgba(15, 23, 42, .07);
    }

    /* ══ SECTION SHARED ══ */
    section {
        position: relative;
        z-index: 1;
    }

    .sec-tag {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        padding: 5px 15px;
        border-radius: 999px;
        background: rgba(124, 58, 237, .1);
        border: 1px solid rgba(124, 58, 237, .22);
        color: #a78bfa;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .9px;
        text-transform: uppercase;
        margin-bottom: 18px;
    }

    [data-theme="light"] .sec-tag {
        color: #6d28d9;
        background: rgba(124, 58, 237, .08);
        border-color: rgba(124, 58, 237, .18);
    }

    .sec-h2 {
        font-size: clamp(1.9rem, 4vw, 2.9rem);
        font-weight: 900;
        letter-spacing: -.04em;
        line-height: 1.13;
        margin-bottom: 18px;
        color: var(--text);
        transition: color var(--transition);
    }

    .sec-sub {
        color: var(--text-muted);
        font-size: 1.06rem;
        max-width: 560px;
        margin: 0 auto 58px;
        transition: color var(--transition);
    }

    /* ══ FEATURES ══ */
    .features-section {
        padding: 110px 0;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        gap: 20px;
    }

    .feat-card {
        padding: 34px 28px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .feat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 2px;
        background: var(--grad);
        opacity: 0;
        transition: var(--transition);
    }

    .feat-card:hover {
        transform: translateY(-7px);
        border-color: rgba(124, 58, 237, .28);
        background: var(--card-hover);
        box-shadow: var(--shadow);
    }

    .feat-card:hover::after {
        opacity: 1;
    }

    [data-theme="light"] .feat-card {
        background: #fff;
        box-shadow: 0 2px 20px rgba(15, 23, 42, .06);
    }

    [data-theme="light"] .feat-card:hover {
        box-shadow: 0 12px 48px rgba(124, 58, 237, .14);
        background: rgba(124, 58, 237, .03);
    }

    .feat-icon {
        width: 54px;
        height: 54px;
        border-radius: 15px;
        background: linear-gradient(135deg, rgba(124, 58, 237, .18), rgba(79, 70, 229, .18));
        border: 1px solid rgba(124, 58, 237, .22);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: #a78bfa;
        margin-bottom: 22px;
    }

    [data-theme="light"] .feat-icon {
        background: rgba(124, 58, 237, .08);
    }

    .feat-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 10px;
        transition: color var(--transition);
    }

    .feat-desc {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.7;
        transition: color var(--transition);
    }

    /* ══ STATS ══ */
    .stats-section {
        padding: 80px 0;
    }

    .stats-inner {
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--stats-bg);
        backdrop-filter: blur(14px);
        padding: 60px 48px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 44px;
        position: relative;
        overflow: hidden;
        transition: background var(--transition), border-color var(--transition);
    }

    .stats-inner::before {
        content: '';
        position: absolute;
        top: -120px;
        left: 50%;
        transform: translateX(-50%);
        width: 500px;
        height: 280px;
        background: radial-gradient(ellipse, rgba(124, 58, 237, .18) 0%, transparent 70%);
        pointer-events: none;
    }

    [data-theme="light"] .stats-inner {
        box-shadow: 0 4px 32px rgba(15, 23, 42, .08);
    }

    .stat-item {
        text-align: center;
    }

    .stat-num {
        font-size: 3.2rem;
        font-weight: 900;
        letter-spacing: -.07em;
        line-height: 1;
        margin-bottom: 10px;
        background: var(--grad);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: .7px;
        transition: color var(--transition);
    }

    /* ══ CATEGORIES HIGHLIGHT ══ */
    .cats-section {
        padding: 100px 0;
    }

    .cats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 14px;
    }

    .cat-card {
        padding: 28px 16px;
        border-radius: var(--radius-md);
        border: 1px solid var(--border);
        background: var(--card-bg);
        text-align: center;
        text-decoration: none;
        color: var(--text-muted);
        transition: var(--transition);
        cursor: pointer;
        display: block;
    }

    .cat-card:hover {
        border-color: rgba(124, 58, 237, .35);
        color: #a78bfa;
        background: var(--card-hover);
        transform: translateY(-4px);
        box-shadow: var(--shadow);
    }

    [data-theme="light"] .cat-card {
        background: #fff;
        box-shadow: 0 2px 14px rgba(15, 23, 42, .06);
    }

    [data-theme="light"] .cat-card:hover {
        box-shadow: 0 8px 36px rgba(124, 58, 237, .12);
    }

    .cat-icon {
        font-size: 2rem;
        margin-bottom: 12px;
        display: block;
    }

    .cat-name {
        font-size: 13px;
        font-weight: 700;
    }

    /* ══ HOW IT WORKS ══ */
    .how-section {
        padding: 100px 0;
    }

    .steps-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
        gap: 24px;
    }

    .step-card {
        text-align: center;
        padding: 36px 22px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--card-bg);
        transition: var(--transition);
    }

    .step-card:hover {
        border-color: rgba(124, 58, 237, .3);
        transform: translateY(-5px);
        box-shadow: var(--shadow);
    }

    [data-theme="light"] .step-card {
        background: #fff;
        box-shadow: 0 2px 16px rgba(15, 23, 42, .06);
    }

    .step-num {
        width: 52px;
        height: 52px;
        border-radius: 50%;
        background: var(--grad);
        color: #fff;
        font-size: 20px;
        font-weight: 900;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        box-shadow: 0 6px 24px rgba(124, 58, 237, .4);
    }

    .step-title {
        font-size: 17px;
        font-weight: 800;
        color: var(--text);
        margin-bottom: 10px;
        transition: color var(--transition);
    }

    .step-desc {
        font-size: 13px;
        color: var(--text-muted);
        line-height: 1.7;
        transition: color var(--transition);
    }

    /* ══ TESTIMONIALS ══ */
    .testi-section {
        padding: 100px 0;
    }

    .testi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
    }

    .testi-card {
        padding: 30px;
        border-radius: var(--radius-lg);
        border: 1px solid var(--border);
        background: var(--card-bg);
        transition: var(--transition);
        position: relative;
    }

    .testi-card:hover {
        border-color: rgba(124, 58, 237, .28);
        transform: translateY(-5px);
        box-shadow: var(--shadow);
    }

    [data-theme="light"] .testi-card {
        background: #fff;
        box-shadow: 0 2px 16px rgba(15, 23, 42, .06);
    }

    .stars {
        color: #f59e0b;
        font-size: 13px;
        letter-spacing: 2px;
        margin-bottom: 14px;
    }

    .testi-quote {
        font-size: 14px;
        color: var(--text-muted);
        line-height: 1.75;
        margin-bottom: 22px;
        font-style: italic;
        transition: color var(--transition);
    }

    .testi-author {
        display: flex;
        align-items: center;
        gap: 13px;
    }

    .testi-avatar {
        width: 42px;
        height: 42px;
        border-radius: 13px;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 17px;
        font-weight: 800;
        color: #fff;
        flex-shrink: 0;
    }

    .testi-name {
        font-size: 14px;
        font-weight: 800;
        color: var(--text);
        transition: color var(--transition);
    }

    .testi-role {
        font-size: 12px;
        color: var(--text-faint);
        transition: color var(--transition);
    }

    /* ══ CTA BANNER ══ */
    .cta-section {
        padding: 80px 0 110px;
    }

    .cta-inner {
        border-radius: 30px;
        background: linear-gradient(135deg, rgba(124, 58, 237, .16), rgba(79, 70, 229, .16) 50%, rgba(6, 182, 212, .1));
        border: 1px solid rgba(124, 58, 237, .22);
        padding: 80px 52px;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    [data-theme="light"] .cta-inner {
        background: linear-gradient(135deg, rgba(124, 58, 237, .07), rgba(79, 70, 229, .07) 50%, rgba(6, 182, 212, .05));
        box-shadow: 0 8px 48px rgba(124, 58, 237, .12);
    }

    .cta-inner::before {
        content: '';
        position: absolute;
        top: -120px;
        left: 50%;
        transform: translateX(-50%);
        width: 700px;
        height: 360px;
        background: radial-gradient(ellipse, rgba(124, 58, 237, .22) 0%, transparent 70%);
    }

    .cta-h2 {
        font-size: clamp(1.8rem, 4vw, 2.7rem);
        font-weight: 900;
        letter-spacing: -.04em;
        color: var(--text);
        margin-bottom: 18px;
        position: relative;
        transition: color var(--transition);
    }

    .cta-sub {
        color: var(--text-muted);
        font-size: 1.06rem;
        margin-bottom: 38px;
        position: relative;
        transition: color var(--transition);
    }

    .cta-btns {
        display: flex;
        gap: 14px;
        justify-content: center;
        flex-wrap: wrap;
        position: relative;
    }

    /* ══ FOOTER ══ */
    .lp-footer {
        background: var(--footer-bg);
        border-top: 1px solid var(--footer-border);
        padding: 64px 28px 40px;
        position: relative;
        z-index: 1;
        transition: background var(--transition), border-color var(--transition);
    }

    .footer-top {
        display: grid;
        grid-template-columns: 1.5fr repeat(3, 1fr);
        gap: 40px;
        margin-bottom: 48px;
    }

    .footer-brand {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .footer-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        width: fit-content;
    }

    .footer-logo-icon {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        background: var(--grad);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        font-weight: 800;
        color: #fff;
    }

    .footer-logo-text {
        font-size: 20px;
        font-weight: 800;
        color: var(--footer-logo-text);
        transition: color var(--transition);
    }

    .footer-tagline {
        font-size: 13.5px;
        color: var(--footer-text);
        line-height: 1.6;
        margin: 0;
        max-width: 280px;
        transition: color var(--transition);
    }

    .footer-socials {
        display: flex;
        gap: 10px;
    }

    .social-btn {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        border: 1px solid var(--footer-border);
        background: var(--footer-badge-bg);
        color: var(--footer-text);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.2s ease;
    }

    .social-btn:hover {
        border-color: var(--accent);
        color: #ffffff;
        background: var(--accent);
        transform: translateY(-2px);
    }

    .footer-col-title {
        font-size: 11px;
        font-weight: 700;
        color: var(--footer-title);
        text-transform: uppercase;
        letter-spacing: 1.2px;
        margin-bottom: 20px;
        transition: color var(--transition);
    }

    .footer-col ul {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .footer-col ul li a {
        font-size: 13.5px;
        color: var(--footer-text);
        text-decoration: none;
        transition: color 0.2s ease;
    }

    .footer-col ul li a:hover {
        color: var(--accent);
    }

    .footer-bottom {
        border-top: 1px solid var(--footer-border);
        padding-top: 28px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 16px;
        transition: border-color var(--transition);
    }

    .footer-copy {
        font-size: 13px;
        color: var(--footer-text);
        margin: 0;
        opacity: 0.85;
        transition: color var(--transition);
    }

    .footer-badges {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .footer-badge {
        padding: 5px 12px;
        border-radius: 6px;
        border: 1px solid var(--footer-badge-border);
        background: var(--footer-badge-bg);
        font-size: 10.5px;
        font-weight: 600;
        color: var(--footer-text);
        letter-spacing: .3px;
        transition: all 0.2s ease;
    }

    .footer-badge:hover {
        border-color: var(--accent);
        color: var(--accent);
    }

    /* ══ ANIMATIONS ══ */
    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-22px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(32px);
        }

        to {
            opacity: 1;
            transform: none;
        }
    }

    .reveal {
        opacity: 0;
        transform: translateY(42px);
        transition: opacity .75s ease, transform .75s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: none;
    }

    .reveal-delay-1 {
        transition-delay: .1s;
    }

    .reveal-delay-2 {
        transition-delay: .2s;
    }

    .reveal-delay-3 {
        transition-delay: .3s;
    }

    .reveal-delay-4 {
        transition-delay: .45s;
    }

    /* Floating particles */
    .particle {
        position: fixed;
        pointer-events: none;
        z-index: 0;
        border-radius: 50%;
        animation: floatParticle linear infinite;
        opacity: 0;
    }

    @keyframes floatParticle {
        0% {
            transform: translateY(100vh) scale(0);
            opacity: 0;
        }

        10% {
            opacity: .5;
        }

        90% {
            opacity: .2;
        }

        100% {
            transform: translateY(-120px) scale(2);
            opacity: 0;
        }
    }

    /* ── Responsive ── */
    @media(max-width:900px) {
        .footer-top {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media(max-width:768px) {
        .lp-nav {
            padding: 0 18px;
        }

        .hero-actions {
            flex-direction: column;
            align-items: center;
        }

        .hero-cta-primary,
        .hero-cta-secondary {
            width: 100%;
            max-width: 340px;
            justify-content: center;
        }

        .stats-inner {
            padding: 40px 24px;
            gap: 30px;
        }

        .cta-inner {
            padding: 52px 24px;
        }

        .footer-top {
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .footer-col {
            border-bottom: 1px solid var(--footer-border);
            padding-bottom: 16px;
        }

        .footer-col:last-of-type {
            border-bottom: none;
        }

        .footer-col-title {
            margin-bottom: 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 10px 0;
        }

        .footer-col-title::after {
            content: "▼";
            font-size: 9px;
            color: var(--footer-text);
            transition: transform 0.3s ease;
            opacity: 0.65;
        }

        .footer-col.active .footer-col-title::after {
            transform: rotate(180deg);
        }

        .footer-col ul {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, margin-top 0.3s ease;
            margin-top: 0;
            gap: 8px;
        }

        .footer-col.active ul {
            max-height: 250px;
            margin-top: 14px;
        }
    }

    @media(max-width:480px) {
        .nav-logo-wordmark {
            display: none;
        }

        .lp-nav {
            height: 64px;
        }
    }
    </style>
</head>

<body>

    <!-- Ambient -->
    <div class="ambient-bg">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
        <div class="orb orb-4"></div>
    </div>
    <div class="mesh-grid"></div>
    <div id="particles"></div>

    <!-- Logged-in bar -->
    <?php if ($already_logged_in): ?>
    <div class="logged-bar">
        Welcome back! You're already signed in. <a href="/Quigly/index.php">Go to your account →</a>
    </div>
    <?php endif; ?>

    <!-- ══ NAVBAR ══ -->
    <nav class="lp-nav" id="lpNav">
        <a href="/Quigly/landing.php" class="nav-logo">
            <div class="nav-logo-icon">Q</div>
            <span class="nav-logo-wordmark">Quig<span>ly</span></span>
        </a>
        <div class="nav-right">
            <!-- Theme toggle -->
            <button class="theme-toggle" id="themeToggle" title="Toggle theme" onclick="toggleTheme()">
                <i class="fas fa-moon" id="themeIcon"></i>
            </button>
            <a href="<?= htmlspecialchars($hero_cta2_url) ?>" class="btn-ghost"
                id="nav-login-btn"><?= htmlspecialchars($hero_cta2_label) ?></a>
            <a href="<?= htmlspecialchars($hero_cta_url) ?>" class="btn-primary-nav"
                id="nav-signup-btn"><?= htmlspecialchars($hero_cta_label) ?></a>
        </div>
    </nav>

    <!-- ══ HERO ══ -->
    <section class="hero" id="hero">
        <div class="hero-inner">
            <div class="hero-badge">
                <span class="dot"></span>
                Global E-Commerce Platform · Trusted Worldwide
            </div>
            <h1 class="hero-h1"><?= $hero_headline ?></h1>
            <p class="hero-sub"><?= htmlspecialchars($hero_subtext) ?></p>
            <div class="hero-actions">
                <a href="<?= htmlspecialchars($hero_cta_url) ?>" class="hero-cta-primary" id="hero-main-cta">
                    <?= htmlspecialchars($hero_cta_label) ?>
                    <i class="fas fa-arrow-right"></i>
                </a>
                <a href="<?= htmlspecialchars($hero_cta2_url) ?>" class="hero-cta-secondary" id="hero-secondary-cta">
                    <i class="fas fa-sign-in-alt"></i>
                    <?= htmlspecialchars($hero_cta2_label) ?>
                </a>
            </div>
            <div class="trust-badges">
                <div class="trust-badge"><i class="fas fa-lock"></i> Secure Payments</div>
                <div class="trust-badge"><i class="fas fa-truck-fast"></i> Express Delivery</div>
                <div class="trust-badge"><i class="fas fa-rotate-left"></i> Easy Returns</div>
                <div class="trust-badge"><i class="fas fa-star"></i> 4.9★ Rated</div>
            </div>
        </div>
    </section>

    <!-- ══ FEATURES ══ -->
    <section class="features-section" id="features">
        <div class="container">
            <div class="text-center reveal">
                <div class="sec-tag"><i class="fas fa-sparkles"></i> Why Quigly</div>
                <h2 class="sec-h2">Shopping <span class="grad-text">Reimagined</span> for the Modern World</h2>
                <p class="sec-sub">From discovery to delivery — we've built the most seamless, secure, and rewarding
                    global shopping experience.</p>
            </div>
            <div class="features-grid">
                <?php $feats=[[$feat1_icon,$feat1_title,$feat1_desc],[$feat2_icon,$feat2_title,$feat2_desc],[$feat3_icon,$feat3_title,$feat3_desc],[$feat4_icon,$feat4_title,$feat4_desc]];
      $ds=['reveal-delay-1','reveal-delay-2','reveal-delay-3','reveal-delay-4'];
      foreach($feats as $i=>$f): ?>
                <div class="feat-card reveal <?= $ds[$i] ?>">
                    <div class="feat-icon"><i class="<?= htmlspecialchars($f[0]) ?>"></i></div>
                    <div class="feat-title"><?= htmlspecialchars($f[1]) ?></div>
                    <p class="feat-desc"><?= htmlspecialchars($f[2]) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══ STATS ══ -->
    <section class="stats-section" id="stats">
        <div class="container">
            <div class="stats-inner reveal">
                <?php $stats=[[$stat1_num,$stat1_label],[$stat2_num,$stat2_label],[$stat3_num,$stat3_label],[$stat4_num,$stat4_label]];
      foreach($stats as $s): ?>
                <div class="stat-item">
                    <div class="stat-num" data-target="<?= htmlspecialchars($s[0]) ?>"><?= htmlspecialchars($s[0]) ?>
                    </div>
                    <div class="stat-label"><?= htmlspecialchars($s[1]) ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══ CATEGORIES ══ -->
    <section class="cats-section" id="categories">
        <div class="container">
            <div class="text-center reveal">
                <div class="sec-tag"><i class="fas fa-th"></i> Browse Categories</div>
                <h2 class="sec-h2">Find Everything <span class="grad-text">You Need</span></h2>
                <p class="sec-sub">Millions of products across hundreds of categories — all in one place.</p>
            </div>
            <div class="cats-grid">
                <?php
      $cats = [
        ['📱','Electronics'],['👗','Fashion'],['🏠','Home & Living'],['💄','Beauty'],
        ['📚','Books'],['⚽','Sports'],['🧸','Toys'],['🌿','Health'],
      ];
      foreach ($cats as $i => $cat): ?>
                <a href="/Quigly/login.php" class="cat-card reveal <?= $ds[min($i,3)] ?? '' ?>">
                    <span class="cat-icon"><?= $cat[0] ?></span>
                    <span class="cat-name"><?= $cat[1] ?></span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══ HOW IT WORKS ══ -->
    <section class="how-section" id="how">
        <div class="container">
            <div class="text-center reveal">
                <div class="sec-tag"><i class="fas fa-map-signs"></i> Simple Process</div>
                <h2 class="sec-h2">Start Shopping in <span class="grad-text">4 Easy Steps</span></h2>
                <p class="sec-sub">Join millions of happy shoppers in minutes — no complexity, just great deals.</p>
            </div>
            <div class="steps-grid">
                <?php $steps=[['1','Create Account','Sign up free in under 60 seconds. No credit card required to get started.'],['2','Discover Products','Browse millions of items from verified sellers across the globe.'],['3','Checkout Securely','Pay with your preferred method — 100% encrypted & protected.'],['4','Receive & Enjoy','Fast tracked delivery right to your door, with real-time updates.']];
      foreach($steps as $i=>$step): ?>
                <div class="step-card reveal <?= $ds[$i] ?>">
                    <div class="step-num"><?= $step[0] ?></div>
                    <div class="step-title"><?= $step[1] ?></div>
                    <p class="step-desc"><?= $step[2] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══ TESTIMONIALS ══ -->
    <section class="testi-section" id="testimonials">
        <div class="container">
            <div class="text-center reveal">
                <div class="sec-tag"><i class="fas fa-quote-left"></i> Real Reviews</div>
                <h2 class="sec-h2">Loved by <span class="grad-text-pink">Millions</span> Worldwide</h2>
                <p class="sec-sub">Don't just take our word for it — here's what our customers say.</p>
            </div>
            <div class="testi-grid">
                <?php $testis=[[$t1_init,$t1_name,$t1_role,$t1_text],[$t2_init,$t2_name,$t2_role,$t2_text],[$t3_init,$t3_name,$t3_role,$t3_text]];
      foreach($testis as $i=>$t): ?>
                <div class="testi-card reveal <?= $ds[$i] ?? '' ?>">
                    <div class="stars">★★★★★</div>
                    <p class="testi-quote">"<?= htmlspecialchars($t[3]) ?>"</p>
                    <div class="testi-author">
                        <div class="testi-avatar"><?= htmlspecialchars($t[0]) ?></div>
                        <div>
                            <div class="testi-name"><?= htmlspecialchars($t[1]) ?></div>
                            <div class="testi-role"><?= htmlspecialchars($t[2]) ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ══ CTA BANNER ══ -->
    <section class="cta-section" id="cta">
        <div class="container">
            <div class="cta-inner reveal">
                <h2 class="cta-h2"><?= htmlspecialchars($cta_title) ?></h2>
                <p class="cta-sub"><?= htmlspecialchars($cta_sub) ?></p>
                <div class="cta-btns">
                    <a href="<?= htmlspecialchars($cta_btn_url) ?>" class="hero-cta-primary" id="cta-signup-btn">
                        <?= htmlspecialchars($cta_btn) ?> <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="/Quigly/login.php" class="hero-cta-secondary" id="cta-login-btn">
                        <i class="fas fa-sign-in-alt"></i> Sign In to Your Account
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- ══ FOOTER ══ -->
    <footer class="lp-footer">
        <div class="container">
            <div class="footer-top">
                <div class="footer-brand">
                    <a href="/Quigly/landing.php" class="footer-logo">
                        <div class="footer-logo-icon">Q</div>
                        <span class="footer-logo-text">Quigly</span>
                    </a>
                    <p class="footer-tagline"><?= htmlspecialchars($footer_tag) ?></p>
                    <div class="footer-socials">
                        <a href="#" class="social-btn"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="social-btn"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-col">
                    <div class="footer-col-title">Shop</div>
                    <ul>
                        <li><a href="/Quigly/login.php">New Arrivals</a></li>
                        <li><a href="/Quigly/login.php">Best Sellers</a></li>
                        <li><a href="/Quigly/login.php">Deals &amp; Offers</a></li>
                        <li><a href="/Quigly/login.php">All Categories</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <div class="footer-col-title">Account</div>
                    <ul>
                        <li><a href="/Quigly/register.php">Create Account</a></li>
                        <li><a href="/Quigly/login.php">Sign In</a></li>
                        <li><a href="/Quigly/login.php">My Orders</a></li>
                        <li><a href="/Quigly/login.php">Wishlist</a></li>
                    </ul>
                </div>
                <div class="footer-col">
                    <div class="footer-col-title">Support</div>
                    <ul>
                        <li><a href="/Quigly/login.php">Help Center</a></li>
                        <li><a href="/Quigly/login.php">Returns &amp; Refunds</a></li>
                        <li><a href="/Quigly/login.php">Track Order</a></li>
                        <li><a href="/Quigly/login.php">Contact Us</a></li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p class="footer-copy">&copy; <?= date('Y') ?> Quigly. All rights reserved.</p>
                <div class="footer-badges">
                    <span class="footer-badge">🔒 SSL Secured</span>
                    <span class="footer-badge">✅ Verified Sellers</span>
                    <span class="footer-badge">📦 Fast Delivery</span>
                    <span class="footer-badge">↩ Easy Returns</span>
                </div>
            </div>
        </div>
    </footer>

    <script>
    // ── Theme toggle ──
    function toggleTheme() {
        const html = document.documentElement;
        const current = html.getAttribute('data-theme');
        const next = current === 'dark' ? 'light' : 'dark';
        html.setAttribute('data-theme', next);
        localStorage.setItem('quiglyLandingTheme', next);
        updateThemeIcon(next);
    }

    function updateThemeIcon(theme) {
        const icon = document.getElementById('themeIcon');
        if (!icon) return;
        icon.className = theme === 'dark' ? 'fas fa-sun' : 'fas fa-moon';
    }
    // Set correct icon on load
    (function() {
        const saved = localStorage.getItem('quiglyLandingTheme') || 'dark';
        updateThemeIcon(saved);
    })();

    // ── Navbar scroll ──
    const nav = document.getElementById('lpNav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 30);
    });

    // ── Scroll reveal ──
    const obs = new IntersectionObserver(entries => {
        entries.forEach(e => {
            if (e.isIntersecting) e.target.classList.add('visible');
        });
    }, {
        threshold: 0.12
    });
    document.querySelectorAll('.reveal').forEach(el => obs.observe(el));

    // ── Floating particles ──
    const pCont = document.getElementById('particles');
    const colors = ['#7c3aed', '#4f46e5', '#06b6d4', '#ec4899', '#a78bfa'];
    for (let i = 0; i < 20; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        const sz = Math.random() * 4 + 2;
        p.style.cssText =
            `width:${sz}px;height:${sz}px;left:${Math.random()*100}vw;background:${colors[i%colors.length]};animation-duration:${Math.random()*14+8}s;animation-delay:${Math.random()*12}s;`;
        pCont.appendChild(p);
    }

    // ── Counter animation ──
    function animateCount(el) {
        const raw = el.getAttribute('data-target') || el.textContent;
        const num = parseInt(raw.replace(/[^0-9]/g, ''));
        if (!num) return;
        const suffix = raw.replace(/[0-9,]/g, '');
        let cur = 0;
        const dur = 2000;
        const steps = 60;
        const inc = num / (dur / steps * 60 / 60);
        const t = setInterval(() => {
            cur += inc;
            if (cur >= num) {
                cur = num;
                clearInterval(t);
            }
            el.textContent = Math.floor(cur).toLocaleString() + suffix;
        }, dur / steps);
    }
    let counted = false;
    new IntersectionObserver(ent => {
        if (ent[0].isIntersecting && !counted) {
            counted = true;
            document.querySelectorAll('.stat-num').forEach(animateCount);
        }
    }, {
        threshold: 0.3
    }).observe(document.querySelector('.stats-section'));

    // ── Mobile Footer Accordions ──
    document.querySelectorAll('.footer-col-title').forEach(title => {
        title.addEventListener('click', () => {
            if (window.innerWidth <= 768) {
                const col = title.parentElement;
                
                // Toggle active state
                col.classList.toggle('active');
                
                // Optional: Collapse other accordion columns (accordion behavior)
                document.querySelectorAll('.footer-col').forEach(otherCol => {
                    if (otherCol !== col) {
                        otherCol.classList.remove('active');
                    }
                });
            }
        });
    });
    </script>
</body>

</html>