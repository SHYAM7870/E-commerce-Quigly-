<?php
include_once(__DIR__ . "/config.php");
include_once(__DIR__ . "/db.php");

if (!isset($conn) || !$conn) {
  die("Database connection not available. Check db.php");
}

$current = basename($_SERVER['PHP_SELF']);

// Pending support badge
$pendingSupportCount = 0;
$psq = mysqli_query($conn, "SELECT COUNT(*) AS total FROM support_tickets WHERE LOWER(status)='pending'");
if ($psq) {
  $pendingSupportCount = (int) (mysqli_fetch_assoc($psq)['total'] ?? 0);
}

// Pending orders badge
$pendingOrderCount = 0;
$oq = mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE LOWER(status) IN ('pending','processing')");
if ($oq) {
  $pendingOrderCount = (int) (mysqli_fetch_assoc($oq)['c'] ?? 0);
}

// Pending reviews badge
$pendingReviewCount = 0;
$rq = mysqli_query($conn, "SELECT COUNT(*) AS c FROM reviews WHERE status = 'pending'");
if ($rq) {
  $pendingReviewCount = (int) (mysqli_fetch_assoc($rq)['c'] ?? 0);
}
?>
<?php
$pendingSupport = mysqli_fetch_assoc(mysqli_query(
  $conn,
  "SELECT COUNT(*) AS total FROM support_tickets WHERE LOWER(status)='pending'"
));
$pendingSupportCount = (int) ($pendingSupport['total'] ?? 0);
?>
<style>
  /* ─── Reset ─── */
  * {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
  }

  /* ─── Sidebar ─── */
  .sidebar {
    width: var(--sb-width, 236px);
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    background: linear-gradient(180deg, #0d1117 0%, #030509 100%);
    color: #fff;
    z-index: 1040;
    overflow-y: auto;
    overflow-x: hidden;
    transition: width .28s cubic-bezier(.4, 0, .2, 1);
    box-shadow: 4px 0 32px rgba(0, 0, 0, .35);
    scrollbar-width: thin;
    scrollbar-color: #7c3aed transparent;
    border-right: 1px solid rgba(255,255,255,.04);
  }

  .sidebar::-webkit-scrollbar {
    width: 4px;
  }

  .sidebar::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #7c3aed, #4f46e5);
    border-radius: 20px;
  }

  .sidebar.collapsed {
    width: var(--sb-collapsed, 68px);
  }

  /* ─── Brand ─── */
  .sb-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 18px 14px 10px;
    border-bottom: 1px solid rgba(255, 255, 255, .06);
    flex-shrink: 0;
  }

  .sb-logo {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    font-weight: 900;
    color: #fff;
    flex-shrink: 0;
    box-shadow: 0 4px 14px rgba(124, 58, 237, .35);
  }

  .sb-name {
    font-size: 16px;
    font-weight: 800;
    letter-spacing: -.5px;
    color: #fff;
    transition: opacity .2s;
    white-space: nowrap;
  }

  .sidebar.collapsed .sb-name {
    opacity: 0;
    width: 0;
    overflow: hidden;
  }

  /* ─── Section label ─── */
  .sb-label {
    font-size: 10px;
    font-weight: 700;
    letter-spacing: 1.2px;
    text-transform: uppercase;
    color: #475569;
    padding: 14px 16px 4px;
    transition: opacity .2s;
    white-space: nowrap;
  }

  .sidebar.collapsed .sb-label {
    opacity: 0;
  }

  /* ─── Nav item ─── */
  .sb-nav {
    list-style: none;
    padding: 0 8px;
    flex: 1;
  }

  .sb-nav li {
    margin: 1px 0;
  }

  .sb-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 9px 10px;
    border-radius: 11px;
    color: #94a3b8;
    text-decoration: none;
    font-size: 13px;
    font-weight: 600;
    transition: all .2s;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
  }

  .sb-link:hover {
    background: rgba(124, 58, 237, .14);
    color: #c4b5fd;
    transform: translateX(2px);
  }

  .sb-link.active {
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    color: #fff;
    box-shadow: 0 6px 20px rgba(124, 58, 237, .28);
  }

  .sb-link i {
    width: 18px;
    text-align: center;
    font-size: 14px;
    flex-shrink: 0;
    transition: transform .2s;
  }

  .sb-link:hover i {
    transform: scale(1.12);
  }

  .sb-text {
    transition: opacity .2s, width .2s;
    overflow: hidden;
    white-space: nowrap;
  }

  .sidebar.collapsed .sb-text {
    opacity: 0;
    width: 0;
  }

  /* ─── Badge ─── */
  .sb-badge {
    margin-left: auto;
    min-width: 20px;
    height: 20px;
    padding: 0 5px;
    border-radius: 999px;
    background: #ef4444;
    color: #fff;
    font-size: 10px;
    font-weight: 800;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    animation: badgePop .3s ease;
    flex-shrink: 0;
    box-shadow: 0 4px 10px rgba(239, 68, 68, .3);
  }

  @keyframes badgePop {
    0% {
      transform: scale(0);
    }

    80% {
      transform: scale(1.18);
    }

    100% {
      transform: scale(1);
    }
  }

  .sidebar.collapsed .sb-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    min-width: 16px;
    height: 16px;
    font-size: 9px;
  }

  /* ─── Divider ─── */
  .sb-divider {
    height: 1px;
    background: rgba(255, 255, 255, .05);
    margin: 8px 10px;
  }

  /* ─── Toggle btn ─── */
  .sb-toggle {
    position: absolute;
    top: 20px;
    right: -13px;
    width: 26px;
    height: 26px;
    border-radius: 50%;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, .10);
    color: #94a3b8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 11px;
    z-index: 10;
    box-shadow: 0 4px 12px rgba(0, 0, 0, .25);
    transition: .2s;
  }

  .sb-toggle:hover {
    background: #7c3aed;
    color: #fff;
    border-color: transparent;
  }

  /* ─── Bottom profile ─── */
  .sb-profile {
    padding: 12px 10px;
    border-top: 1px solid rgba(255, 255, 255, .06);
    display: flex;
    align-items: center;
    gap: 10px;
    flex-shrink: 0;
    margin-top: auto;
  }

  .sb-avatar {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    background: linear-gradient(135deg, #7c3aed, #4f46e5);
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 14px;
    color: #fff;
    flex-shrink: 0;
  }

  .sb-profile-info {
    overflow: hidden;
    transition: opacity .2s, width .2s;
  }

  .sidebar.collapsed .sb-profile-info {
    opacity: 0;
    width: 0;
  }

  .sb-profile-name {
    font-size: 12px;
    font-weight: 700;
    color: #e2e8f0;
    white-space: nowrap;
  }

  .sb-profile-role {
    font-size: 10px;
    color: #64748b;
    white-space: nowrap;
  }

  /* ─── Main content offset ─── */
  .main-content {
    margin-left: var(--sb-width, 236px);
    min-height: 100vh;
    transition: margin-left .28s cubic-bezier(.4, 0, .2, 1), background var(--transition, .26s);
    background: var(--adm-bg, #f0f4ff);
  }

  .sidebar.collapsed~.main-content,
  body.sb-collapsed .main-content {
    margin-left: var(--sb-collapsed, 68px);
  }

  /* ─── Mobile overlay ─── */
  .sidebar-overlay {
    position: fixed;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.6);
    backdrop-filter: blur(4px);
    -webkit-backdrop-filter: blur(4px);
    z-index: 1035;
    opacity: 0;
    visibility: hidden;
    transition: opacity .25s ease, visibility .25s ease;
  }
  .sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
  }

  @media(max-width:991px) {
    .sidebar {
      transform: translateX(-100%);
      width: 250px !important;
      left: 0 !important;
      transition: transform .28s cubic-bezier(.4,0,.2,1);
    }

    .sidebar.mobile-open {
      transform: translateX(0) !important;
    }

    .sb-toggle {
      display: none !important;
    }

    .main-content {
      margin-left: 0 !important;
    }
  }
