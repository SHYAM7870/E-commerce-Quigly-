<?php
include '../../function.php';
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/sidebar.php'; ?>

<?php
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM quigly_table");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalUsers = (int)($totalRow['total'] ?? 0);

$totalPages = max(1, (int)ceil($totalUsers / $limit));

$userQuery = mysqli_query($conn, "
    SELECT id, name, email, image, status
    FROM quigly_table
    ORDER BY id DESC
    LIMIT $limit OFFSET $offset
");
?>

<style>
    .page-wrap {
        padding: 1.5rem;
    }

    .page-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
        margin-bottom: 1.5rem;
    }

    .page-title {
        margin: 0;
        font-size: 1.9rem;
        font-weight: 800;
        color: #0f172a;
    }

    .page-subtitle {
        margin: .35rem 0 0;
        color: #64748b;
        font-size: .95rem;
    }

    .top-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: #fff;
        border: 1px solid #eef2f7;
        border-radius: 22px;
        padding: 1.2rem 1.3rem;
        box-shadow: 0 12px 30px rgba(15, 23, 42, .06);
    }

    .stat-label {
        color: #64748b;
        font-size: .9rem;
        margin-bottom: .35rem;
        font-weight: 600;
    }

    .stat-value {
        font-size: 1.6rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1.1;
    }

    .user-card {
        border: none;
        border-radius: 26px;
        overflow: hidden;
        box-shadow: 0 12px 35px rgba(15, 23, 42, .08);
        background: #fff;
    }

    .user-table thead th {
        background: linear-gradient(135deg, #dbeafe, #eef2ff);
        color: #0f172a;
        font-weight: 800;
        border: none;
        padding: 18px 16px;
        white-space: nowrap;
        font-size: .95rem;
    }

    .user-table tbody td {
        padding: 18px 16px;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .user-table tbody tr {
        transition: .25s ease;
    }

    .user-table tbody tr:hover {
        background: #f8fafc;
    }

    .user-profile {
        width: 56px;
        height: 56px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #fff;
        box-shadow: 0 6px 18px rgba(0, 0, 0, .10);
    }

    .user-name {
        font-weight: 800;
        color: #0f172a;
    }

    .user-email {
        color: #64748b;
        font-size: .95rem;
    }

    .user-id-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 44px;
        height: 34px;
        padding: 0 .8rem;
        border-radius: 999px;
        background: #f8fafc;
        color: #334155;
        font-weight: 800;
        font-size: .9rem;
    }

    .delete-btn {
        width: 42px;
        height: 42px;
        border: none;
        border-radius: 14px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #ef4444;
        color: #fff;
        transition: .25s ease;
        text-decoration: none;
        box-shadow: 0 10px 20px rgba(239, 68, 68, .18);
    }

    .delete-btn:hover {
        background: #dc2626;
        transform: translateY(-2px);
        color: #fff;
    }

    .users-pagination {
        padding: 1.4rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: center;
    }

    .users-pagination nav {
        display: flex;
        align-items: center;
        gap: .55rem;
        background: #fff;
        padding: .8rem 1rem;
        border-radius: 20px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 10px 25px rgba(15, 23, 42, .08);
        flex-wrap: wrap;
    }

    .pagination-number,
    .pagination-btn {
        width: 42px;
        height: 42px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        font-weight: 700;
        transition: .25s ease;
        border: 1px solid transparent;
    }

    .pagination-number {
        background: #f8fafc;
        color: #475569;
    }

    .pagination-number:hover {
        background: #ede9fe;
        color: #7c3aed;
    }

    .pagination-number.active {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: #fff;
        box-shadow: 0 10px 20px rgba(124, 58, 237, .25);
    }

    .pagination-btn {
        background: #f1f5f9;
        color: #334155;
    }

    .pagination-btn:hover {
        background: #7c3aed;
        color: #fff;
    }

    .pagination-btn.disabled {
        opacity: .4;
        pointer-events: none;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
        color: #64748b;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }
    .status-badge{

        display: inline-flex;

        align-items: center;

        justify-content: center;

        padding: .45rem 1rem;

        border-radius: 999px;

        font-size: .82rem;

        font-weight: 700;
    }

    .active-badge{

        background: rgba(34,197,94,.12);

        color: #16a34a;
    }

    .blocked-badge{

        background: rgba(239,68,68,.12);

        color: #dc2626;
    }

    .block-btn,
    .unblock-btn{

        width: 42px;

        height: 42px;

        border-radius: 14px;

        display: inline-flex;

        align-items: center;

        justify-content: center;

        /* background: #22c55e; */

        color: #ffffff;

        text-decoration: none;

        margin-right: .45rem;

        transition: .25s ease;

        box-shadow: 0 10px 20px rgba(34,197,94,.18);
    }


    .block-btn{

        background: #ef4444;
    }

    .block-btn:hover{

        background: #dc2626;

        transform: translateY(-2px);

        color: #fff;
    }

    .unblock-btn{
        background: #22c55e;
    }

    .unblock-btn:hover{

        background: #16a34a;

        transform: translateY(-2px);

        color: #ffffff;
    }
    .unblock-btn i{

        font-size: 15px;
    }
    @media (max-width: 992px) {
        .top-stats {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .page-wrap {
            padding: 1rem;
        }

        .top-stats {
            grid-template-columns: 1fr;
        }

        .user-table thead th,
        .user-table tbody td {
            padding: 14px 12px;
        }
    }
</style>

<div class="container-fluid page-wrap">
    <div class="page-head">
        <div>
            <h3 class="page-title">User Details</h3>
            <p class="page-subtitle">Manage all registered users from one place.</p>
        </div>

        <a href="../../register.php" class="btn btn-primary px-4 py-2 fw-semibold rounded-4">
            + Add New User
        </a>
    </div>

    <div class="top-stats">
        <div class="stat-card">
            <div class="stat-label">Total Users</div>
            <div class="stat-value"><?= (int)$totalUsers ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Current Page</div>
            <div class="stat-value"><?= (int)$page ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Pages</div>
            <div class="stat-value"><?= (int)$totalPages ?></div>
        </div>
    </div>

    <div class="card user-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle text-center mb-0 user-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Profile</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($userQuery && mysqli_num_rows($userQuery) > 0) { ?>
                        <?php while ($row = mysqli_fetch_assoc($userQuery)) { ?>
                            <tr>
                                <td>
                                    <span class="user-id-badge">
                                        <?= $offset + 1 ?>
                                    </span>
                                </td>
                                <td class="text-start">
                                    <div class="user-name">
                                        <?= htmlspecialchars($row['name'] ?? 'NA') ?>
                                    </div>
                                </td>
                                <td class="text-start">
                                    <div class="user-email">
                                        <?= htmlspecialchars($row['email'] ?? 'NA') ?>
                                    </div>
                                </td>
                                <td>
                                    <img
                                        src="../../upload/<?= !empty($row['image']) ? htmlspecialchars($row['image']) : 'default.png'; ?>"
                                        class="user-profile"
                                        alt="User Profile"
                                    >
                                </td>
                                <td>
                                    <?php if (($row['status'] ?? 'active') === 'active') { ?>
                                        <span class="status-badge active-badge">
                                            Active
                                        </span>
                                    <?php } else { ?>
                                        <span class="status-badge blocked-badge">
                                            Blocked
                                        </span>
                                    <?php } ?>
                                </td>
                                <td>
                                <?php if (($row['status'] ?? 'active') === 'active') { ?>
                                    <a
                                        href="../actions/toggle_user_status.php?id=<?= (int)$row['id']; ?>&status=blocked"
                                        class="block-btn"
                                        onclick="return confirm('Block this user?')"
                                    >
                                        <i class="fa-solid fa-ban"></i>
                                    </a>
                                <?php } else { ?>
                                    <a href="../actions/toggle_user_status.php?id=<?= (int)$row['id']; ?>&status=active" class="unblock-btn" onclick="return confirm('Unblock this user?')">
                                        <i class="fa-solid fa-unlock"></i>
                                    </a>
                                <?php } ?>
                                    <a
                                        href="../actions/delete_action.php?id=<?= (int)$row['id']; ?>&btn=quigly_table"
                                        class="delete-btn"
                                        onclick="return confirm('Delete this user?')"                                   >
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php $offset++; ?>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="fa-solid fa-users-slash"></i>
                                    <h5 class="fw-bold mb-2">No users found</h5>
                                    <p class="mb-0">There are no registered users to display right now.</p>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <?php if ($totalPages > 1) { ?>
            <div class="users-pagination">
                <nav>
                    <a class="pagination-btn <?= ($page <= 1) ? 'disabled' : ''; ?>" href="?page=<?= $page - 1; ?>">
                        <i class="fa-solid fa-angle-left"></i>
                    </a>

                    <?php for ($p = 1; $p <= $totalPages; $p++) { ?>
                        <a class="pagination-number <?= ($p == $page) ? 'active' : ''; ?>" href="?page=<?= $p; ?>">
                            <?= $p; ?>
                        </a>
                    <?php } ?>

                    <a class="pagination-btn <?= ($page >= $totalPages) ? 'disabled' : ''; ?>" href="?page=<?= $page + 1; ?>">
                        <i class="fa-solid fa-angle-right"></i>
                    </a>
                </nav>
            </div>
        <?php } ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('collapsed');
}
</script>