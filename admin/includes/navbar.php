<style>
/* ── GLASSMORPHISM NAVBAR ── */
.navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1100;
    transition: all 0.35s cubic-bezier(0.4, 0, 0.2, 1);
    /* Default: full height */
    padding-top: 14px;
    padding-bottom: 14px;
    background: rgba(10, 12, 28, 0.55) !important;
    backdrop-filter: blur(22px) saturate(180%);
    -webkit-backdrop-filter: blur(22px) saturate(180%);
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.25);
}

body.light-mode .navbar {
    background: rgba(255, 255, 255, 0.72) !important;
    border-bottom: 1px solid rgba(0, 0, 0, 0.07);
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.08);
}

/* Scrolled state — smaller, more opaque */
.navbar.scrolled {
    padding-top: 8px;
    padding-bottom: 8px;
    background: rgba(10, 12, 28, 0.88) !important;
    backdrop-filter: blur(28px) saturate(200%);
    -webkit-backdrop-filter: blur(28px) saturate(200%);
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.4);
}

body.light-mode .navbar.scrolled {
    background: rgba(255, 255, 255, 0.92) !important;
    box-shadow: 0 8px 40px rgba(0, 0, 0, 0.12);
}

/* ── Brand ── */
.navbar-brand {
    font-family: 'Space Grotesk', sans-serif;
    font-weight: 800;
    font-size: 1.45rem;
    letter-spacing: -0.03em;
    background: linear-gradient(135deg, #a78bfa, #60a5fa);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: opacity 0.2s;
}

.navbar-brand:hover {
    opacity: 0.85;
}

.navbar-brand .brand-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #7c3aed, #2563eb);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    font-size: 15px;
    flex-shrink: 0;
    box-shadow: 0 6px 18px rgba(124, 58, 237, 0.4);
    transition: transform 0.2s;
}

.navbar-brand:hover .brand-icon {
    transform: rotate(-8deg) scale(1.08);
}

/* ── Nav links ── */
.navbar .nav-link {
    font-family: 'Inter', sans-serif;
    font-weight: 500;
    font-size: 0.92rem;
    color: rgba(255, 255, 255, 0.75) !important;
    padding: 6px 12px !important;
    border-radius: 8px;
    position: relative;
    transition: color 0.2s, background 0.2s;
}

body.light-mode .navbar .nav-link {
    color: rgba(15, 23, 42, 0.72) !important;
}

.navbar .nav-link::after {
    content: '';
    position: absolute;
    bottom: 2px;
    left: 50%;
    transform: translateX(-50%) scaleX(0);
    width: 60%;
    height: 2px;
    border-radius: 2px;
    background: linear-gradient(90deg, #7c3aed, #2563eb);
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1);
}

.navbar .nav-link:hover::after,
.navbar .nav-link.active::after {
    transform: translateX(-50%) scaleX(1);
}

.navbar .nav-link:hover,
.navbar .nav-link.active {
    color: #fff !important;
    background: rgba(255, 255, 255, 0.08);
}

body.light-mode .navbar .nav-link:hover,
body.light-mode .navbar .nav-link.active {
    color: #0f172a !important;
    background: rgba(0, 0, 0, 0.06);
}

.navbar .nav-link.active {
    font-weight: 600;
}

/* ── Icon buttons (cart, wishlist, theme) ── */
.icon-link,
.theme-toggle {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.8) !important;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.1);
    transition: background 0.2s, transform 0.2s, border-color 0.2s;
    cursor: pointer;
    text-decoration: none;
}

body.light-mode .icon-link,
body.light-mode .theme-toggle {
    color: #374151 !important;
    background: rgba(0, 0, 0, 0.05);
    border: 1px solid rgba(0, 0, 0, 0.08);
}

.icon-link:hover,
.theme-toggle:hover {
    background: rgba(124, 58, 237, 0.25);
    border-color: rgba(124, 58, 237, 0.4);
    transform: translateY(-1px);
    color: #fff !important;
}

