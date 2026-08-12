<?php
$gridTitle = $categoryGridTitle ?? 'Categories';
$gridSubtitle = $categoryGridSubtitle ?? '';
?>

<div class="container py-5">
    <h2 class="section-title center gradient-text text-center">
        <?php echo htmlspecialchars($gridTitle); ?>
    </h2>

    <?php if (!empty($gridSubtitle)) { ?>
        <p class="text-center mb-5 gradient-text">
            <?php echo htmlspecialchars($gridSubtitle); ?>
        </p>
    <?php } ?>

    <div class="row g-4 mb-5">
        <?php
        $cat_query = "SELECT id, name, image, details FROM categories ORDER BY id DESC";
        $cat_result = mysqli_query($conn, $cat_query);

        if ($cat_result && mysqli_num_rows($cat_result) > 0) {
            while ($cat = mysqli_fetch_assoc($cat_result)) {
        ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card category-card h-100 animate__animated animate__fadeInUp">
                        <img src="upload/<?php echo htmlspecialchars($cat['image']); ?>"
                             class="card-img-top"
                             style="height:180px; object-fit:cover;"
                             alt="<?php echo htmlspecialchars($cat['name']); ?>">

                        <div class="card-body text-center">
                            <h5 class="card-title"><?php echo htmlspecialchars($cat['name']); ?></h5>
                            <p class="card-text text-muted">
                                <?php echo htmlspecialchars($cat['details']); ?>
                            </p>

                            <a href="index.php?category_id=<?php echo (int)$cat['id']; ?>"
                               class="btn btn-quigly btn-sm">
                                Browse
                            </a>
                        </div>
                    </div>
                </div>
        <?php
            }
        } else {
            echo "<h4 class='text-center'>No Categories Found</h4>";
        }
        ?>
    </div>
</div>