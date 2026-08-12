<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
if (!isset($_SESSION['email']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /Quigly/login.php?error=Unauthorized+Access");
    exit;
}

include_once(__DIR__ . "/config.php");
include_once(__DIR__ . "/db.php");

// Notifications (latest 8)
$notificationsQuery = mysqli_query($conn,
    "SELECT * FROM notifications ORDER BY id DESC LIMIT 8"
);
$unreadCount = (int)(mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS total FROM notifications WHERE is_read = 0")
)['total'] ?? 0);

// Pending orders count for header awareness
$pendingOrders = (int)(mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT COUNT(*) AS c FROM orders WHERE LOWER(status) IN ('pending','processing')")
)['c'] ?? 0);
?>
<!DOCTYPE html>
<html lang="en" id="adminHtml">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Panel — Quigly</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300;0,14..32,400;0,14..32,500;0,14..32,600;0,14..32,700;0,14..32,800;0,14..32,900&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

<script>
/* ── Apply theme BEFORE page renders (prevents flash) ── */
(function(){
  var t = localStorage.getItem('quiglyAdminTheme') || 'dark';
  document.documentElement.setAttribute('data-adm-theme', t);
})();
</script>

<style>
/* ══════════════════════════════════════════════════
   QUIGLY ADMIN — PREMIUM THEME  v3
   data-adm-theme="dark" | "light"
══════════════════════════════════════════════════ */

/* ─── Light Mode (default) ─── */
:root {
  --adm-bg:          #f0f4ff;
  --adm-bg2:         #e8edf8;
  --adm-surface:     #ffffff;
  --adm-surface2:    #f8faff;
  --adm-border:      #e2e8f0;
  --adm-text:        #0f172a;
  --adm-text-muted:  #64748b;
  --adm-text-faint:  #94a3b8;
  --adm-accent:      #7c3aed;
  --adm-accent2:     #4f46e5;
  --adm-accent-bg:   rgba(124,58,237,.08);
  --adm-accent-glow: rgba(124,58,237,.18);
  --adm-danger:      #ef4444;
  --adm-success:     #22c55e;
  --adm-warn:        #f59e0b;
  --adm-shadow:      0 2px 16px rgba(15,23,42,.06);
  --adm-shadow-md:   0 8px 32px rgba(15,23,42,.10);
  --adm-shadow-lg:   0 20px 60px rgba(15,23,42,.14);
  --adm-radius:      16px;
  --sb-width:        236px;
  --sb-collapsed:    68px;
  --nav-height:      66px;
  --transition:      .26s cubic-bezier(.4,0,.2,1);
}

/* ─── Dark Mode ─── */
[data-adm-theme="dark"] {
  --adm-bg:          #080c14;
  --adm-bg2:         #060a10;
  --adm-surface:     #111827;
  --adm-surface2:    #1a2236;
  --adm-border:      rgba(255,255,255,.07);
  --adm-text:        #e2e8f0;
  --adm-text-muted:  #94a3b8;
  --adm-text-faint:  #475569;
  --adm-accent-bg:   rgba(124,58,237,.14);
  --adm-accent-glow: rgba(124,58,237,.30);
  --adm-shadow:      0 2px 16px rgba(0,0,0,.40);
  --adm-shadow-md:   0 8px 32px rgba(0,0,0,.50);
  --adm-shadow-lg:   0 20px 60px rgba(0,0,0,.65);
}

/* ─── Base ─── */
*, *::before, *::after { box-sizing: border-box; }

html, body {
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
  overflow-x: hidden;
  background: var(--adm-bg);
  color: var(--adm-text);
  transition: background var(--transition), color var(--transition);
  margin: 0; padding: 0;
}

/* ══ NAVBAR ══ */
.admin-navbar {
  height: var(--nav-height);
  background: var(--adm-surface);
  border-bottom: 1px solid var(--adm-border);
  position: sticky; top: 0; z-index: 1030;
  display: flex; align-items: center; justify-content: space-between;
  padding: 0 24px 0 32px;
  margin-left: var(--sb-width);
  box-shadow: var(--adm-shadow);
  backdrop-filter: blur(12px);
  -webkit-backdrop-filter: blur(12px);
  transition: margin-left var(--transition), background var(--transition), border-color var(--transition);
}
body.sb-collapsed .admin-navbar { margin-left: var(--sb-collapsed); }

