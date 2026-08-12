<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h4 class="fw-bold text-primary border-start border-4 ps-3">
            Add Subcategory
        </h4>
    </div>

    <?php
    if(isset($_GET['msg'])){
        if($_GET['msg'] == 'success'){
            echo "<div class='alert alert-success'>Subcategory Added ✔</div>";
        }
    }
    ?>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="../actions/subcategory_action.php" method="POST">

                <!-- CATEGORY SELECT -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Select Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">Select Category</option>
                        <?php
                        $cat = mysqli_query($conn, "SELECT * FROM categories");
                        while($row = mysqli_fetch_assoc($cat)){
                            echo "<option value='{$row['id']}'>{$row['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- SUBCATEGORY NAME -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Subcategory Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="text-end">
                    <button class="btn btn-success">Add Subcategory</button>
                </div>

            </form>

        </div>
    </div>

</div>

<?php include("../includes/footer.php"); ?>