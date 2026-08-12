<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h4 class="fw-bold text-primary border-start border-4 ps-3">
            Subcategory List
        </h4>
        <a href="add_subcategory.php" class="btn btn-primary btn-sm">
            + Add Subcategory
        </a>
    </div>

    <?php
    // pagination
    $limit = 5;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) {
        $page = 1;
    }

    $offset = ($page - 1) * $limit;

    // total records
    $countQuery = "SELECT COUNT(*) AS total FROM subcategories";
    $countResult = mysqli_query($conn, $countQuery);
    $countRow = mysqli_fetch_assoc($countResult);
    $totalRecords = $countRow['total'];
    $totalPages = ceil($totalRecords / $limit);

    // main query with join + limit
    $query = "
        SELECT s.*, c.name AS cat_name
        FROM subcategories s
        LEFT JOIN categories c ON s.category_id = c.id
        ORDER BY s.id DESC
        LIMIT $limit OFFSET $offset
    ";

    $result = mysqli_query($conn, $query);
    ?>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">
                <table class="table table-hover text-center">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>Subcategory</th>
                            <th>Category</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php
                    if(mysqli_num_rows($result) > 0){
                        $i = $offset + 1;
                        while($row = mysqli_fetch_assoc($result)){
                    ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $row['name'] ?></td>
                            <td><?= $row['cat_name'] ?></td>
                            <td>
                                <a href="edit_subcategory.php?id=<?= $row['id']; ?>" class="btn btn-warning btn-sm"><i class="fa-solid fa-pen"></i></a>
                                <a href="../actions/delete_subcategory.php?id=<?= $row['id'] ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Delete?')">
                                   <i class="fa-solid fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php
                        }
                    } else {
                    ?>
                        <tr>
                            <td colspan="4" class="text-danger py-4">No subcategories found</td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
            </div>

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