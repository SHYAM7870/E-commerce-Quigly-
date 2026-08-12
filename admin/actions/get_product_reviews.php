<?php
// admin/actions/get_product_reviews.php
// Returns JSON: { avg_rating, review_count, reviews_html }
// First review is shown; additional reviews are hidden behind "View All Reviews" button.

include_once __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

if ($product_id <= 0) {
    echo json_encode([
        'avg_rating'   => 0,
        'review_count' => 0,
        'reviews_html' => '<div class="reviews-empty"><i class="far fa-comment-dots" style="font-size:28px;margin-bottom:8px;display:block;"></i>No reviews yet — be the first to review!</div>'
    ]);
    exit;
}

$avgStmt = $conn->prepare("
    SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews
    FROM reviews WHERE product_id = ? AND status = 'approved'
");
$avgStmt->bind_param("i", $product_id);
$avgStmt->execute();
$avgRow = $avgStmt->get_result()->fetch_assoc();
$avgStmt->close();

$avgRating   = (float)($avgRow['avg_rating']    ?? 0);
$reviewCount = (int)  ($avgRow['total_reviews'] ?? 0);

$breakdownStmt = $conn->prepare("
    SELECT rating, COUNT(*) AS cnt FROM reviews
    WHERE product_id = ? AND status = 'approved'
    GROUP BY rating ORDER BY rating DESC
");
$breakdownStmt->bind_param("i", $product_id);
$breakdownStmt->execute();
$breakdownResult = $breakdownStmt->get_result();
$breakdown = [5=>0,4=>0,3=>0,2=>0,1=>0];
while ($br = $breakdownResult->fetch_assoc()) $breakdown[(int)$br['rating']] = (int)$br['cnt'];
$breakdownStmt->close();

$revStmt = $conn->prepare("
    SELECT r.rating, r.review_text, r.created_at, u.name AS customer_name
    FROM reviews r
    LEFT JOIN quigly_table u ON u.id = r.user_id
    WHERE r.product_id = ? AND r.status = 'approved'
    ORDER BY r.id DESC
");
$revStmt->bind_param("i", $product_id);
$revStmt->execute();
$revResult = $revStmt->get_result();
$revStmt->close();

ob_start();

if ($revResult && $revResult->num_rows > 0) {
    // Rating breakdown bars
    if ($reviewCount > 0) {
        echo '<div class="review-breakdown mb-3">';
        foreach ([5,4,3,2,1] as $star) {
            $cnt = $breakdown[$star];
            $pct = $reviewCount > 0 ? round(($cnt / $reviewCount) * 100) : 0;
            echo '<div class="rb-row">';
            echo   '<span class="rb-label">' . $star . ' <i class="fas fa-star"></i></span>';
            echo   '<div class="rb-bar"><div class="rb-fill" style="width:' . $pct . '%"></div></div>';
            echo   '<span class="rb-count">' . $cnt . '</span>';
            echo '</div>';
        }
        echo '</div>';
    }

    $rowNum = 0;
    while ($row = $revResult->fetch_assoc()) {
        $rowNum++;
        $rating   = (int)$row['rating'];
        $stars    = '';
        for ($i = 1; $i <= 5; $i++) {
            $stars .= $i <= $rating
                ? '<i class="fas fa-star"></i>'
                : '<i class="far fa-star"></i>';
        }
        $initials = mb_strtoupper(mb_substr($row['customer_name'] ?: 'C', 0, 1));
        // All reviews beyond the first get the "review-collapsed" class
        $cls = $rowNum > 1 ? ' review-collapsed' : '';
        ?>
        <div class="review-card<?= $cls ?>">
            <div class="review-card-top">
                <div style="display:flex;align-items:center;gap:10px;">
                    <div class="review-avatar"><?= htmlspecialchars($initials) ?></div>
                    <div>
                        <div class="review-user"><?= htmlspecialchars($row['customer_name'] ?: 'Customer') ?></div>
                        <div class="review-date"><?= !empty($row['created_at']) ? date("d M Y", strtotime($row['created_at'])) : '' ?></div>
                    </div>
                </div>
                <div class="review-rating"><?= $stars ?></div>
            </div>
            <p class="review-text"><?= htmlspecialchars($row['review_text']) ?></p>
        </div>
        <?php
    }
} else {
    echo '<div class="reviews-empty"><i class="far fa-comment-dots" style="font-size:28px;margin-bottom:8px;display:block;"></i>No approved reviews yet.</div>';
}

$reviewsHtml = ob_get_clean();

echo json_encode([
    'avg_rating'   => $avgRating,
    'review_count' => $reviewCount,
    'reviews_html' => $reviewsHtml
]);
?>
