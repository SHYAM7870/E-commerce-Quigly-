<?php
include 'db.php';
session_start();

$type = $_GET['type'] ?? 'banner';
$allowed_types = ['banner', 'brand', 'logo'];

if (!in_array($type, $allowed_types)) {
    $type = 'banner';
}

$sql = "SELECT * FROM homepage_media WHERE type = '$type' ORDER BY sort_order ASC, id DESC";
$result = mysqli_query($conn, $sql);
?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0 text-dark">
            <?= ucfirst($type) ?> Manage
        </h2>

        <a href="admin_homepage_add.php?type=<?= $type ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New <?= ucfirst($type) ?>
        </a>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-body p-2">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link <?= ($type == 'banner') ? 'active' : '' ?>" href="admin_homepage.php?type=banner">
                        Banner
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($type == 'brand') ? 'active' : '' ?>" href="admin_homepage.php?type=brand">
                        Brands
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($type == 'logo') ? 'active' : '' ?>" href="admin_homepage.php?type=logo">
                        Logo
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="thead-light">
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Title</th>
                            <th>Link</th>
                            <th>Status</th>
                            <th>Order</th>
                            <th width="180">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($result) > 0): ?>
                        <?php while ($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $row['id'] ?></td>
                                <td>
                                    <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" 
                                         style="width:80px;height:50px;object-fit:cover;border-radius:8px;">
                                </td>
                                <td><?= htmlspecialchars($row['title'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($row['link'] ?? '-') ?></td>
                                <td>
                                    <?= $row['status'] == 1 ? '<span class="badge badge-success">Active</span>' : '<span class="badge badge-danger">Inactive</span>' ?>
                                </td>
                                <td><?= $row['sort_order'] ?></td>
                                <td>
                                    <a href="admin_homepage_edit.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-info">Edit</a>
                                    <a href="admin_homepage_delete.php?id=<?= $row['id'] ?>&type=<?= $type ?>" 
                                       class="btn btn-sm btn-danger"
                                       onclick="return confirm('Delete this item?')">Delete</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">No data found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>