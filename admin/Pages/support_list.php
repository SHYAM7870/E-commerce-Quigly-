<?php
include_once __DIR__ . '/../includes/db.php';

if (isset($_GET['close'])) {
    $id = (int) $_GET['close'];

    mysqli_query(
        $conn,
        "UPDATE support_tickets
     SET status='Closed'
     WHERE id='$id'
     AND LOWER(status)!='closed'"
    );
    $backStatus = $_GET['status'] ?? 'all';
    $backPage = (int) ($_GET['page'] ?? 1);

    header(
        "Location: support_list.php?status=" .
        urlencode($backStatus) .
        "&page=" . $backPage
    );
    exit;
}

include_once __DIR__ . '/../includes/header.php';
include_once __DIR__ . '/../includes/sidebar.php';

$page = max(1, (int) ($_GET['page'] ?? 1));
$limit = 10;
$offset = ($page - 1) * $limit;

$statusFilter = isset($_GET['status']) ? trim($_GET['status']) : 'all';
$allowed = ['all', 'pending', 'replied', 'closed'];
if (!in_array($statusFilter, $allowed)) {
    $statusFilter = 'all';
}

$where = "WHERE 1=1";
if ($statusFilter !== 'all') {
    $statusSafe = mysqli_real_escape_string($conn, $statusFilter);
    $where .= " AND LOWER(status) = '$statusSafe'";
}

function countTickets ($conn, $cond)
{
    $q = mysqli_query($conn, "SELECT COUNT(*) AS c FROM support_tickets $cond");
    $r = mysqli_fetch_assoc($q);
    return (int) ($r['c'] ?? 0);
}

$totalTickets = countTickets($conn, $where);
$totalPages = max(1, (int) ceil($totalTickets / $limit));

$stats = [
    'all' => countTickets($conn, "WHERE 1=1"),
    'pending' => countTickets($conn, "WHERE LOWER(status)='pending'"),
    'replied' => countTickets($conn, "WHERE LOWER(status)='replied'"),
    'closed' => countTickets($conn, "WHERE LOWER(status)='closed'"),
];

$tickets = mysqli_query($conn, "
    SELECT id, user_email, subject, message, status, created_at
    FROM support_tickets
    $where
    ORDER BY id DESC
    LIMIT $limit OFFSET $offset
");

function statusBadge ($status)
{
    $s = strtolower(trim($status));
    if ($s === 'replied')
        return ['class' => 'bg-success', 'label' => 'Replied'];
    if ($s === 'closed')
        return ['class' => 'bg-secondary', 'label' => 'Closed'];
    return ['class' => 'bg-warning text-dark', 'label' => 'Pending'];
}
?>

<style>
    .support-page {
        padding: 24px 28px 40px;
    }

    .support-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        margin-bottom: 18px;
    }

    .support-topbar h4 {
        margin: 0;
        font-weight: 900;
        color: #0f172a;
    }

    .support-topbar p {
        margin: 3px 0 0;
        color: #64748b;
        font-size: 13px;
    }

    .support-stats {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 14px;
        margin-bottom: 18px;
    }

    .support-stat {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 18px;
        padding: 18px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .04);
    }

    .support-stat .num {
        font-size: 2rem;
        font-weight: 900;
        line-height: 1;
        margin-bottom: 4px;
        color: #0f172a;
    }

    .support-stat .label {
        font-size: .82rem;
        font-weight: 700;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: .6px;
    }

    .support-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 20px;
        box-shadow: 0 4px 14px rgba(0, 0, 0, .04);
        overflow: hidden;
    }

    .support-table thead th {
        background: #f8fafc;
        color: #334155;
        font-size: 13px;
        font-weight: 800;
        border-bottom: 1px solid #e5e7eb !important;
    }

    .support-table td {
        vertical-align: top;
        font-size: 14px;
        color: #334155;
    }

    .msg-preview {
        max-width: 420px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>

<div class="support-page">
    <div class="support-topbar">
        <div>
            <h4>Contact Support Requests</h4>
            <p>All messages submitted from the support form appear here.</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <a href="?status=all"
                class="btn btn-sm <?= $statusFilter === 'all' ? 'btn-primary' : 'btn-outline-primary' ?>">All
                (<?= $stats['all'] ?>)</a>
            <a href="?status=pending"
                class="btn btn-sm <?= $statusFilter === 'pending' ? 'btn-primary' : 'btn-outline-primary' ?>">Pending
                (<?= $stats['pending'] ?>)</a>
            <a href="?status=replied"
                class="btn btn-sm <?= $statusFilter === 'replied' ? 'btn-primary' : 'btn-outline-primary' ?>">Replied
                (<?= $stats['replied'] ?>)</a>
            <a href="?status=closed"
                class="btn btn-sm <?= $statusFilter === 'closed' ? 'btn-primary' : 'btn-outline-primary' ?>">Closed
                (<?= $stats['closed'] ?>)</a>
        </div>
    </div>

    <div class="support-stats">
        <div class="support-stat">
            <div class="num"><?= $stats['all'] ?></div>
            <div class="label">Total Requests</div>
        </div>
        <div class="support-stat">
            <div class="num"><?= $stats['pending'] ?></div>
            <div class="label">Pending</div>
        </div>
        <div class="support-stat">
            <div class="num"><?= $stats['replied'] ?></div>
            <div class="label">Replied</div>
        </div>
        <div class="support-stat">
            <div class="num"><?= $stats['closed'] ?></div>
            <div class="label">Closed</div>
        </div>
    </div>

    <div class="support-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 support-table">
                <thead>
                    <tr>
                        <th style="width:70px;">ID</th>
                        <th style="width:220px;">Email</th>
                        <th style="width:220px;">Subject</th>
                        <th>Message</th>
                        <th style="width:120px;">Status</th>
                        <th style="width:160px;">Action</th>
                        <th style="width:160px;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($tickets && mysqli_num_rows($tickets) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($tickets)): ?>
                            <?php $badge = statusBadge($row['status']); ?>
                            <tr>
                                <td>#<?= (int) $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['user_email']) ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($row['subject']) ?></td>
                                <td class="msg-preview"><?= htmlspecialchars($row['message']) ?></td>
                                <td><span class="badge <?= $badge['class'] ?>"><?= $badge['label'] ?></span></td>
                                <td>
                                    <?php if (strtolower(trim($row['status'])) !== 'closed'): ?>
                                        <a href="?close=<?= (int) $row['id'] ?>&status=<?= urlencode($statusFilter) ?>"
                                            class="btn btn-sm btn-success"
                                            onclick="return confirm('Mark this ticket as completed?')">
                                            Completed
                                        </a>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Completed</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                No support requests found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
            <ul class="pagination justify-content-center">
                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>&status=<?= urlencode($statusFilter) ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>