/* Search */
.adm-search {
  width: 300px; height: 40px;
  background: var(--adm-surface2);
  border: 1px solid var(--adm-border);
  border-radius: 12px;
  display: flex; align-items: center; gap: 10px;
  padding: 0 14px;
  transition: var(--transition);
}
.adm-search:focus-within {
  background: var(--adm-surface);
  border-color: var(--adm-accent);
  box-shadow: 0 0 0 3px var(--adm-accent-glow);
}
.adm-search i { color: var(--adm-text-faint); font-size: 13px; flex-shrink: 0; }
.adm-search input {
  border: none; outline: none; background: transparent;
  width: 100%; font-size: 13px; font-weight: 500;
  color: var(--adm-text); font-family: inherit;
}
.adm-search input::placeholder { color: var(--adm-text-faint); }

/* Right side */
.admin-right { display: flex; align-items: center; gap: 10px; }

/* Icon button base */
.adm-icon-btn {
  width: 40px; height: 40px; border-radius: 12px;
  background: var(--adm-surface2); border: 1px solid var(--adm-border);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; transition: var(--transition);
  font-size: 16px; color: var(--adm-text-muted);
  position: relative; flex-shrink: 0;
}
.adm-icon-btn:hover {
  background: var(--adm-accent-bg);
  border-color: var(--adm-accent);
  color: var(--adm-accent);
}

/* Theme toggle specific animation */
.theme-toggle-btn { transition: var(--transition), transform .35s; }
.theme-toggle-btn:hover { transform: rotate(20deg); }

/* Sun / Moon icons */
.t-sun, .t-moon { position: absolute; transition: opacity .25s, transform .25s; }
[data-adm-theme="dark"]  .t-sun  { opacity: 1; transform: translateY(0);    }
[data-adm-theme="dark"]  .t-moon { opacity: 0; transform: translateY(-16px);}
[data-adm-theme="light"] .t-sun  { opacity: 0; transform: translateY(16px); }
[data-adm-theme="light"] .t-moon { opacity: 1; transform: translateY(0);    }

