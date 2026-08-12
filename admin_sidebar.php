<?php
$current_page = basename($_SERVER['PHP_SELF']);
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$admin_initial = strtoupper(substr(trim($admin_username), 0, 1) ?: 'A');
?>

<aside class="admin-sidebar">
    <div>
        <div class="sidebar-logo">
            <div class="logo-icon">
                <i class="bi bi-shop"></i>
            </div>
            <div class="logo-text">
                <h4>Campus</h4>
                <span>Exchange</span>
            </div>
        </div>

        <ul class="sidebar-menu">
            <li>
                <a href="dashboard.php" class="<?= $current_page === 'dashboard.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="users.php" class="<?= $current_page === 'users.php' ? 'active' : '' ?>">
                    <i class="bi bi-people"></i>
                    Users
                </a>
            </li>
            <li>
                <a href="listings.php" class="<?= $current_page === 'listings.php' ? 'active' : '' ?>">
                    <i class="bi bi-box-seam"></i>
                    Listings
                </a>
            </li>
            <li>
                <a href="categories.php" class="<?= $current_page === 'categories.php' ? 'active' : '' ?>">
                    <i class="bi bi-grid-3x3-gap"></i>
                    Categories
                </a>
            </li>
            <li>
                <a href="meetup_locations.php" class="<?= $current_page === 'meetup_locations.php' ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt"></i>
                    Meetup Locations
                </a>
            </li>
            <li>
                <a href="messages.php" class="<?= $current_page === 'messages.php' ? 'active' : '' ?>" style="display:flex;align-items:center;justify-content:space-between">
                    <span style="display:flex;align-items:center;gap:12px">
                        <i class="bi bi-chat-dots"></i>
                        Messages
                    </span>
                    <?php
                        $_unread_count = 0;
                        if (function_exists('admin_count')) {
                            $_unread_count = admin_count('SELECT COUNT(*) AS total FROM messages WHERE is_read = 0');
                        }
                        if ($_unread_count > 0): ?>
                        <span style="background:#ef4444;color:#fff;border-radius:999px;font-size:.68rem;font-weight:800;padding:2px 7px;line-height:1.4">
                            <?= $_unread_count ?>
                        </span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="reports.php" class="<?= $current_page === 'reports.php' ? 'active' : '' ?>">
                    <i class="bi bi-flag"></i>
                    Reports
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?= $current_page === 'settings.php' ? 'active' : '' ?>">
                    <i class="bi bi-gear"></i>
                    Settings
                </a>
            </li>
        </ul>
    </div>

    <div class="sidebar-bottom">
        <div class="admin-profile">
            <div class="admin-avatar"><?= htmlspecialchars($admin_initial) ?></div>
            <div>
                <h6><?= htmlspecialchars($admin_username) ?></h6>
                <small>Super Admin</small>
            </div>
        </div>

        <a href="../common/logout.php" class="logout-btn">
            <i class="bi bi-box-arrow-right"></i>
            Logout
        </a>
    </div>
</aside>