/* ── Badge on icons ── */
.icon-badge-wrap {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
}

.cart-badge,
.favorites-badge {
    position: absolute;
    top: -9px;
    right: -10px;
    min-width: 18px;
    height: 18px;
    padding: 0 4px;
    border-radius: 99px;
    background: linear-gradient(135deg, #ef4444, #dc2626);
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 2px solid transparent;
    box-shadow: 0 2px 8px rgba(239, 68, 68, 0.4);
}

/* ── Account button ── */
.account-btn {
    background: rgba(255, 255, 255, 0.1) !important;
    border: 1px solid rgba(255, 255, 255, 0.15) !important;
    color: rgba(255, 255, 255, 0.85) !important;
    border-radius: 10px !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    padding: 7px 14px !important;
    transition: background 0.2s, border-color 0.2s !important;
}

body.light-mode .account-btn {
    background: rgba(0, 0, 0, 0.05) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    color: #0f172a !important;
}

.account-btn:hover,
.account-btn:focus {
    background: rgba(124, 58, 237, 0.25) !important;
    border-color: rgba(124, 58, 237, 0.4) !important;
    color: #fff !important;
}

/* ── Dropdown ── */
.account-menu {
    border-radius: 16px !important;
    border: 1px solid rgba(255, 255, 255, 0.1) !important;
    background: rgba(15, 20, 40, 0.95) !important;
    backdrop-filter: blur(20px) !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4) !important;
    padding: 8px !important;
    min-width: 200px !important;
}

body.light-mode .account-menu {
    background: rgba(255, 255, 255, 0.97) !important;
    border: 1px solid rgba(0, 0, 0, 0.08) !important;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.12) !important;
}

.account-menu .dropdown-item {
    border-radius: 10px !important;
    padding: 9px 14px !important;
    font-size: 0.88rem !important;
    font-weight: 500 !important;
    color: rgba(255, 255, 255, 0.8) !important;
    transition: background 0.15s, color 0.15s !important;
    display: flex !important;
    align-items: center !important;
    gap: 8px !important;
}

body.light-mode .account-menu .dropdown-item {
    color: #374151 !important;
}

.account-menu .dropdown-item:hover {
    background: rgba(124, 58, 237, 0.2) !important;
    color: #fff !important;
}

body.light-mode .account-menu .dropdown-item:hover {
    background: rgba(124, 58, 237, 0.1) !important;
    color: #7c3aed !important;
}

.account-menu .dropdown-item.text-danger {
    color: #f87171 !important;
}

.account-menu .dropdown-divider {
    border-color: rgba(255, 255, 255, 0.08) !important;
    margin: 4px 8px !important;
}

body.light-mode .account-menu .dropdown-divider {
    border-color: rgba(0, 0, 0, 0.06) !important;
}

/* ── Mobile toggler ── */
.navbar-toggler {
    border: 1px solid rgba(255, 255, 255, 0.2) !important;
    border-radius: 8px !important;
    padding: 6px 10px !important;
}

body.light-mode .navbar-toggler {
    border: 1px solid rgba(0, 0, 0, 0.15) !important;
}

.navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%28255%2c255%2c255%2c0.8%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
}

body.light-mode .navbar-toggler-icon {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba%280%2c0%2c0%2c0.7%29' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e") !important;
}