/* Notification btn */
.notif-btn.has-unread { background: #fef3c7; border-color: #f59e0b; color: #d97706; }

@keyframes bellShake {
  0%,100%{transform:rotate(0);}
  10%{transform:rotate(-14deg);}
  20%{transform:rotate(12deg);}
  30%{transform:rotate(-10deg);}
  40%{transform:rotate(8deg);}
  50%{transform:rotate(-6deg);}
  60%{transform:rotate(4deg);}
  70%{transform:rotate(-2deg);}
}
.notif-btn.shake i { animation: bellShake .5s ease; }

.notif-count {
  position: absolute; top: -5px; right: -5px;
  min-width: 18px; height: 18px; padding: 0 4px;
  border-radius: 999px;
  background: linear-gradient(135deg, #ef4444, #dc2626);
  color: #fff; font-size: 10px; font-weight: 800;
  display: flex; align-items: center; justify-content: center;
  border: 2px solid var(--adm-surface);
  box-shadow: 0 3px 8px rgba(239,68,68,.35);
  animation: notifPop .3s ease;
}
@keyframes notifPop { 0%{transform:scale(0);}80%{transform:scale(1.2);}100%{transform:scale(1);} }

/* Notification dropdown */
.notif-wrap { position: relative; }
.notif-dropdown {
  position: absolute; top: calc(100% + 12px); right: 0;
  width: 380px;
  background: var(--adm-surface);
  border: 1px solid var(--adm-border);
  border-radius: 20px;
  box-shadow: var(--adm-shadow-lg);
  overflow: hidden;
  opacity: 0; visibility: hidden;
  transform: translateY(10px) scale(.97);
  transition: .22s cubic-bezier(.4,0,.2,1);
  z-index: 9999;
}
.notif-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }

.notif-head {
  padding: 14px 18px;
  display: flex; align-items: center; justify-content: space-between;
  border-bottom: 1px solid var(--adm-border);
}
.notif-head h6 { margin: 0; font-size: 14px; font-weight: 800; color: var(--adm-text); }
.mark-read-btn {
  font-size: 11px; font-weight: 700; color: var(--adm-accent);
  background: var(--adm-accent-bg);
  border: none; cursor: pointer;
  padding: 4px 10px; border-radius: 8px;
  transition: var(--transition);
}
.mark-read-btn:hover { background: var(--adm-accent-glow); }

.notif-list { max-height: 340px; overflow-y: auto; scrollbar-width: thin; scrollbar-color: var(--adm-border) transparent; }

.notif-item {
  display: flex; align-items: flex-start; gap: 12px;
  padding: 12px 16px;
  border-bottom: 1px solid var(--adm-border);
  transition: .15s; position: relative;
}
.notif-item:hover { background: var(--adm-surface2); }
.notif-item.unread { background: var(--adm-accent-bg); }
.notif-item.unread::before {
  content: ''; position: absolute; left: 0; top: 0; bottom: 0;
  width: 3px; background: var(--adm-accent); border-radius: 0 2px 2px 0;
}
.notif-icon { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 15px; }
.notif-icon.order   { background: #dbeafe; color: #2563eb; }
.notif-icon.review  { background: #fef3c7; color: #d97706; }
.notif-icon.user    { background: #dcfce7; color: #16a34a; }
.notif-icon.default { background: var(--adm-accent-bg); color: var(--adm-accent); }
.notif-msg  { font-size: 13px; font-weight: 600; color: var(--adm-text); margin-bottom: 3px; line-height: 1.4; }
.notif-time { font-size: 11px; color: var(--adm-text-faint); font-weight: 500; }
.notif-empty { padding: 32px; text-align: center; color: var(--adm-text-faint); }
.notif-empty i { font-size: 28px; margin-bottom: 8px; display: block; }
.notif-empty span { font-size: 13px; font-weight: 600; }
.notif-footer { padding: 10px 16px; border-top: 1px solid var(--adm-border); text-align: center; }
.notif-footer a { font-size: 12px; font-weight: 700; color: var(--adm-accent); text-decoration: none; }
.notif-footer a:hover { text-decoration: underline; }

/* Divider */
.navbar-vr { width: 1px; height: 24px; background: var(--adm-border); margin: 0 4px; }

/* Profile chip */
.admin-profile {
  display: flex; align-items: center; gap: 10px;
  padding: 5px 12px 5px 5px; border-radius: 14px;
  background: var(--adm-surface2); border: 1px solid var(--adm-border);
  cursor: pointer; transition: var(--transition);
}
.admin-profile:hover { border-color: var(--adm-accent); background: var(--adm-accent-bg); }
.admin-avatar {
  width: 32px; height: 32px; border-radius: 8px;
  background: linear-gradient(135deg, #7c3aed, #4f46e5);
  color: #fff; font-weight: 800; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
}
.admin-name { font-size: 13px; font-weight: 700; color: var(--adm-text); margin: 0; line-height: 1.2; }
.admin-role { font-size: 10px; color: var(--adm-text-muted); margin: 0; }

/* Profile dropdown menu */
.profile-dropdown {
  position: absolute; top: calc(100% + 12px); right: 0;
  width: 240px;
  background: var(--adm-surface);
  border: 1px solid var(--adm-border);
  border-radius: 16px;
  box-shadow: var(--adm-shadow-lg);
  overflow: hidden;
  opacity: 0; visibility: hidden;
  transform: translateY(10px) scale(.97);
  transition: .22s cubic-bezier(.4,0,.2,1);
  z-index: 9999;
}
.profile-dropdown.open { opacity: 1; visibility: visible; transform: translateY(0) scale(1); }
.profile-dropdown-head {
  padding: 14px 16px;
  background: var(--adm-surface2);
  border-bottom: 1px solid var(--adm-border);
}
.profile-dropdown-body { padding: 6px; }
.profile-dd-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 12px; border-radius: 10px;
  font-size: 13px; font-weight: 600;
  color: var(--adm-text); text-decoration: none;
  transition: var(--transition);
}
.profile-dd-item:hover { background: var(--adm-surface2); color: var(--adm-accent); }
.profile-dd-item.danger { color: #ef4444; }
.profile-dd-item.danger:hover { background: rgba(239,68,68,.08); color: #dc2626; }

/* Mobile hamburger */
.mobile-nav-btn {
  width: 38px; height: 38px; border-radius: 10px;
  background: var(--adm-surface2); border: 1px solid var(--adm-border);
  color: var(--adm-text-muted);
  display: none; align-items: center; justify-content: center;
  font-size: 16px; cursor: pointer; transition: var(--transition);
}
.mobile-nav-btn:hover { background: var(--adm-accent-bg); color: var(--adm-accent); border-color: var(--adm-accent); }

/* Logout btn */
.adm-logout {
  display: inline-flex; align-items: center; gap: 6px;
  padding: 6px 14px; border-radius: 10px;
  background: rgba(239,68,68,.08);
  color: #ef4444; border: 1px solid rgba(239,68,68,.2);
  font-size: 12px; font-weight: 700; text-decoration: none;
  transition: var(--transition);
}
.adm-logout:hover { background: rgba(239,68,68,.15); border-color: rgba(239,68,68,.4); color: #dc2626; }

/* ══ MAIN CONTENT ══ */
.main-content {
  margin-left: var(--sb-width);
  min-height: 100vh;
  background: var(--adm-bg);
  transition: margin-left var(--transition), background var(--transition);
}
body.sb-collapsed .main-content { margin-left: var(--sb-collapsed); }

/* ══ ADMIN FOOTER ══ */
.admin-footer {
  margin-left: var(--sb-width);
  background: var(--adm-surface);
  border-top: 1px solid var(--adm-border);
  padding: 16px 28px; text-align: center;
  color: var(--adm-text-faint); font-size: 12px;
  transition: margin-left var(--transition), background var(--transition);
}
body.sb-collapsed .admin-footer { margin-left: var(--sb-collapsed); }

/* ══ GLOBAL DARK-MODE FORM / CARD OVERRIDES ══ */
[data-adm-theme="dark"] .form-control,
[data-adm-theme="dark"] .form-select,
[data-adm-theme="dark"] textarea {
  background: var(--adm-surface2) !important;
  border-color: var(--adm-border) !important;
  color: var(--adm-text) !important;
}
[data-adm-theme="dark"] .form-control:focus,
[data-adm-theme="dark"] .form-select:focus,
[data-adm-theme="dark"] textarea:focus {
  background: var(--adm-surface) !important;
  border-color: var(--adm-accent) !important;
  box-shadow: 0 0 0 3px var(--adm-accent-glow) !important;
  color: var(--adm-text) !important;
}
[data-adm-theme="dark"] .form-control::placeholder,
[data-adm-theme="dark"] textarea::placeholder { color: var(--adm-text-faint) !important; }
[data-adm-theme="dark"] .form-label { color: var(--adm-text) !important; }
[data-adm-theme="dark"] .card { background: var(--adm-surface) !important; border-color: var(--adm-border) !important; color: var(--adm-text) !important; }
[data-adm-theme="dark"] .card-header { background: var(--adm-surface2) !important; border-color: var(--adm-border) !important; }
[data-adm-theme="dark"] .modal-content { background: var(--adm-surface) !important; border-color: var(--adm-border) !important; color: var(--adm-text) !important; }
[data-adm-theme="dark"] .modal-header, [data-adm-theme="dark"] .modal-footer { border-color: var(--adm-border) !important; }
[data-adm-theme="dark"] .table { color: var(--adm-text) !important; }
[data-adm-theme="dark"] .table > :not(caption) > * > * { background-color: transparent !important; border-color: var(--adm-border) !important; }
[data-adm-theme="dark"] .dropdown-menu { background: var(--adm-surface) !important; border-color: var(--adm-border) !important; }
[data-adm-theme="dark"] .dropdown-item { color: var(--adm-text) !important; }
[data-adm-theme="dark"] .dropdown-item:hover { background: var(--adm-surface2) !important; }
[data-adm-theme="dark"] .input-group-text { background: var(--adm-surface2) !important; border-color: var(--adm-border) !important; color: var(--adm-text-muted) !important; }
[data-adm-theme="dark"] .btn-light { background: var(--adm-surface2) !important; color: var(--adm-text) !important; border-color: var(--adm-border) !important; }
[data-adm-theme="dark"] .alert { border-color: var(--adm-border); }
[data-adm-theme="dark"] .pagination .page-link { background: var(--adm-surface); border-color: var(--adm-border); color: var(--adm-text); }
[data-adm-theme="dark"] .pagination .page-item.active .page-link { background: var(--adm-accent); border-color: var(--adm-accent); }

/* ══ TABLE UTILITY ══ */
.table-responsive { width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch; }
.table-responsive::-webkit-scrollbar { height:6px; }
.table-responsive::-webkit-scrollbar-thumb { background: var(--adm-border); border-radius:10px; }

/* Dashboard stat card aware of theme */
.stat-card {
  background: var(--adm-surface) !important;
  border: 1px solid var(--adm-border) !important;
  transition: background var(--transition), border-color var(--transition), transform .2s, box-shadow .2s !important;
}
.stat-card:hover { transform: translateY(-3px) !important; box-shadow: var(--adm-shadow-md) !important; }
.stat-num  { color: var(--adm-text) !important; }
.stat-lbl  { color: var(--adm-text-muted) !important; }

/* Data card */
.data-card { background: var(--adm-surface) !important; border: 1px solid var(--adm-border) !important; }
.data-card-head { border-bottom: 1px solid var(--adm-border) !important; }
.data-card-head h6 { color: var(--adm-text) !important; }
.dt thead { background: var(--adm-surface2) !important; }
.dt thead th { color: var(--adm-text-muted) !important; border-bottom: 1px solid var(--adm-border) !important; }
.dt tbody td { color: var(--adm-text) !important; border-bottom: 1px solid var(--adm-border) !important; }
.dt tbody tr:hover { background: var(--adm-surface2) !important; }
.sec-title { color: var(--adm-text) !important; }

/* Quick action buttons */
.qa-btn {
  background: var(--adm-surface) !important;
  border: 1px solid var(--adm-border) !important;
  color: var(--adm-text-muted) !important;
  transition: var(--transition) !important;
}
.qa-btn:hover { border-color: var(--adm-accent) !important; color: var(--adm-accent) !important; background: var(--adm-accent-bg) !important; }

/* ══ RESPONSIVE ══ */
@media(max-width:991px) {
  .admin-navbar  { margin-left: 0 !important; padding: 0 16px; }
  .admin-footer  { margin-left: 0 !important; }
  .main-content  { margin-left: 0 !important; }
  .adm-search    { width: 220px; }
  .mobile-nav-btn { display: flex !important; }
}
@media(max-width:768px) {
  .notif-dropdown { width: calc(100vw - 32px) !important; right: -8px !important; }
}
@media(max-width:576px) {
  .admin-navbar { height: auto !important; padding: 10px 14px !important; gap: 10px !important; flex-wrap: wrap !important; }
  .adm-search   { width: 100% !important; order: 3 !important; }
  .admin-right  { order: 2; }
  .mobile-nav-btn { order: 1; }
  .admin-name, .admin-role, .navbar-vr { display: none !important; }
  .admin-profile { padding: 4px 6px !important; }
  .adm-logout span { display: none; }
}
</style>

<nav class="admin-navbar" id="adminNavbar">

  <div class="d-flex align-items-center gap-2">
    <button type="button" class="mobile-nav-btn" onclick="toggleMobileSidebar()" title="Menu">
      <i class="fas fa-bars"></i>
    </button>

    <div class="adm-search">
      <i class="fa-solid fa-magnifying-glass"></i>
      <input type="text" placeholder="Search products, orders, customers…" id="adminSearch">
    </div>
  </div>

  <div class="admin-right">

    <!-- Dark / Light Toggle -->
    <button class="adm-icon-btn theme-toggle-btn" id="themeToggleBtn" onclick="toggleAdminTheme()" title="Toggle Theme" aria-label="Toggle dark/light mode">
      <i class="fa-solid fa-sun t-sun"></i>
      <i class="fa-solid fa-moon t-moon"></i>
    </button>

    <!-- Notifications -->
    <div class="notif-wrap" id="notifWrap">
      <button class="adm-icon-btn notif-btn <?= $unreadCount > 0 ? 'has-unread' : '' ?>"
              id="notifBtn" onclick="toggleNotif()" title="Notifications" aria-label="Notifications">
        <i class="fa-<?= $unreadCount > 0 ? 'solid' : 'regular' ?> fa-bell"></i>
        <?php if ($unreadCount > 0): ?>
          <span class="notif-count" id="notifCount"><?= $unreadCount ?></span>
        <?php endif; ?>
      </button>

      <div class="notif-dropdown" id="notifDropdown">
        <div class="notif-head">
          <h6><i class="fas fa-bell" style="color:var(--adm-accent);margin-right:7px;"></i>Notifications</h6>
          <button class="mark-read-btn" onclick="markAllRead()">Mark all read</button>
        </div>

        <div class="notif-list" id="notifList">
          <?php if ($notificationsQuery && mysqli_num_rows($notificationsQuery) > 0):
            while ($n = mysqli_fetch_assoc($notificationsQuery)):
              $nType    = $n['type'] ?? 'default';
              $nUnread  = !$n['is_read'];
              $nIconMap = ['order'=>'fa-box','review'=>'fa-star','user'=>'fa-user'];
              $nIcon    = $nIconMap[$nType] ?? 'fa-bell';
          ?>
          <div class="notif-item <?= $nUnread ? 'unread' : '' ?>">
            <div class="notif-icon <?= htmlspecialchars($nType) ?>">
              <i class="fas <?= $nIcon ?>"></i>
            </div>
            <div>
              <div class="notif-msg"><?= htmlspecialchars($n['message']) ?></div>
              <div class="notif-time"><?= date('d M Y, h:i A', strtotime($n['created_at'])) ?></div>
            </div>
          </div>
          <?php endwhile; else: ?>
          <div class="notif-empty">
            <i class="far fa-bell-slash"></i>
            <span>No notifications yet</span>
          </div>
          <?php endif; ?>
        </div>

        <div class="notif-footer">
          <a href="<?= BASE_URL ?>Pages/orders_list.php">View all orders &rarr;</a>
        </div>
      </div>
    </div>

    <div class="navbar-vr"></div>

    <!-- Profile Dropdown -->
    <div class="profile-wrap" id="profileWrap" style="position:relative;">
      <div class="admin-profile" onclick="toggleProfileDropdown()" id="profileBtn" title="Account Menu">
        <div class="admin-avatar">A</div>
        <div>
          <p class="admin-name"><?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></p>
          <p class="admin-role">Administrator <i class="fas fa-chevron-down ms-1" style="font-size:9px;"></i></p>
        </div>
      </div>

      <div class="profile-dropdown" id="profileDropdown">
        <div class="profile-dropdown-head">
          <div class="d-flex align-items-center gap-2">
            <div class="admin-avatar" style="width:38px;height:38px;font-size:16px;">A</div>
            <div>
              <div style="font-weight:700;font-size:13px;color:var(--adm-text);"><?= htmlspecialchars($_SESSION['email'] ?? 'Admin') ?></div>
              <div style="font-size:11px;color:var(--adm-text-muted);">Super Administrator</div>
            </div>
          </div>
        </div>
        <div class="profile-dropdown-body">
          <a href="<?= BASE_URL ?>Pages/user_list.php" class="profile-dd-item">
            <i class="fas fa-users"></i> Manage Customers
          </a>
          <a href="<?= BASE_URL ?>Pages/homepage_media.php" class="profile-dd-item">
            <i class="fas fa-sliders"></i> Site Settings
          </a>
          <a href="/Quigly/index.php" target="_blank" class="profile-dd-item">
            <i class="fas fa-store"></i> View Storefront
          </a>
          <div style="height:1px;background:var(--adm-border);margin:6px 0;"></div>
          <a href="/Quigly/logout.php" class="profile-dd-item danger">
            <i class="fas fa-sign-out-alt"></i> Logout
          </a>
        </div>
      </div>
    </div>

    <a href="/Quigly/logout.php" class="adm-logout" title="Logout">
      <i class="fas fa-sign-out-alt"></i>
      <span>Logout</span>
    </a>

  </div>
</nav>

<script>
/* ══════════════════════════════════════
   ADMIN THEME SYSTEM
══════════════════════════════════════ */
function toggleAdminTheme() {
  var html = document.getElementById('adminHtml');
  var cur  = html.getAttribute('data-adm-theme') || 'dark';
  var next = cur === 'dark' ? 'light' : 'dark';
  html.setAttribute('data-adm-theme', next);
  localStorage.setItem('quiglyAdminTheme', next);
}

/* ── Toggle notification dropdown ── */
function toggleNotif() {
  var dd  = document.getElementById('notifDropdown');
  var btn = document.getElementById('notifBtn');
  var open = dd.classList.toggle('open');
  if (open) {
    btn.classList.add('shake');
    setTimeout(function(){ btn.classList.remove('shake'); }, 600);
    markAllRead();
  }
}
document.addEventListener('click', function(e) {
  var wrap = document.getElementById('notifWrap');
  if (wrap && !wrap.contains(e.target)) {
    var dd = document.getElementById('notifDropdown');
    if (dd) dd.classList.remove('open');
  }
  var pWrap = document.getElementById('profileWrap');
  if (pWrap && !pWrap.contains(e.target)) {
    var pDd = document.getElementById('profileDropdown');
    if (pDd) pDd.classList.remove('open');
  }
});

/* ── Toggle profile dropdown ── */
function toggleProfileDropdown() {
  var pDd = document.getElementById('profileDropdown');
  if (pDd) pDd.classList.toggle('open');
}

/* ── Mark all read ── */
function markAllRead() {
  fetch('/Quigly/admin/actions/read_notifications.php')
    .then(function() {
      var badge = document.getElementById('notifCount');
      if (badge) badge.remove();
      document.querySelectorAll('.notif-item.unread').forEach(function(el){ el.classList.remove('unread'); });
      var btn = document.getElementById('notifBtn');
      if (btn) {
        btn.classList.remove('has-unread');
        var icon = btn.querySelector('i');
        if (icon) icon.className = 'fa-regular fa-bell';
      }
    });
}

/* ── Live poll every 20 s ── */
var _lastUnread = <?= $unreadCount ?>;
function pollNotifications() {
  fetch('/Quigly/admin/actions/get_notifications.php')
    .then(function(r){ return r.json(); })
    .then(function(data) {
      if (!data) return;
      var count = data.unread_count || 0;
      var btn   = document.getElementById('notifBtn');
      var badge = document.getElementById('notifCount');
      if (count > 0) {
        if (!badge) {
          badge = document.createElement('span');
          badge.id = 'notifCount';
          badge.className = 'notif-count';
          btn.appendChild(badge);
        }
        badge.textContent = count;
        btn.classList.add('has-unread');
        var icon = btn.querySelector('i');
        if (icon) icon.className = 'fa-solid fa-bell';
        if (count > _lastUnread) {
          btn.classList.add('shake');
          setTimeout(function(){ btn.classList.remove('shake'); }, 600);
        }
      } else {
        if (badge) badge.remove();
        btn.classList.remove('has-unread');
        var icon = btn.querySelector('i');
        if (icon) icon.className = 'fa-regular fa-bell';
      }
      _lastUnread = count;
      if (data.notifications && Array.isArray(data.notifications)) {
        var list = document.getElementById('notifList');
        if (!list) return;
        if (!data.notifications.length) {
          list.innerHTML = '<div class="notif-empty"><i class="far fa-bell-slash"></i><span>No notifications yet</span></div>';
          return;
        }
        var iconMap = {order:'fa-box', review:'fa-star', user:'fa-user'};
        var html = '';
        data.notifications.forEach(function(n) {
          var ic  = iconMap[n.type] || 'fa-bell';
          var cls = n.is_read == 0 ? 'unread' : '';
          html += '<div class="notif-item '+cls+'">'
            + '<div class="notif-icon '+(n.type||'default')+'"><i class="fas '+ic+'"></i></div>'
            + '<div><div class="notif-msg">'+escHtml(n.message)+'</div>'
            + '<div class="notif-time">'+n.created_at+'</div></div></div>';
        });
        list.innerHTML = html;
      }
    })
    .catch(function(){});
}
function escHtml(s) {
  return String(s||'').replace(/[&<>"']/g, function(c){
    return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
  });
}
setInterval(pollNotifications, 20000);

/* ── Sidebar sync ── */
document.addEventListener('DOMContentLoaded', function() {
  var sb = document.getElementById('adminSidebar');
  if (sb && sb.classList.contains('collapsed')) {
    document.body.classList.add('sb-collapsed');
  }
});
</script>
