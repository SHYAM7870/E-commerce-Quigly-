<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<?php
$id = $_GET['id'];

$query = "SELECT * FROM categories WHERE id='$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="container-fluid">

    <h4 class="mb-4 mt-3 fw-bold text-primary border-start border-4 ps-3">
        Edit Category
    </h4>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="../actions/update_category.php" method="POST" enctype="multipart/form-data">

                <input type="hidden" name="id" value="<?= $data['id']; ?>">

                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" value="<?= $data['name']; ?>" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Current Image</label><br>
                    <img src="../../upload/<?= $data['image']; ?>" width="80">
                </div>

                <div class="mb-3">
                    <label>Change Image (optional)</label>
                    <input type="file" name="image" class="form-control">
                </div>

                <button class="btn btn-success">Update Category</button>

            </form>

        </div>
    </div>

</div>

<?php include("../includes/footer.php"); ?>