/* ── Mobile expanded menu ── */
@media (max-width: 991.98px) {

    /* Give the collapsed menu a proper background so it floats cleanly */
    .navbar-collapse {
        margin-top: 10px;
        background: rgba(10, 12, 28, 0.97);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border-radius: 16px;
        padding: 14px 12px 12px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.35);
    }

    body.light-mode .navbar-collapse {
        background: rgba(255, 255, 255, 0.98);
        border: 1px solid rgba(0, 0, 0, 0.08);
        box-shadow: 0 16px 48px rgba(0, 0, 0, 0.12);
    }

    /* Stack nav links vertically with comfortable tap targets */
    .navbar-nav .nav-item {
        width: 100%;
    }

    .navbar-nav .nav-link {
        padding: 10px 14px !important;
        border-radius: 10px;
        font-size: 0.95rem !important;
    }

    /* Underline indicator looks odd on mobile — use bg highlight instead */
    .navbar .nav-link::after {
        display: none !important;
    }

    .navbar .nav-link.active,
    .navbar .nav-link:hover {
        background: rgba(124, 58, 237, 0.15) !important;
        color: #c4b5fd !important;
    }

    body.light-mode .navbar .nav-link.active,
    body.light-mode .navbar .nav-link:hover {
        background: rgba(124, 58, 237, 0.1) !important;
        color: #7c3aed !important;
    }

    /* Icon buttons row: spread them out, full width */
    .navbar-collapse .d-flex {
        flex-wrap: wrap;
        justify-content: flex-start;
        gap: 8px !important;
        padding-top: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.07);
        margin-top: 6px;
    }

    body.light-mode .navbar-collapse .d-flex {
        border-top-color: rgba(0, 0, 0, 0.06);
    }

    /* Make account button fill available space */
    .account-dropdown {
        flex: 1;
    }

    .account-btn {
        width: 100%;
        justify-content: center;
    }

    /* Dropdown menu inside mobile: show as inline block, no absolute */
    .account-dropdown .dropdown-menu {
        position: static !important;
        transform: none !important;
        margin-top: 6px !important;
        width: 100% !important;
        box-shadow: none !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
    }

    body.light-mode .account-dropdown .dropdown-menu {
        border-color: rgba(0, 0, 0, 0.08) !important;
    }
}

/* ── Extra-small phones (< 390px) ── */
@media (max-width: 389px) {
    .navbar-brand {
        font-size: 1.15rem;
    }

    .navbar-brand .brand-icon {
        width: 28px;
        height: 28px;
        font-size: 13px;
    }

    .icon-link,
    .theme-toggle {
        width: 34px;
        height: 34px;
    }
}
</style>

