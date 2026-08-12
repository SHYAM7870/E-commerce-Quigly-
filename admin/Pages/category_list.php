<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid">

    <!-- HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h4 class="fw-bold text-primary border-start border-4 ps-3">
            Category List
        </h4>
        <a href="add_category.php" class="btn btn-primary btn-sm">
            + Add Category
        </a>
    </div>

    <!-- MESSAGE -->
    <?php
    if(isset($_GET['msg'])){
        if($_GET['msg'] == 'delete'){
            echo "<div class='alert alert-success'>Category Deleted ✔</div>";
        }
    }
    ?>

    <?php
    // pagination
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    // total records
    $countQuery = "SELECT COUNT(*) AS total FROM categories";
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    // main query with limit
    $query = "SELECT * FROM categories ORDER BY id DESC LIMIT $limit OFFSET $offset";
    $result = mysqli_query($conn, $query);
    ?>

    <!-- CARD -->
    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">

                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php
                        $i = $offset + 1;

                        if(mysqli_num_rows($result) > 0){
                            while($row = mysqli_fetch_assoc($result)){
                        ?>
                        <tr>

                            <td><?= $i++ ?></td>

                            <td>
                                <img 
                                    src="../../upload/<?= $row['image']; ?>" 
                                    class="rounded border"
                                    style="height:60px; width:60px; object-fit:cover;"
                                >
                            </td>

                            <td class="fw-semibold">
                                <?= $row['name']; ?>
                            </td>

                            <td class="text-muted">
                                <?= date("d M Y", strtotime($row['created_at'] ?? 'now')); ?>
                            </td>

                            <td>
                                <a href="edit_category.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <a href="../actions/delete_category.php?id=<?= $row['id']; ?>" 
                                   class="btn btn-sm btn-danger"
                                   onclick="return confirm('Delete this category?')">
                                   <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>

                        </tr>
                        <?php
                            }
                        } else {
                        ?>
                        <tr>
                            <td colspan="5" class="text-danger py-4">
                                No categories found
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>

                </table>
            </div>

            <!-- PAGINATION -->
            <?php if($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center mb-0">
                    
                    <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page - 1 ?>"><i class="fas fa-chevron-left"></i></a>
                    </li>

                    <?php for($p = 1; $p <= $totalPages; $p++): ?>
                        <li class="page-item <?= ($p == $page) ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $p ?>"><?= $p ?></a>
                        </li>
                    <?php endfor; ?>

                    <li class="page-item <?= ($page >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link" href="?page=<?= $page + 1 ?>"><i class="fas fa-chevron-right"></i></a>
                    </li>

                </ul>
            </nav>
            <?php endif; ?>

        </div>
    </div>

</div>

<?php include("../includes/footer.php"); ?>