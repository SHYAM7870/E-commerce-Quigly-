<?php include("../includes/db.php"); ?>
<?php include("../includes/header.php"); ?>
<?php include("../includes/sidebar.php"); ?>

<div class="container-fluid">

    <!-- PAGE HEADER -->
    <div class="d-flex justify-content-between align-items-center mb-4 mt-3">
        <h4 class="fw-bold text-primary border-start border-4 ps-3">
            Add Category
        </h4>
    </div>

    <!-- MESSAGE ALERT -->
    <?php
    if(isset($_GET['msg'])){
        if($_GET['msg'] == 'success'){
            echo "<div class='alert alert-success'>Category Added Successfully ✔</div>";
        } elseif($_GET['msg'] == 'error'){
            echo "<div class='alert alert-danger'>Something went wrong ❌</div>";
        }
    }
    ?>

    <!-- CARD FORM -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-4">

            <form action="../actions/category_action.php" method="POST" enctype="multipart/form-data">

                <!-- CATEGORY NAME -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Name</label>
                    <input 
                        type="text" 
                        name="name" 
                        class="form-control" 
                        placeholder="Enter category name"
                        required
                    >
                </div>

                <!-- IMAGE -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">Category Image</label>
                    <input 
                        type="file" 
                        name="image" 
                        class="form-control"
                        id="imageInput"
                        required
                    >
                </div>

                <!-- IMAGE PREVIEW -->
                <div class="mb-3 text-center">
                    <img id="preview" 
                         src="" 
                         class="img-fluid rounded border d-none"
                         style="max-height:150px;">
                </div>

                <!-- BUTTON -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-plus"></i> Add Category
                    </button>
                </div>

            </form>

        </div>
    </div>

</div>

<?php include("../includes/footer.php"); ?>

<!-- IMAGE PREVIEW SCRIPT -->
<script>
document.getElementById("imageInput").addEventListener("change", function(e){
    const file = e.target.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            const preview = document.getElementById("preview");
            preview.src = e.target.result;
            preview.classList.remove("d-none");
        }
        reader.readAsDataURL(file);
    }
});
</script>