<nav class="navbar navbar-expand-lg fixed-top" id="mainNavbar">
    <div class="container">
        <a class="navbar-brand" href="#" data-section="home" onclick="return showSection('home', event)">
            <?php if (!empty($logoRow['image'])): ?>
            <img src="<?= htmlspecialchars($logoRow['image']) ?>" alt="Logo"
                style="height:34px;width:auto;object-fit:contain;">
            <?php else: ?>
            <div class="brand-icon"><i class="fas fa-bolt"></i></div>
            <?php endif; ?>
            Quigly
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto ms-3">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-section="home"
                        onclick="return showSection('home', event)">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="products"
                        onclick="return showSection('products', event)">Products</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="categories"
                        onclick="return showSection('categories', event)">Categories</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-section="deals"
                        onclick="return showSection('deals', event)">Deals</a>
                </li>
            </ul>

            <div class="d-flex align-items-center gap-2 ms-lg-auto">
                <!-- Theme toggle -->
                <div class="theme-toggle" onclick="toggleTheme()" title="Toggle theme">
                    <i class="fas fa-moon" id="themeIcon"></i>
                </div>

                <!-- Wishlist -->
                <a class="icon-link" href="#" data-section="favorites" onclick="return showSection('favorites', event)"
                    title="Wishlist">
                    <span class="icon-badge-wrap">
                        <i class="fas fa-heart"></i>
                        <span id="favoritesCount" class="favorites-badge">0</span>
                    </span>
                </a>

                <!-- Cart -->
                <a href="#" class="icon-link" data-section="cart" onclick="return showSection('cart', event)"
                    title="Cart">
                    <span class="icon-badge-wrap">
                        <i class="fas fa-shopping-cart"></i>
                        <span id="cartCount" class="cart-badge">0</span>
                    </span>
                </a>

                <!-- Account -->
                <div class="dropdown account-dropdown">
                    <button class="btn dropdown-toggle account-btn" type="button" id="userDropdown"
                        data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                        <i class="fas fa-user me-1"></i>
                        <span id="userName">Account</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end account-menu">
                        <li><a class="dropdown-item" href="#" data-section="profile"
                                onclick="return showSection('profile', event)"><i class="fas fa-user-circle"></i>My
                                Profile</a>
                        </li>
                        <li><a class="dropdown-item" href="#" data-section="orders"
                                onclick="return showSection('orders', event)"><i class="fas fa-box"></i>My Orders</a>
                        </li>
                        <li><a class="dropdown-item" href="#" data-section="favorites"
                                onclick="return showSection('favorites', event)"><i class="fas fa-heart"></i>My
                                Favorites</a>
                        </li>
                        <li><a class="dropdown-item" href="#" data-section="offers"
                                onclick="return showSection('offers', event)"><i class="fas fa-tag"></i>My Offers</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="admin/index.php" id="adminLink" style="display:none;"><i
                                    class="fas fa-cog me-2"></i>Admin Panel</a></li>
                        <li><a class="dropdown-item" href="#" id="sellerLink" style="display:none;"
                                onclick="return showSection('seller', event)"><i class="fas fa-store"></i>Seller
                                Dashboard</a>
                        </li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#" onclick="return showSection('support', event)">
                                <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960"
                                    width="24px" fill="#000">
                                    <path
                                        d="m480-80-10-120h-10q-142 0-241-99t-99-241q0-142 99-241t241-99q71 0 132.5 26.5t108 73q46.5 46.5 73 108T800-540q0 75-24.5 144t-67 128q-42.5 59-101 107T480-80Zm80-146q71-60 115.5-140.5T720-540q0-109-75.5-184.5T460-800q-109 0-184.5 75.5T200-540q0 109 75.5 184.5T460-280h100v54Zm-72-107q12-12 12-29t-12-29q-12-12-29-12t-29 12q-12 12-12 29t12 29q12 12 29 12t29-12Zm-58-115h60q0-30 6-42t38-44q18-18 30-39t12-45q0-51-34.5-76.5T460-720q-44 0-74 24.5T344-636l56 22q5-17 19-33.5t41-16.5q27 0 40.5 15t13.5 33q0 17-10 30.5T480-558q-35 30-42.5 47.5T430-448Zm30-65Z" />
                                </svg>Support
                            </a>
                        </li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="logout()"><i
                                    class="fas fa-sign-out-alt"></i>Logout</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Scroll-shrink effect
(function() {
    const nav = document.getElementById('mainNavbar');
    if (!nav) return;
    window.addEventListener('scroll', function() {
        nav.classList.toggle('scrolled', window.scrollY > 50);
    }, {
        passive: true
    });
})();

// ── Close mobile navbar on outside click ──
document.addEventListener('click', function(e) {
    const navbarCollapse = document.getElementById('navbarNav');
    const toggler = document.querySelector('.navbar-toggler');
    if (!navbarCollapse || !toggler) return;

    // Check if the collapse menu is currently open
    const isOpen = navbarCollapse.classList.contains('show');
    if (!isOpen) return;

    // If click is outside the navbar, close it
    const clickedInside = navbarCollapse.contains(e.target) || toggler.contains(e.target);
    if (!clickedInside) {
        const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
        if (bsCollapse) {
            bsCollapse.hide();
        } else {
            new bootstrap.Collapse(navbarCollapse, { toggle: true }).hide();
        }
    }
});

// ── Close mobile navbar when a nav link is clicked ──
document.querySelectorAll('#navbarNav .nav-link, #navbarNav .dropdown-item, #navbarNav .icon-link').forEach(link => {
    link.addEventListener('click', function() {
        const navbarCollapse = document.getElementById('navbarNav');
        if (navbarCollapse && navbarCollapse.classList.contains('show')) {
            const bsCollapse = bootstrap.Collapse.getInstance(navbarCollapse);
            if (bsCollapse) bsCollapse.hide();
        }
    });
});
</script>