<?php
// admin/actions/cart_action.php
// Handles: fetch, add, update, remove, clear, sync
// All responses are JSON

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['email'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

include_once __DIR__ . '/../includes/db.php';

$email = trim($_SESSION['email']);
$stmt  = $conn->prepare("SELECT id FROM quigly_table WHERE email = ? LIMIT 1");
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

// ─── Helper: build full cart rows ─────────────────────────────────────────
function fetchCartRows($conn, $user_id) {
    $res = $conn->query("
        SELECT p.id, p.name, p.price, p.original_price, p.image,
               p.description, c.name AS category, ci.quantity,
               ci.variant_id, ci.selected_size, ci.selected_color
        FROM cart_items ci
        INNER JOIN products p  ON p.id = ci.product_id
        LEFT JOIN  categories c ON c.id = p.category_id
        WHERE ci.user_id = $user_id
        ORDER BY ci.created_at ASC
    ");
    $items = [];
    while ($r = $res->fetch_assoc()) {
        $img = trim($r['image'] ?? '');
        if ($img !== '' && !preg_match('#^(https?://|upload/)#i', $img)) {
            $img = 'upload/' . $img;
        }
        $r['image']         = $img ?: 'assets/images/no-image.png';
        $r['originalPrice'] = $r['original_price'];
        // Compose a unique cart key matching JS logic
        $key = (string)$r['id'];
        if ($r['variant_id'])     $key .= '_v' . $r['variant_id'];
        if ($r['selected_size'])  $key .= '_s' . $r['selected_size'];
        if ($r['selected_color']) $key .= '_c' . $r['selected_color'];
        $r['_key'] = $key;
        $items[] = $r;
    }
    return $items;
}

// ── FETCH ─────────────────────────────────────────────────────────────────
if ($action === 'fetch') {
    echo json_encode(['status' => 'ok', 'items' => fetchCartRows($conn, $user_id)]);
    exit;
}

// ── ADD or INCREMENT ──────────────────────────────────────────────────────
if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $qty        = max(1, (int)($_POST['quantity'] ?? 1));
    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }

    // Verify product exists
    $pchk = $conn->prepare("SELECT id FROM products WHERE id=? LIMIT 1");
    $pchk->bind_param("i", $product_id);
    $pchk->execute();
    if ($pchk->get_result()->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Product not found']);
        exit;
    }
    $pchk->close();

    // INSERT or increment quantity
    $ins = $conn->prepare("
        INSERT INTO cart_items (user_id, product_id, quantity)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)
    ");
    $ins->bind_param("iii", $user_id, $product_id, $qty);
    $ins->execute();
    $ins->close();

    echo json_encode(['status' => 'ok', 'items' => fetchCartRows($conn, $user_id)]);
    exit;
}

// ── UPDATE QTY ────────────────────────────────────────────────────────────
if ($action === 'update') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $qty        = (int)($_POST['quantity'] ?? 0);

    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }

    if ($qty <= 0) {
        // Remove
        $del = $conn->prepare("DELETE FROM cart_items WHERE user_id=? AND product_id=?");
        $del->bind_param("ii", $user_id, $product_id);
        $del->execute();
        $del->close();
    } else {
        $upd = $conn->prepare("
            UPDATE cart_items SET quantity=? WHERE user_id=? AND product_id=?
        ");
        $upd->bind_param("iii", $qty, $user_id, $product_id);
        $upd->execute();
        $upd->close();
    }

    echo json_encode(['status' => 'ok', 'items' => fetchCartRows($conn, $user_id)]);
    exit;
}

// ── REMOVE ────────────────────────────────────────────────────────────────
if ($action === 'remove') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid product']);
        exit;
    }
    $del = $conn->prepare("DELETE FROM cart_items WHERE user_id=? AND product_id=?");
    $del->bind_param("ii", $user_id, $product_id);
    $del->execute();
    $del->close();

    echo json_encode(['status' => 'ok', 'items' => fetchCartRows($conn, $user_id)]);
    exit;
}

// ── CLEAR ─────────────────────────────────────────────────────────────────
if ($action === 'clear') {
    $conn->query("DELETE FROM cart_items WHERE user_id = $user_id");
    echo json_encode(['status' => 'ok', 'items' => []]);
    exit;
}

// ── SYNC (bulk replace from client) ───────────────────────────────────────
// Accepts JSON body: {"items":[{"product_id":1,"quantity":2,"variant_id":null,"selected_size":"M","selected_color":"Red"},...]
if ($action === 'sync') {
    $body  = file_get_contents('php://input');
    $data  = json_decode($body, true);
    $items = $data['items'] ?? [];

    $conn->query("DELETE FROM cart_items WHERE user_id = $user_id");

    if (!empty($items) && is_array($items)) {
        // Ensure columns exist (safe upgrade for existing DBs)
        @$conn->query("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS variant_id int(11) DEFAULT NULL");
        @$conn->query("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_size varchar(50) DEFAULT NULL");
        @$conn->query("ALTER TABLE cart_items ADD COLUMN IF NOT EXISTS selected_color varchar(50) DEFAULT NULL");

        $ins = $conn->prepare("
            INSERT IGNORE INTO cart_items (user_id, product_id, quantity, variant_id, selected_size, selected_color)
            VALUES (?,?,?,?,?,?)
        ");
        foreach ($items as $it) {
            $pid   = (int)($it['product_id'] ?? 0);
            $qty   = max(1, (int)($it['quantity'] ?? 1));
            $vid   = isset($it['variant_id'])     && $it['variant_id']     !== null ? (int)$it['variant_id']     : null;
            $sname = isset($it['selected_size'])  && $it['selected_size']  !== ''   ? substr(trim($it['selected_size']),  0, 50) : null;
            $cname = isset($it['selected_color']) && $it['selected_color'] !== ''   ? substr(trim($it['selected_color']), 0, 50) : null;
            if ($pid > 0) {
                $ins->bind_param("iiisss", $user_id, $pid, $qty, $vid, $sname, $cname);
                $ins->execute();
            }
        }
        $ins->close();
    }

    echo json_encode(['status' => 'ok', 'items' => fetchCartRows($conn, $user_id)]);
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Unknown action']);
?>
