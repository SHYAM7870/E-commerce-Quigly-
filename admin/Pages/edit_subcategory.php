<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<?php
$id = $_GET['id'];

$query = "SELECT * FROM subcategories WHERE id='$id'";
$result = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($result);
?>

<div class="container-fluid">

    <h4 class="mb-4 mt-3 fw-bold text-primary border-start border-4 ps-3">
        Edit Subcategory
    </h4>

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <form action="../actions/update_subcategory.php" method="POST">

                <input type="hidden" name="id" value="<?= $data['id']; ?>">

                <div class="mb-3">
                    <label>Select Category</label>
                    <select name="category_id" class="form-control">

                        <?php
                        $cat = mysqli_query($conn, "SELECT * FROM categories");

                        while($c = mysqli_fetch_assoc($cat)){
                            $selected = ($c['id'] == $data['category_id']) ? "selected" : "";
                            echo "<option value='{$c['id']}' $selected>{$c['name']}</option>";
                        }
                        ?>

                    </select>
                </div>

                <div class="mb-3">
                    <label>Subcategory Name</label>
                    <input type="text" name="name" value="<?= $data['name']; ?>" class="form-control">
                </div>

                <button class="btn btn-success">Update Subcategory</button>

            </form>

        </div>
    </div>

</div>

<?php include("../includes/footer.php"); ?>