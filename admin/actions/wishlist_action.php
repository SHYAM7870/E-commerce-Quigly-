<?php
// admin/actions/wishlist_action.php
// Handles: toggle, fetch
// Called by JS via fetch() — always returns JSON

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

include_once __DIR__ . '/../includes/db.php';

$email = trim($_SESSION['email']);

$stmt = $conn->prepare("SELECT id FROM quigly_table WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'message' => 'User not found']);
    exit;
}

$user_id = (int)$row['id'];
$action  = trim($_POST['action'] ?? $_GET['action'] ?? '');

// ── FETCH: return all wishlisted product_ids + full product data ────────────
if ($action === 'fetch') {
    $res = $conn->query("
        SELECT p.id, p.name, p.price, p.original_price, p.image,
               p.description, c.name AS category
        FROM wishlists w
        INNER JOIN products p  ON p.id = w.product_id
        LEFT JOIN  categories c ON c.id = p.category_id
        WHERE w.user_id = $user_id
        ORDER BY w.created_at DESC
    ");

    $items = [];
    while ($r = $res->fetch_assoc()) {
        $img = trim($r['image'] ?? '');
        if ($img !== '' && !preg_match('#^(https?://|upload/)#i', $img)) {
            $img = 'upload/' . $img;
        }
        $r['image'] = $img ?: 'assets/images/no-image.png';
        $items[] = $r;
    }

    echo json_encode(['status' => 'ok', 'items' => $items]);
    exit;
}

// ── TOGGLE: add if not present, remove if present ──────────────────────────
if ($action === 'toggle') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }

    // Check current state
    $chk = $conn->prepare("SELECT id FROM wishlists WHERE user_id=? AND product_id=? LIMIT 1");
    $chk->bind_param("ii", $user_id, $product_id);
    $chk->execute();
    $exists = $chk->get_result()->num_rows > 0;
    $chk->close();

    if ($exists) {
        $del = $conn->prepare("DELETE FROM wishlists WHERE user_id=? AND product_id=?");
        $del->bind_param("ii", $user_id, $product_id);
        $del->execute();
        $del->close();
        echo json_encode(['status' => 'ok', 'wishlisted' => false]);
    } else {
        $ins = $conn->prepare("INSERT IGNORE INTO wishlists (user_id, product_id) VALUES (?,?)");
        $ins->bind_param("ii", $user_id, $product_id);
        $ins->execute();
        $ins->close();
        echo json_encode(['status' => 'ok', 'wishlisted' => true]);
    }
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
?>