</style>

<div class="d-flex">

  <!-- SIDEBAR -->
  <div class="sidebar" id="adminSidebar">

    <!-- Toggle button -->
    <button class="sb-toggle" id="sbToggle" onclick="toggleSidebar()" title="Toggle sidebar">
      <i class="fas fa-chevron-left" id="sbToggleIcon"></i>
    </button>

    <!-- Brand -->
    <div class="sb-brand">
      <div class="sb-logo">Q</div>
      <span class="sb-name">Quigly Admin</span>
    </div>

    <!-- Nav -->
    <ul class="sb-nav mt-2">

      <!-- MAIN -->
      <li class="sb-label">Main</li>

      <li>
        <a href="<?= BASE_URL ?>index.php" class="sb-link <?= ($current == 'index.php') ? 'active' : '' ?>">
          <i class="fas fa-gauge-high"></i>
          <span class="sb-text">Dashboard</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>Pages/user_list.php"
          class="sb-link <?= ($current == 'user_list.php') ? 'active' : '' ?>">
          <i class="fas fa-users"></i>
          <span class="sb-text">Customers</span>
        </a>
      </li>

      <div class="sb-divider"></div>

      <!-- CATALOGUE -->
      <li class="sb-label">Catalogue</li>

      <li>
        <a href="<?= BASE_URL ?>Pages/add_category.php"
          class="sb-link <?= ($current == 'add_category.php') ? 'active' : '' ?>">
          <i class="fas fa-layer-group"></i>
          <span class="sb-text">Add Category</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>Pages/category_list.php"
          class="sb-link <?= ($current == 'category_list.php') ? 'active' : '' ?>">
          <i class="fas fa-list"></i>
          <span class="sb-text">Category List</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>Pages/add_subcategory.php"
          class="sb-link <?= ($current == 'add_subcategory.php') ? 'active' : '' ?>">
          <i class="fas fa-sitemap"></i>
          <span class="sb-text">Add Subcategory</span>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>Pages/subcategory_list.php"
          class="sb-link <?= ($current == 'subcategory_list.php') ? 'active' : '' ?>">
          <i class="fas fa-stream"></i>
          <span class="sb-text">Subcategory List</span>
        </a>
      </li>

      <div class="sb-divider"></div>

      <!-- PRODUCTS -->
      <li class="sb-label">Products</li>

      <li>
        <a href="<?= BASE_URL ?>Pages/add_product.php"
          class="sb-link <?= ($current == 'add_product.php') ? 'active' : '' ?>">
          <i class="fas fa-plus-circle"></i>
          <span class="sb-text">Add Product</span>
        </a>
      </li>
      <!-- HOMEPAGE MANAGEMENT -->
      <li class="sb-label">Homepage</li>

      <li>
        <a href="<?= BASE_URL ?>Pages/homepage_media.php"
          class="sb-link <?= ($current == 'homepage_media.php') ? 'active' : '' ?>">
          <i class="fas fa-house"></i>
          <span class="sb-text">Homepage Media</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>Pages/site_assets.php"
          class="sb-link <?= ($current == 'site_assets.php') ? 'active' : '' ?>">
          <i class="fas fa-images"></i>
          <span class="sb-text">Banners &amp; Brands</span>
        </a>
      </li>
      <li>
        <a href="<?= BASE_URL ?>Pages/product_list.php"
          class="sb-link <?= ($current == 'product_list.php') ? 'active' : '' ?>">
          <i class="fas fa-boxes-stacked"></i>
          <span class="sb-text">Product List</span>
        </a>
      </li>

      <div class="sb-divider"></div>

      <!-- ACTIVITY -->
      <li class="sb-label">Activity</li>

      <li>
        <a href="<?= BASE_URL ?>Pages/orders_list.php"
          class="sb-link <?= ($current == 'orders_list.php') ? 'active' : '' ?>">
          <i class="fas fa-shopping-bag"></i>
          <span class="sb-text">Orders</span>
          <?php if ($pendingOrderCount > 0): ?>
            <span class="sb-badge"><?= $pendingOrderCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <?php
      // Return requests pending badge
      $pendingReturnCount = 0;
      // Auto-create return_requests table if missing
      mysqli_query($conn, "CREATE TABLE IF NOT EXISTS return_requests (
          id INT AUTO_INCREMENT PRIMARY KEY,
          order_id INT NOT NULL,
          order_item_id INT DEFAULT NULL,
          user_id INT NOT NULL,
          product_id INT DEFAULT NULL,
          request_type VARCHAR(20) NOT NULL DEFAULT 'return',
          preferred_resolution VARCHAR(30) NOT NULL DEFAULT 'full_refund',
          reason VARCHAR(100) NOT NULL,
          details TEXT,
          pickup_address TEXT,
          admin_note TEXT,
          status VARCHAR(30) NOT NULL DEFAULT 'pending',
          created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
          updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
      $rrq = mysqli_query($conn, "SELECT COUNT(*) AS c FROM return_requests WHERE status='pending'");
      if ($rrq) $pendingReturnCount = (int)(mysqli_fetch_assoc($rrq)['c'] ?? 0);
      ?>
      <li>
        <a href="<?= BASE_URL ?>Pages/return_requests.php"
          class="sb-link <?= ($current == 'return_requests.php') ? 'active' : '' ?>">
          <i class="fas fa-rotate-left"></i>
          <span class="sb-text">Returns & Refunds</span>
          <?php if ($pendingReturnCount > 0): ?>
            <span class="sb-badge"><?= $pendingReturnCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <li>
        <a href="<?= BASE_URL ?>Pages/reviews_list.php"
          class="sb-link <?= ($current == 'reviews_list.php') ? 'active' : '' ?>">
          <i class="fas fa-star"></i>
          <span class="sb-text">Reviews</span>
          <?php if ($pendingReviewCount > 0): ?>
            <span class="sb-badge"><?= $pendingReviewCount ?></span>
          <?php endif; ?>
        </a>
      </li>

      <div class="sb-divider"></div>

      <!-- OTHER -->
      <li class="sb-label">Other</li>
      <li>
        <a href="<?= BASE_URL ?>Pages/support_list.php"
          class="sb-link <?= ($current == 'support_list.php') ? 'active' : '' ?>">
          <i class="fas fa-headset"></i>
          <span class="sb-text">Support Requests</span>
          <?php if ($pendingSupportCount > 0): ?>
            <span class="sb-badge"><?= $pendingSupportCount ?></span>
          <?php endif; ?>
        </a>
      </li>
      <li>
        <a href="/Quigly/index.php" class="sb-link" target="_blank">
          <i class="fas fa-store"></i>
          <span class="sb-text">Storefront</span>
        </a>
      </li>

      <li>
        <a href="/Quigly/logout.php" class="sb-link" style="color:#f87171;">
          <i class="fas fa-arrow-right-from-bracket"></i>
          <span class="sb-text">Logout</span>
        </a>
      </li>

    </ul>

    <!-- Profile -->
    <div class="sb-profile">
      <div class="sb-avatar">A</div>
      <div class="sb-profile-info">
        <div class="sb-profile-name">Administrator</div>
        <div class="sb-profile-role">Super Admin</div>
      </div>
    </div>

  </div>

  <!-- SIDEBAR MOBILE OVERLAY BACKDROP -->
  <div class="sidebar-overlay" id="sbOverlay" onclick="toggleMobileSidebar()"></div>

  <!-- MAIN CONTENT WRAPPER -->
  <div class="main-content w-100" id="mainContent">

    <script>
      (function () {
        const sb = document.getElementById('adminSidebar');
        const icon = document.getElementById('sbToggleIcon');
        const saved = localStorage.getItem('sbCollapsed');
        if (saved === '1' && sb && icon) { sb.classList.add('collapsed'); icon.className = 'fas fa-chevron-right'; }
      })();

      function toggleSidebar() {
        const sb = document.getElementById('adminSidebar');
        const icon = document.getElementById('sbToggleIcon');
        if (!sb) return;
        sb.classList.toggle('collapsed');
        const isCollapsed = sb.classList.contains('collapsed');
        localStorage.setItem('sbCollapsed', isCollapsed ? '1' : '0');
        if (icon) icon.className = isCollapsed ? 'fas fa-chevron-right' : 'fas fa-chevron-left';
      }

      function toggleMobileSidebar() {
        const sb = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sbOverlay');
        if (sb) sb.classList.toggle('mobile-open');
        if (overlay) overlay.classList.toggle('active');
      }
    </script>